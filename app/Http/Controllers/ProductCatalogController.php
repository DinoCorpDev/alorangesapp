<?php

namespace App\Http\Controllers;

use App\Http\Services\ProductCatalogPdfRenderer;
use App\Http\Services\ProductCatalogStore;
use App\Jobs\GenerateProductCatalogJob;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class ProductCatalogController extends Controller
{
    /** Rows returned per page by the product picker. */
    private const PRODUCTS_PER_REQUEST = 250;

    private const MAX_PRODUCTS_PER_REQUEST = 1000;

    /** Ids are sent to MySQL in batches so a huge selection never builds one giant query. */
    private const ID_QUERY_CHUNK = 5000;

    protected ProductCatalogStore $store;

    protected ProductCatalogPdfRenderer $renderer;

    public function __construct(ProductCatalogStore $store, ProductCatalogPdfRenderer $renderer)
    {
        $this->middleware(['permission:show_categories']);

        $this->store = $store;
        $this->renderer = $renderer;
    }

    public function index()
    {
        return $this->formView();
    }

    public function edit($catalog)
    {
        $catalog = $this->store->find($catalog);

        if (! $catalog) {
            flash(translate('Catalog was not found'))->error();
            return redirect()->route('product_catalogs.index');
        }

        return $this->formView($catalog);
    }

    public function configurationDefaults()
    {
        $catalog = null;
        $settings = $this->store->defaults();
        $sharedBlocks = $this->store->sharedBlocks();
        $categories = Category::orderBy('order_level')->orderBy('name')->get();
        $letters = array_merge(range('A', 'Z'), ['#']);
        $letterPalette = $this->renderer->letterPalette();
        $action = route('product_catalogs.configuration.defaults.update');
        $method = 'POST';

        return view('backend.product.catalogs.configuration', compact('catalog', 'settings', 'sharedBlocks', 'categories', 'letters', 'letterPalette', 'action', 'method'));
    }

    public function updateConfigurationDefaults(Request $request)
    {
        $settings = $this->validatedConfigurationSettings($request);
        unset($settings['cover_image']);
        $this->store->saveDefaults($settings);

        flash(translate('Catalog configuration updated successfully'))->success();
        return redirect()->route('product_catalogs.index');
    }

    public function configuration($catalog)
    {
        $catalog = $this->store->find($catalog);

        if (! $catalog) {
            flash(translate('Catalog was not found'))->error();
            return redirect()->route('product_catalogs.index');
        }

        $settings = array_merge($this->store->defaults(), $catalog['settings'] ?? []);
        $sharedBlocks = $this->store->sharedBlocks();
        $categories = Category::orderBy('order_level')->orderBy('name')->get();
        $letters = array_merge(range('A', 'Z'), ['#']);
        $letterPalette = $this->renderer->letterPalette();
        $action = route('product_catalogs.configuration.update', $catalog['id']);
        $method = 'PUT';

        return view('backend.product.catalogs.configuration', compact('catalog', 'settings', 'sharedBlocks', 'categories', 'letters', 'letterPalette', 'action', 'method'));
    }

    public function updateConfiguration(Request $request, $catalog)
    {
        $existing = $this->store->find($catalog);

        if (! $existing) {
            flash(translate('Catalog was not found'))->error();
            return redirect()->route('product_catalogs.index');
        }

        $settings = $this->validatedConfigurationSettings($request);
        $settings['cover_image'] = $existing['settings']['cover_image'] ?? null;
        $settings['cover_title_position'] = $existing['settings']['cover_title_position'] ?? 'middle';
        $settings['advisor_name'] = $existing['settings']['advisor_name'] ?? '';
        $settings['advisor_phone'] = $existing['settings']['advisor_phone'] ?? '';
        $settings['advisor_email_1'] = $existing['settings']['advisor_email_1'] ?? '';
        $settings['advisor_email_2'] = $existing['settings']['advisor_email_2'] ?? '';
        $settings['final_page_image'] = $existing['settings']['final_page_image'] ?? ($settings['final_page_image'] ?? null);
        $settings['final_page_blank'] = $existing['settings']['final_page_blank'] ?? ($settings['final_page_blank'] ?? false);
        $settings['advertising_items'] = $this->catalogAdvertisingItems($existing['settings'] ?? []);
        $settings['letter_intro_ads'] = $this->catalogLetterIntroAds($existing['settings'] ?? []);
        $settings['products_per_page'] = (int) ($existing['settings']['products_per_page'] ?? 12) === 20 ? 20 : 12;
        $settings['diagnostic_filler_blocks'] = $existing['settings']['diagnostic_filler_blocks'] ?? [];
        $settings['filler_mode'] = ! empty($settings['diagnostic_filler_blocks']) ? 'diagnostic' : 'off';

        $this->store->update($catalog, [
            'settings' => $settings,
            'updated_at' => now()->format('Y-m-d H:i:s'),
        ]);

        $this->queueGeneration($catalog, translate('Catalog configuration updated successfully') . ' ');

        return redirect()->route('product_catalogs.index');
    }

    /**
     * Product picker data.
     *
     * Paginated on the server: a category can hold thousands of products and returning them
     * all at once produced a payload and a DOM the browser could not work with. mode=ids
     * returns only the matching ids, which is what "select everything" needs.
     */
    public function categoryProducts(Request $request)
    {
        $request->validate([
            'category_ids' => 'required|array|min:1',
            'category_ids.*' => 'integer|exists:categories,id',
            'search' => 'nullable|string|max:255',
            'page' => 'nullable|integer|min:1',
            'per_page' => 'nullable|integer|min:1|max:' . self::MAX_PRODUCTS_PER_REQUEST,
            'mode' => 'nullable|in:rows,ids',
        ]);

        $categoryIds = array_values(array_unique(array_map('intval', $request->category_ids)));
        $search = trim((string) $request->input('search', ''));

        if ($request->input('mode') === 'ids') {
            return response()->json([
                'ids' => $this->productQuery($categoryIds, $search)
                    ->where('products.lowest_price', '>', 0)
                    ->pluck('products.id')
                    ->map(fn ($id) => (string) $id)
                    ->all(),
            ]);
        }

        $perPage = (int) ($request->input('per_page') ?: self::PRODUCTS_PER_REQUEST);
        $page = (int) ($request->input('page') ?: 1);

        $total = $this->productQuery($categoryIds, $search)->count();
        $selectableTotal = $this->productQuery($categoryIds, $search)
            ->where('products.lowest_price', '>', 0)
            ->count();
        $rows = $this->productQuery($categoryIds, $search)
            ->orderBy('categories.order_level')
            ->orderBy('categories.name')
            ->orderByRaw($this->productNameExpression())
            ->orderBy('products.id')
            ->forPage($page, $perPage)
            ->get([
                'products.id',
                'products.lowest_price',
                'categories.id as category_id',
                'categories.name as category_name',
                DB::raw($this->productNameExpression() . ' as product_name'),
            ]);

        return response()->json([
            'products' => $rows->map(function ($row) {
                $name = (string) $row->product_name;

                return [
                    'id' => (string) $row->id,
                    'name' => $name,
                    'price' => format_price($row->lowest_price),
                    'is_disabled' => (float) $row->lowest_price <= 0,
                    'category_id' => (string) $row->category_id,
                    'category_name' => (string) $row->category_name,
                    'letter' => $this->productLetter($name),
                ];
            })->values(),
            'page' => $page,
            'per_page' => $perPage,
            'total' => $total,
            'selectable_total' => $selectableTotal,
            'has_more' => ($page * $perPage) < $total,
            'letters_by_category' => $page === 1 ? $this->lettersByCategory($categoryIds) : null,
        ]);
    }

    public function store(Request $request)
    {
        $catalog = $this->buildCatalog($request);

        if (! $catalog) {
            return back()->withInput();
        }

        $this->store->put($catalog);
        $this->queueGeneration($catalog['id'], 'Catalogo creado. ');

        return redirect()->route('product_catalogs.index');
    }

    public function update(Request $request, $catalog)
    {
        $existing = $this->store->find($catalog);

        if (! $existing) {
            flash(translate('Catalog was not found'))->error();
            return redirect()->route('product_catalogs.index');
        }

        $updated = $this->buildCatalog($request, $existing);

        if (! $updated) {
            return back()->withInput();
        }

        $this->store->put($updated);
        $this->queueGeneration($catalog, 'Catalogo actualizado. ');

        return redirect()->route('product_catalogs.index');
    }

    /**
     * Current state of every catalog, polled by the listing while one is being generated.
     */
    public function statuses()
    {
        return response()->json(collect($this->store->all())->mapWithKeys(function ($catalog) {
            return [$catalog['id'] => [
                'status' => $catalog['status'],
                'status_message' => $catalog['status_message'],
                'products_count' => $catalog['products_count'],
                'generated_at' => $catalog['generated_at'],
                'updated_at' => $catalog['updated_at'],
                'has_file' => ! empty($catalog['file_path']) && is_file(public_path($catalog['file_path'])),
                'progress' => GenerateProductCatalogJob::progress($catalog['id']),
            ]];
        }));
    }

    public function regenerate($catalog)
    {
        $existing = $this->store->find($catalog);

        if (! $existing) {
            flash(translate('Catalog was not found'))->error();
            return back();
        }

        $this->queueGeneration($catalog, '');

        return redirect()->route('product_catalogs.index');
    }

    public function duplicate($catalog)
    {
        $existing = $this->store->find($catalog);

        if (! $existing) {
            flash(translate('Catalog was not found'))->error();
            return back();
        }

        $newId = (string) Str::uuid();
        $now = now()->format('Y-m-d H:i:s');
        $copy = $existing;
        $copy['id'] = $newId;
        $copy['name'] = 'Copia de ' . ($existing['name'] ?? translate('Catalog'));
        $copy['created_by'] = auth()->id();
        $copy['created_at'] = $now;
        $copy['updated_at'] = $now;
        $copy['status_message'] = null;

        // Make the PDF file independent too. This prevents editing, regenerating or
        // deleting the copy from affecting the original catalog file.
        $copy['file_path'] = null;
        $copy['generated_at'] = null;
        $copy['status'] = ProductCatalogStore::STATUS_QUEUED;

        $sourceRelativePath = $existing['file_path'] ?? null;
        if ($sourceRelativePath) {
            $sourcePath = public_path($sourceRelativePath);

            if (is_file($sourcePath)) {
                $extension = pathinfo($sourceRelativePath, PATHINFO_EXTENSION) ?: 'pdf';
                $directory = trim(str_replace('\\', '/', dirname($sourceRelativePath)), '/.');
                $directory = $directory !== '' ? $directory : 'uploads/catalogs';
                $targetRelativePath = $directory . '/copia-' . $newId . '.' . $extension;
                $targetPath = public_path($targetRelativePath);

                if (! is_dir(dirname($targetPath))) {
                    mkdir(dirname($targetPath), 0755, true);
                }

                if (@copy($sourcePath, $targetPath)) {
                    $copy['file_path'] = $targetRelativePath;
                    $copy['generated_at'] = $now;
                    $copy['status'] = ProductCatalogStore::STATUS_READY;
                }
            }
        }

        $this->store->put($copy);

        flash('Copia creada correctamente. Ya puedes editarla sin modificar el catalogo original.')->success();
        return redirect()->route('product_catalogs.edit', $newId);
    }

    public function download($catalog)
    {
        $catalog = $this->store->find($catalog);

        if (! $catalog) {
            flash(translate('Catalog was not found'))->error();
            return back();
        }

        if (empty($catalog['file_path'])) {
            flash($catalog['status'] === ProductCatalogStore::STATUS_FAILED
                ? 'La generacion del catalogo fallo: ' . $catalog['status_message']
                : 'El catalogo todavia se esta generando.')->error();
            return back();
        }

        $path = public_path($catalog['file_path']);

        if (! file_exists($path)) {
            flash(translate('Catalog file was not found'))->error();
            return back();
        }

        return response()->download($path, basename($catalog['file_path']));
    }

    public function destroy($catalog)
    {
        $catalogToDelete = $this->store->find($catalog);

        if (! $catalogToDelete) {
            flash(translate('Catalog was not found'))->error();
            return back();
        }

        if (! empty($catalogToDelete['file_path'])) {
            $path = public_path($catalogToDelete['file_path']);

            if (file_exists($path)) {
                unlink($path);
            }
        }

        $this->store->forget($catalog);

        flash(translate('Catalog deleted successfully'))->success();
        return redirect()->route('product_catalogs.index');
    }

    /**
     * Hands generation to the background worker. A failure to even start it is reported on
     * the catalog instead of bubbling up as a 500 that would lose the admin's selection.
     */
    protected function queueGeneration(string $catalogId, string $prefix): void
    {
        try {
            GenerateProductCatalogJob::trigger($catalogId);

            flash($prefix . 'El PDF se esta generando en segundo plano. La lista se actualiza sola cuando este listo.')->success();
        } catch (Throwable $e) {
            Log::error('Could not start catalog PDF generation', [
                'catalog_id' => $catalogId,
                'exception' => $e,
            ]);

            $this->store->update($catalogId, [
                'status' => ProductCatalogStore::STATUS_FAILED,
                'status_message' => Str::limit(trim($e->getMessage()) ?: get_class($e), 400),
            ]);

            flash('No se pudo iniciar la generacion del PDF: ' . $e->getMessage())->error();
        }
    }

    protected function formView($catalog = null)
    {
        $categories = Category::orderBy('order_level')->orderBy('name')->get();
        $catalogs = collect($this->store->all())->sortByDesc('created_at')->values();
        $sharedBlocks = $this->store->sharedBlocks();
        $defaultSettings = $this->store->defaults();
        $settings = array_merge($defaultSettings, $catalog['settings'] ?? []);

        if (empty($settings['cover_category_images']) && ! empty($defaultSettings['cover_category_images'])) {
            $settings['cover_category_images'] = $defaultSettings['cover_category_images'];
        }
        $mode = $catalog ? 'edit' : 'create';

        return view('backend.product.catalogs.index', compact('categories', 'catalogs', 'catalog', 'sharedBlocks', 'settings', 'mode'));
    }

    protected function buildCatalog(Request $request, $existing = null)
    {
        $request->validate([
            'category_ids' => 'required|array|min:1',
            'category_ids.*' => 'integer|exists:categories,id',
            // A comma separated list rather than product_ids[]: one input per product hits
            // PHP's max_input_vars (1000 by default), which silently drops everything past
            // the first thousand checkboxes without any error.
            'product_ids' => 'required|string',
            'name' => 'nullable|string|max:255',
            'cover_image' => 'nullable|string|max:255',
            'final_page_image' => 'nullable|string|max:255',
            'advertising_images' => 'nullable|array',
            'advertising_images.*' => 'nullable|string|max:255',
            'advertising_letters' => 'nullable|array',
            'advertising_letters.*' => 'nullable|string|max:1',
            'letter_intro_ad_images' => 'nullable|array',
            'letter_intro_ad_images.*' => 'nullable|string|max:255',
            'letter_intro_ad_category_ids' => 'nullable|array',
            'letter_intro_ad_category_ids.*' => 'nullable|integer|exists:categories,id',
            'letter_intro_ad_letters' => 'nullable|array',
            'letter_intro_ad_letters.*' => 'nullable|string|max:1',
            'letter_intro_ad_orders' => 'nullable|array',
            'letter_intro_ad_orders.*' => 'nullable|integer|in:1,2',
            'diagnostic_filler_images' => 'nullable|array',
            'diagnostic_filler_images.*' => 'nullable|string|max:255',
            'diagnostic_filler_category_ids' => 'nullable|array',
            'diagnostic_filler_category_ids.*' => 'nullable|integer|exists:categories,id',
            'diagnostic_filler_letters' => 'nullable|array',
            'diagnostic_filler_letters.*' => 'nullable|string|max:1',
            'diagnostic_filler_block_indexes' => 'nullable|array',
            'diagnostic_filler_block_indexes.*' => 'nullable|integer|min:0|max:99',
            'diagnostic_filler_xs' => 'nullable|array',
            'diagnostic_filler_xs.*' => 'nullable|integer|min:0|max:4',
            'diagnostic_filler_ys' => 'nullable|array',
            'diagnostic_filler_ys.*' => 'nullable|integer|min:0|max:5',
            'diagnostic_filler_widths' => 'nullable|array',
            'diagnostic_filler_widths.*' => 'nullable|integer|min:1|max:4',
            'diagnostic_filler_heights' => 'nullable|array',
            'diagnostic_filler_heights.*' => 'nullable|integer|min:1|max:5',
            'diagnostic_filler_spaces' => 'nullable|array',
            'diagnostic_filler_spaces.*' => 'nullable|integer|min:1|max:20',
            'diagnostic_filler_width_mms' => 'nullable|array',
            'diagnostic_filler_width_mms.*' => 'nullable|numeric|min:1|max:300',
            'diagnostic_filler_height_mms' => 'nullable|array',
            'diagnostic_filler_height_mms.*' => 'nullable|numeric|min:1|max:300',
            'diagnostic_filler_width_pixels' => 'nullable|array',
            'diagnostic_filler_width_pixels.*' => 'nullable|integer|min:1|max:10000',
            'diagnostic_filler_height_pixels' => 'nullable|array',
            'diagnostic_filler_height_pixels.*' => 'nullable|integer|min:1|max:10000',
            'diagnostic_filler_products_on_last_page' => 'nullable|array',
            'diagnostic_filler_products_on_last_page.*' => 'nullable|integer|min:0|max:20',
            'diagnostic_filler_capacities' => 'nullable|array',
            'diagnostic_filler_capacities.*' => 'nullable|integer|in:12,20',
            'diagnostic_filler_free_spaces' => 'nullable|array',
            'diagnostic_filler_free_spaces.*' => 'nullable|integer|min:1|max:20',
            'products_per_page' => 'required|integer|in:12,20',
        ]);

        $categoryIds = array_values(array_unique(array_map('intval', $request->category_ids)));
        $requestedIds = $this->parseIdList($request->input('product_ids'));

        if (empty($requestedIds)) {
            flash(translate('Select at least one product from the selected categories'))->error();
            return null;
        }

        // One query instead of an "exists" rule per id, which would have run thousands of
        // queries, and it doubles as the filter for products without a price.
        $productIds = $this->selectableProductIds($requestedIds, $categoryIds);

        if (empty($productIds)) {
            flash(translate('Select at least one product from the selected categories'))->error();
            return null;
        }

        $categoryNames = Category::whereIn('id', $categoryIds)
            ->orderBy('order_level')->orderBy('name')
            ->get()
            ->map(fn ($category) => $category->getTranslation('name'))
            ->values();

        // Los ajustes de Configuracion del catalogo (medios de pago, informacion, paginas
        // adicionales, tipografia, colores, etc.) siempre se toman frescos desde el default
        // global vigente, sin congelar una copia por catalogo — asi un cambio global aplica
        // de inmediato la proxima vez que se edite o regenere cualquier catalogo.
        $diagnosticFillerBlocks = $this->sanitizeDiagnosticFillerBlocks($request);

        $settings = array_merge($this->store->defaults(), [
            'cover_image' => $request->cover_image,
            'advertising_items' => $this->sanitizeAdvertisingItems($request),
            'letter_intro_ads' => $this->sanitizeLetterIntroAds($request),
            'filler_mode' => empty($diagnosticFillerBlocks) ? 'off' : 'diagnostic',
            'auto_fill_enabled' => false,
            'filler_ads' => [],
            'manual_filler_ads' => [],
            'diagnostic_filler_blocks' => $diagnosticFillerBlocks,
            'products_per_page' => (int) $request->products_per_page,
        ]);

        $settings['final_page_image'] = $request->final_page_image ?: ($existing['settings']['final_page_image'] ?? null);
        $settings['final_page_blank'] = $request->boolean('final_page_blank');

        $catalogName = $request->name ?: translate('Catalog') . ' - ' . $categoryNames->join(', ') . ' - ' . now()->format('Y-m-d H:i');

        return [
            'id' => $existing['id'] ?? (string) Str::uuid(),
            'name' => $catalogName,
            'category_name' => $categoryNames->join(', '),
            'category_ids' => $categoryIds,
            'category_names' => $categoryNames->all(),
            'product_ids' => $productIds,
            // Kept from the previous run so the old PDF stays downloadable while the new one
            // is being built; the job swaps it once the new file exists.
            'file_path' => $existing['file_path'] ?? null,
            'products_count' => count($productIds),
            'settings' => $settings,
            'status' => ProductCatalogStore::STATUS_QUEUED,
            'status_message' => null,
            'generated_at' => $existing['generated_at'] ?? null,
            'created_by' => $existing['created_by'] ?? auth()->id(),
            'created_at' => $existing['created_at'] ?? now()->format('Y-m-d H:i:s'),
            'updated_at' => now()->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * Calcula los espacios libres reales de la última página de cada letra.
     * Devuelve bloques rectangulares (columnas x filas) y las medidas exactas
     * para que la interfaz pueda descargar plantillas SVG a escala correcta.
     */
    public function fillerDiagnostics(Request $request)
    {
        $request->validate([
            'category_ids' => 'required|array|min:1',
            'category_ids.*' => 'integer|exists:categories,id',
            'product_ids' => 'required|string',
            'products_per_page' => 'required|integer|in:12,20',
            'advertising_letters' => 'nullable|array',
            'advertising_letters.*' => 'nullable|string|max:1',
        ]);

        $categoryIds = array_values(array_unique(array_map('intval', $request->category_ids)));
        $requestedIds = $this->parseIdList($request->product_ids);
        $productIds = $this->selectableProductIds($requestedIds, $categoryIds);
        $productsPerPage = (int) $request->products_per_page;
        $columns = $productsPerPage === 20 ? 4 : 3;
        $rows = (int) ($productsPerPage / $columns);
        $cardWidth = $productsPerPage === 20 ? 45.0 : 58.0;
        $gap = $productsPerPage === 20 ? 3.0 : 5.0;
        $rowHeight = $productsPerPage === 20 ? 48.0 : 62.0;
        $verticalTrim = $productsPerPage === 20 ? 2.5 : 7.0;
        $advertisingCounts = collect($request->input('advertising_letters', []))
            ->filter()->map(fn ($letter) => Str::upper((string) $letter))->countBy();
        $categories = Category::whereIn('id', $categoryIds)->orderBy('order_level')->orderBy('name')->get();
        $groups = $this->renderer->productsByCategory($categories, $productIds);
        $result = [];

        foreach ($groups as $group) {
            $category = $group['category'];
            foreach ($group['letter_groups'] as $letter => $products) {
                $remaining = $products->count();
                $adCount = (int) ($advertisingCounts[$letter] ?? 0);
                $adIndex = 0;
                $lastCount = 0;
                $lastHasAd = false;

                while ($remaining > 0) {
                    $hasAd = $adIndex < $adCount;
                    $capacity = $productsPerPage - ($hasAd ? 4 : 0);
                    $lastCount = min($remaining, $capacity);
                    $remaining -= $lastCount;
                    $lastHasAd = $hasAd;
                    if ($hasAd) $adIndex++;
                }

                $grid = array_fill(0, $rows, array_fill(0, $columns, false));
                if ($lastHasAd) {
                    // Publicidad interna ocupa las dos columnas derechas de las primeras dos filas.
                    for ($r = 0; $r < min(2, $rows); $r++) {
                        for ($c = max(0, $columns - 2); $c < $columns; $c++) $grid[$r][$c] = true;
                    }
                    $featuredColumns = max(0, $columns - 2);
                    $featuredCapacity = $featuredColumns * min(2, $rows);
                    $featured = min($lastCount, $featuredCapacity);
                    for ($i = 0; $i < $featured; $i++) {
                        $r = intdiv($i, max(1, $featuredColumns));
                        $c = $i % max(1, $featuredColumns);
                        $grid[$r][$c] = true;
                    }
                    $regular = $lastCount - $featured;
                    for ($i = 0; $i < $regular; $i++) {
                        $r = 2 + intdiv($i, $columns);
                        $c = $i % $columns;
                        if ($r < $rows) $grid[$r][$c] = true;
                    }
                } else {
                    for ($i = 0; $i < $lastCount; $i++) {
                        $r = intdiv($i, $columns);
                        $c = $i % $columns;
                        if ($r < $rows) $grid[$r][$c] = true;
                    }
                }

                $blocks = [];
                if (! $lastHasAd) {
                    $partial = $lastCount % $columns;
                    $usedRows = $lastCount ? (int) ceil($lastCount / $columns) : 0;
                    if ($partial > 0) {
                        $blocks[] = ['x' => $partial, 'y' => $usedRows - 1, 'width' => $columns - $partial, 'height' => 1];
                    }
                    $fullEmptyRows = $rows - $usedRows;
                    if ($fullEmptyRows > 0) {
                        $blocks[] = ['x' => 0, 'y' => $usedRows, 'width' => $columns, 'height' => $fullEmptyRows];
                    }
                } else {
                    // Descomposición rectangular genérica para páginas con publicidad interna.
                    $empty = [];
                    for ($r=0;$r<$rows;$r++) for($c=0;$c<$columns;$c++) if(!$grid[$r][$c]) $empty["$r:$c"] = true;
                    while ($empty) {
                        $best = null;
                        foreach ($empty as $key => $_) {
                            [$sr,$sc] = array_map('intval', explode(':',$key));
                            for ($h=1;$sr+$h<=$rows;$h++) {
                                for ($w=1;$sc+$w<=$columns;$w++) {
                                    $ok=true;
                                    for($rr=$sr;$rr<$sr+$h && $ok;$rr++) for($cc=$sc;$cc<$sc+$w;$cc++) if(!isset($empty["$rr:$cc"])){$ok=false;break;}
                                    if(!$ok) continue;
                                    $area=$w*$h;
                                    if(!$best || $area>$best['area'] || ($area===$best['area'] && $w>$best['width'])) $best=['x'=>$sc,'y'=>$sr,'width'=>$w,'height'=>$h,'area'=>$area];
                                }
                            }
                        }
                        if(!$best) break;
                        for($rr=$best['y'];$rr<$best['y']+$best['height'];$rr++) for($cc=$best['x'];$cc<$best['x']+$best['width'];$cc++) unset($empty["$rr:$cc"]);
                        unset($best['area']); $blocks[]=$best;
                    }
                }

                $blocks = collect($blocks)->map(function ($block) use ($cardWidth,$gap,$rowHeight,$verticalTrim) {
                    $widthMm = ($block['width'] * $cardWidth) + (($block['width'] - 1) * $gap);
                    $heightMm = ($block['height'] * $rowHeight) - $verticalTrim;
                    return array_merge($block, [
                        'spaces' => $block['width'] * $block['height'],
                        'width_mm' => round($widthMm, 1),
                        'height_mm' => round($heightMm, 1),
                        'width_px_300' => (int) round($widthMm / 25.4 * 300),
                        'height_px_300' => (int) round($heightMm / 25.4 * 300),
                    ]);
                })->values()->all();

                $free = collect($blocks)->sum('spaces');
                if ($free > 0) {
                    $result[] = [
                        'category_id' => (int) $category->id,
                        'category_name' => $category->getTranslation('name'),
                        'letter' => $letter,
                        'products_on_last_page' => $lastCount,
                        'capacity' => $productsPerPage,
                        'free_spaces' => $free,
                        'columns' => $columns,
                        'rows' => $rows,
                        'grid' => $grid,
                        'blocks' => $blocks,
                    ];
                }
            }
        }

        return response()->json(['diagnostics' => $result]);
    }

    /**
     * @return int[]
     */
    protected function parseIdList($raw): array
    {
        $parts = preg_split('/[^0-9]+/', (string) $raw, -1, PREG_SPLIT_NO_EMPTY) ?: [];

        return array_values(array_unique(array_filter(array_map('intval', $parts))));
    }

    /**
     * Ids from the submitted selection that actually belong to the chosen categories and
     * have a price, queried in batches so the selection size never caps out.
     *
     * @return int[]
     */
    protected function selectableProductIds(array $productIds, array $categoryIds): array
    {
        $found = [];

        foreach (array_chunk($productIds, self::ID_QUERY_CHUNK) as $chunk) {
            $found = array_merge($found, DB::table('product_categories')
                ->join('products', 'products.id', '=', 'product_categories.product_id')
                ->whereIn('product_categories.category_id', $categoryIds)
                ->whereIntegerInRaw('products.id', $chunk)
                ->where('products.lowest_price', '>', 0)
                ->distinct()
                ->pluck('products.id')
                ->all());
        }

        return array_values(array_unique(array_map('intval', $found)));
    }

    /**
     * Base picker query. Uses the translated name when there is one, matching what the PDF
     * prints, resolved in the query so ordering and paging can happen in the database.
     */
    protected function productQuery(array $categoryIds, string $search)
    {
        $query = DB::table('product_categories')
            ->join('products', 'products.id', '=', 'product_categories.product_id')
            ->join('categories', 'categories.id', '=', 'product_categories.category_id')
            ->leftJoin('product_translations', function ($join) {
                $join->on('product_translations.product_id', '=', 'products.id')
                    ->where('product_translations.lang', '=', App::getLocale());
            })
            ->whereIn('product_categories.category_id', $categoryIds);

        if ($search !== '') {
            $like = '%' . str_replace(['%', '_'], ['\%', '\_'], $search) . '%';

            $query->where(function ($where) use ($like, $search) {
                $where->whereRaw($this->productNameExpression() . ' like ?', [$like])
                    ->orWhere('products.reference', 'like', $like);

                if (ctype_digit($search)) {
                    $where->orWhere('products.id', (int) $search);
                }
            });
        }

        return $query;
    }

    protected function productNameExpression(): string
    {
        // Single quotes on purpose: with ANSI_QUOTES in sql_mode, "" would be read as an
        // identifier instead of an empty string.
        return "COALESCE(NULLIF(product_translations.name, ''), products.name)";
    }

    /**
     * Letters that actually have selectable products, per category. The letter separator
     * pages need this and it can no longer be derived from the rows on screen, since only a
     * page of them is loaded at a time.
     */
    protected function lettersByCategory(array $categoryIds): array
    {
        $rows = $this->productQuery($categoryIds, '')
            ->where('products.lowest_price', '>', 0)
            ->distinct()
            ->get([
                'categories.id as category_id',
                DB::raw('UPPER(SUBSTRING(TRIM(' . $this->productNameExpression() . '), 1, 1)) as letter'),
            ]);

        $letters = [];

        foreach ($rows as $row) {
            $letter = $this->productLetter((string) $row->letter);
            $letters[(string) $row->category_id][$letter] = true;
        }

        return array_map(function ($group) {
            $keys = array_keys($group);
            sort($keys);
            return $keys;
        }, $letters);
    }

    protected function productLetter(string $name): string
    {
        $letter = Str::upper(Str::substr(trim($name), 0, 1));

        return preg_match('/^[A-ZÑ0-9]$/u', $letter) ? $letter : '#';
    }

    protected function validatedConfigurationSettings(Request $request)
    {
        $request->validate([
            'payment_page_image' => 'nullable|string|max:255',
            'payment_bank_icon' => 'nullable|string|max:255',
            'payment_debit_icon' => 'nullable|string|max:255',
            'payment_credit_icon' => 'nullable|string|max:255',
            'payment_cash_icon' => 'nullable|string|max:255',
            'info_page_image' => 'nullable|string|max:255',
            'page_four_image' => 'nullable|string|max:255',
            'cover_category_ids' => 'nullable|array',
            'cover_category_ids.*' => 'nullable|integer|exists:categories,id',
            'cover_category_images' => 'nullable|array',
            'cover_category_images.*' => 'nullable|string|max:255',
            'final_page_image' => 'nullable|string|max:255',
            'payment_page_position' => 'nullable|in:start,end',
            'info_page_position' => 'nullable|in:start,end',
            'additional_page_images' => 'nullable|array',
            'additional_page_images.*' => 'nullable|string|max:255',
            'additional_page_positions' => 'nullable|array',
            'additional_page_positions.*' => 'nullable|in:start,end',
            'payment_title' => 'nullable|string|max:120',
            'payment_delivery_title' => 'nullable|string|max:160',
            'payment_bank_info' => 'nullable|string|max:1000',
            'payment_debit_title' => 'nullable|string|max:120',
            'payment_debit_info' => 'nullable|string|max:500',
            'payment_credit_title' => 'nullable|string|max:120',
            'payment_credit_info' => 'nullable|string|max:500',
            'payment_cash_title' => 'nullable|string|max:160',
            'payment_cash_info' => 'nullable|string|max:800',
            'info_page_title' => 'nullable|string|max:160',
            'info_table_labels' => 'nullable|array',
            'info_table_labels.*' => 'nullable|string|max:160',
            'info_table_values' => 'nullable|array',
            'info_table_values.*' => 'nullable|string|max:500',
            'description_limit' => 'nullable|integer|min:40|max:220',
            'product_title_font_family' => 'nullable|string|max:80',
            'product_title_font_size' => 'nullable|integer|min:8|max:28',
            'product_description_font_family' => 'nullable|string|max:80',
            'product_description_font_size' => 'nullable|integer|min:7|max:22',
            'product_price_font_family' => 'nullable|string|max:80',
            'product_price_font_size' => 'nullable|integer|min:8|max:30',
            'product_reference_font_family' => 'nullable|string|max:80',
            'product_reference_font_size' => 'nullable|integer|min:7|max:22',
            'product_box_colors' => 'nullable|array',
            'product_text_colors' => 'nullable|array',
            'show_alphabetic_navigator' => 'nullable|boolean',
            'standalone_letter_position' => 'nullable|in:left,right,alternate_outer',
        ]);

        return array_merge($this->store->defaultSettings(), [
            'show_prices' => $request->has('show_prices'),
            'show_payment_page' => $request->has('show_payment_page'),
            'show_info_page' => $request->has('show_info_page'),
            'show_page_four' => $request->has('show_page_four'),
            'show_alphabetic_navigator' => $request->has('show_alphabetic_navigator'),
            'standalone_letter_position' => in_array($request->standalone_letter_position, ['left', 'right', 'alternate_outer'], true) ? $request->standalone_letter_position : 'right',
            'description_limit' => (int) ($request->description_limit ?: 90),
            'payment_page_image' => $request->payment_page_image,
            'payment_bank_icon' => $request->payment_bank_icon,
            'payment_debit_icon' => $request->payment_debit_icon,
            'payment_credit_icon' => $request->payment_credit_icon,
            'payment_cash_icon' => $request->payment_cash_icon,
            'info_page_image' => $request->info_page_image,
            'page_four_image' => $request->page_four_image,
            'payment_title' => $request->payment_title,
            'payment_delivery_title' => $request->payment_delivery_title,
            'payment_bank_info' => $request->payment_bank_info,
            'payment_debit_title' => $request->payment_debit_title,
            'payment_debit_info' => $request->payment_debit_info,
            'payment_credit_title' => $request->payment_credit_title,
            'payment_credit_info' => $request->payment_credit_info,
            'payment_cash_title' => $request->payment_cash_title,
            'payment_cash_info' => $request->payment_cash_info,
            'info_page_title' => $request->info_page_title,
            'info_table_rows' => $this->sanitizeInfoTableRows($request),
            'cover_category_images' => $this->sanitizeCoverCategoryImages($request),
            'final_page_image' => $request->final_page_image,
            'payment_page_position' => $request->payment_page_position === 'end' ? 'end' : 'start',
            'info_page_position' => $request->info_page_position === 'end' ? 'end' : 'start',
            'additional_pages' => $this->sanitizeAdditionalPages($request),
            'product_title_font_family' => $this->sanitizeFontFamily($request->product_title_font_family),
            'product_title_font_size' => (int) ($request->product_title_font_size ?: 12),
            'product_description_font_family' => $this->sanitizeFontFamily($request->product_description_font_family),
            'product_description_font_size' => (int) ($request->product_description_font_size ?: 10),
            'product_price_font_family' => $this->sanitizeFontFamily($request->product_price_font_family),
            'product_price_font_size' => (int) ($request->product_price_font_size ?: 16),
            'product_reference_font_family' => $this->sanitizeFontFamily($request->product_reference_font_family),
            'product_reference_font_size' => (int) ($request->product_reference_font_size ?: 12),
            'product_box_colors' => $this->sanitizeColors($request->product_box_colors ?: []),
            'product_text_colors' => $this->sanitizeColors($request->product_text_colors ?: []),
        ]);
    }

    protected function sanitizeColors(array $colors)
    {
        $clean = [];

        foreach ($colors as $letter => $color) {
            $letter = Str::upper((string) $letter);
            $color = trim((string) $color);

            if (preg_match('/^#[0-9a-fA-F]{6}$/', $color)) {
                $clean[$letter] = $color;
            }
        }

        return $clean;
    }

    protected function sanitizeFontFamily($fontFamily)
    {
        // Solo fuentes con archivo TTF/OTF real dentro de mPDF (vendor/mpdf/mpdf/ttfonts) —
        // cualquier otro nombre ("Arial", "Georgia", etc.) no tiene archivo propio y mPDF lo
        // sustituye en silencio por una de estas mismas fuentes.
        $allowedFonts = [
            'DejaVu Sans',
            'DejaVu Sans Condensed',
            'DejaVu Serif',
            'DejaVu Serif Condensed',
            'DejaVu Sans Mono',
            'FreeSans',
            'FreeSerif',
            'FreeMono',
        ];

        return in_array($fontFamily, $allowedFonts, true) ? $fontFamily : 'DejaVu Sans';
    }

    protected function sanitizeInfoTableRows(Request $request)
    {
        $labels = $request->info_table_labels ?: [];
        $values = $request->info_table_values ?: [];
        $rows = [];

        foreach ($labels as $index => $label) {
            $label = trim((string) $label);
            $value = trim((string) ($values[$index] ?? ''));

            if ($label !== '' || $value !== '') {
                $rows[] = [
                    'label' => $label,
                    'value' => $value,
                ];
            }
        }

        return $rows;
    }

    protected function sanitizeAdvertisingItems(Request $request)
    {
        $images = $request->advertising_images ?: [];
        $letters = $request->advertising_letters ?: [];
        $allowedLetters = array_merge(range('A', 'Z'), ['#']);
        $items = [];

        foreach ($images as $index => $image) {
            $image = trim((string) $image);
            $letter = Str::upper(trim((string) ($letters[$index] ?? '')));

            if ($image === '' || ! in_array($letter, $allowedLetters, true)) {
                continue;
            }

            $items[] = [
                'image' => $image,
                'letter' => $letter,
            ];
        }

        return $items;
    }

    protected function sanitizeCoverCategoryImages(Request $request)
    {
        $categoryIds = $request->cover_category_ids ?: [];
        $images = $request->cover_category_images ?: [];
        $items = [];

        foreach ($categoryIds as $index => $categoryId) {
            $categoryId = (int) $categoryId;
            $image = trim((string) ($images[$index] ?? ''));

            if ($categoryId > 0 && $image !== '') {
                $items[] = [
                    'category_id' => $categoryId,
                    'image' => $image,
                ];
            }
        }

        return $items;
    }

    protected function sanitizeAdditionalPages(Request $request)
    {
        $images = $request->additional_page_images ?: [];
        $positions = $request->additional_page_positions ?: [];
        $allowedPositions = ['start', 'end'];
        $items = [];

        foreach ($images as $index => $image) {
            $image = trim((string) $image);

            if ($image === '') {
                continue;
            }

            $position = $positions[$index] ?? 'start';

            $items[] = [
                'image' => $image,
                'position' => in_array($position, $allowedPositions, true) ? $position : 'start',
            ];
        }

        return $items;
    }

    protected function sanitizeLetterIntroAds(Request $request)
    {
        $images = $request->letter_intro_ad_images ?: [];
        $categoryIds = $request->letter_intro_ad_category_ids ?: [];
        $letters = $request->letter_intro_ad_letters ?: [];
        $orders = $request->letter_intro_ad_orders ?: [];
        $allowedLetters = array_merge(range('A', 'Z'), ['#']);
        $itemsByKey = [];

        foreach ($images as $index => $image) {
            $image = trim((string) $image);
            $categoryId = (int) ($categoryIds[$index] ?? 0);
            $letter = Str::upper(trim((string) ($letters[$index] ?? '')));
            $order = (int) ($orders[$index] ?? 1);

            if ($image === '' || $categoryId <= 0 || ! in_array($letter, $allowedLetters, true)) {
                continue;
            }

            $order = in_array($order, [1, 2], true) ? $order : 1;
            $key = $categoryId . '|' . $letter;

            // Máximo dos separadores por categoría y letra. Si se repite el mismo
            // orden, la última fila configurada reemplaza la anterior.
            $itemsByKey[$key][$order] = [
                'image' => $image,
                'category_id' => $categoryId,
                'letter' => $letter,
                'order' => $order,
            ];
        }

        $items = [];
        foreach ($itemsByKey as $orderedItems) {
            ksort($orderedItems);
            foreach (array_slice($orderedItems, 0, 2, true) as $item) {
                $items[] = $item;
            }
        }

        return $items;
    }

    protected function catalogAdvertisingItems(array $settings)
    {
        if (! empty($settings['advertising_items']) && is_array($settings['advertising_items'])) {
            return $settings['advertising_items'];
        }

        if (! empty($settings['advertising_image']) && ($settings['advertising_position'] ?? '') === 'after_each_letter') {
            return [[
                'image' => $settings['advertising_image'],
                'letter' => 'A',
            ]];
        }

        return [];
    }

    /**
     * Guarda todos los bloques detectados, incluso los que todavía no tienen imagen.
     * Así la tabla puede volver a mostrarse al editar el catálogo. Cada bloque admite
     * una sola pieza; seleccionar otra imagen reemplaza el valor anterior.
     */
    protected function sanitizeDiagnosticFillerBlocks(Request $request): array
    {
        $images = $request->input('diagnostic_filler_images', []);
        $categoryIds = $request->input('diagnostic_filler_category_ids', []);
        $letters = $request->input('diagnostic_filler_letters', []);
        $blockIndexes = $request->input('diagnostic_filler_block_indexes', []);
        $xs = $request->input('diagnostic_filler_xs', []);
        $ys = $request->input('diagnostic_filler_ys', []);
        $widths = $request->input('diagnostic_filler_widths', []);
        $heights = $request->input('diagnostic_filler_heights', []);
        $spaces = $request->input('diagnostic_filler_spaces', []);
        $widthMms = $request->input('diagnostic_filler_width_mms', []);
        $heightMms = $request->input('diagnostic_filler_height_mms', []);
        $widthPixels = $request->input('diagnostic_filler_width_pixels', []);
        $heightPixels = $request->input('diagnostic_filler_height_pixels', []);
        $productsOnLastPage = $request->input('diagnostic_filler_products_on_last_page', []);
        $capacities = $request->input('diagnostic_filler_capacities', []);
        $freeSpaces = $request->input('diagnostic_filler_free_spaces', []);
        $items = [];

        foreach ($categoryIds as $index => $rawCategoryId) {
            $categoryId = (int) $rawCategoryId;
            $letter = mb_strtoupper(trim((string) ($letters[$index] ?? '')), 'UTF-8');
            $width = max(1, min(4, (int) ($widths[$index] ?? 1)));
            $height = max(1, min(5, (int) ($heights[$index] ?? 1)));

            if ($categoryId <= 0 || $letter === '') {
                continue;
            }

            $items[] = [
                'image' => trim((string) ($images[$index] ?? '')),
                'category_id' => $categoryId,
                'letter' => $letter,
                'block_index' => max(0, min(99, (int) ($blockIndexes[$index] ?? $index))),
                'x' => max(0, min(4, (int) ($xs[$index] ?? 0))),
                'y' => max(0, min(5, (int) ($ys[$index] ?? 0))),
                'width' => $width,
                'height' => $height,
                'spaces' => max(1, min(20, (int) ($spaces[$index] ?? ($width * $height)))),
                'width_mm' => round(max(1, min(300, (float) ($widthMms[$index] ?? 1))), 1),
                'height_mm' => round(max(1, min(300, (float) ($heightMms[$index] ?? 1))), 1),
                'width_px_300' => max(1, min(10000, (int) ($widthPixels[$index] ?? 1))),
                'height_px_300' => max(1, min(10000, (int) ($heightPixels[$index] ?? 1))),
                'products_on_last_page' => max(0, min(20, (int) ($productsOnLastPage[$index] ?? 0))),
                'capacity' => (int) ($capacities[$index] ?? 12) === 20 ? 20 : 12,
                'free_spaces' => max(1, min(20, (int) ($freeSpaces[$index] ?? ($width * $height)))),
            ];
        }

        usort($items, fn ($a, $b) => [$a['category_id'], $a['letter'], $a['block_index']] <=> [$b['category_id'], $b['letter'], $b['block_index']]);

        return $items;
    }

    protected function sanitizeFillerAds(Request $request): array
    {
        $images = $request->filler_ad_images ?: [];
        $sizes = $request->filler_ad_sizes ?: [];
        $heights = $request->filler_ad_heights ?: [];
        $categoryIds = $request->filler_ad_category_ids ?: [];
        $priorities = $request->filler_ad_priorities ?: [];
        $items = [];

        foreach ($images as $index => $image) {
            $image = trim((string) $image);
            if ($image === '') continue;
            $items[] = [
                'image' => $image,
                'size' => max(1, min(4, (int) ($sizes[$index] ?? 1))),
                'height' => max(1, min(5, (int) ($heights[$index] ?? 1))),
                'category_id' => ((int) ($categoryIds[$index] ?? 0)) ?: null,
                'priority' => max(1, min(999, (int) ($priorities[$index] ?? 100))),
            ];
        }
        return $items;
    }

    protected function sanitizeManualFillerAds(Request $request): array
    {
        $images = $request->manual_filler_images ?: [];
        $sizes = $request->manual_filler_sizes ?: [];
        $heights = $request->manual_filler_heights ?: [];
        $categoryIds = $request->manual_filler_category_ids ?: [];
        $letters = $request->manual_filler_letters ?: [];
        $orders = $request->manual_filler_orders ?: [];
        $items = [];

        foreach ($images as $index => $image) {
            $image = trim((string) $image);
            $letter = mb_strtoupper(trim((string) ($letters[$index] ?? '')), 'UTF-8');
            $categoryId = (int) ($categoryIds[$index] ?? 0);

            if ($image === '' || $categoryId <= 0 || $letter === '') {
                continue;
            }

            $items[] = [
                'image' => $image,
                'size' => max(1, min(4, (int) ($sizes[$index] ?? 1))),
                'height' => max(1, min(5, (int) ($heights[$index] ?? 1))),
                'category_id' => $categoryId,
                'letter' => $letter,
                'order' => max(1, min(99, (int) ($orders[$index] ?? ($index + 1)))),
            ];
        }

        usort($items, fn ($a, $b) => [$a['category_id'], $a['letter'], $a['order']] <=> [$b['category_id'], $b['letter'], $b['order']]);

        return $items;
    }

    protected function catalogLetterIntroAds(array $settings)
    {
        return ! empty($settings['letter_intro_ads']) && is_array($settings['letter_intro_ads'])
            ? $settings['letter_intro_ads']
            : [];
    }
}
