<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;

class ProductCatalogController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:show_categories']);
    }

    public function index()
    {
        return $this->formView();
    }

    public function edit($catalog)
    {
        $catalog = collect($this->catalogs())->firstWhere('id', $catalog);

        if (! $catalog) {
            flash(translate('Catalog was not found'))->error();
            return redirect()->route('product_catalogs.index');
        }

        return $this->formView($catalog);
    }

    public function configurationDefaults()
    {
        $catalog = null;
        $settings = $this->catalogDefaultsConfig();
        $sharedBlocks = $this->sharedBlocksConfig();
        $letters = array_merge(range('A', 'Z'), ['#']);
        $letterPalette = $this->letterPalette();
        $action = route('product_catalogs.configuration.defaults.update');
        $method = 'POST';

        return view('backend.product.catalogs.configuration', compact('catalog', 'settings', 'sharedBlocks', 'letters', 'letterPalette', 'action', 'method'));
    }

    public function updateConfigurationDefaults(Request $request)
    {
        $settings = $this->validatedConfigurationSettings($request);
        unset($settings['cover_image']);
        $this->saveJson($this->catalogDefaultsPath(), $settings);

        flash(translate('Catalog configuration updated successfully'))->success();
        return redirect()->route('product_catalogs.index');
    }

    public function configuration($catalog)
    {
        $catalog = collect($this->catalogs())->firstWhere('id', $catalog);

        if (! $catalog) {
            flash(translate('Catalog was not found'))->error();
            return redirect()->route('product_catalogs.index');
        }

        $settings = array_merge($this->catalogDefaultsConfig(), $catalog['settings'] ?? []);
        $sharedBlocks = $this->sharedBlocksConfig();
        $letters = array_merge(range('A', 'Z'), ['#']);
        $letterPalette = $this->letterPalette();
        $action = route('product_catalogs.configuration.update', $catalog['id']);
        $method = 'PUT';

        return view('backend.product.catalogs.configuration', compact('catalog', 'settings', 'sharedBlocks', 'letters', 'letterPalette', 'action', 'method'));
    }

    public function updateConfiguration(Request $request, $catalog)
    {
        $catalogs = collect($this->catalogs());
        $existing = $catalogs->firstWhere('id', $catalog);

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
        $settings['advertising_items'] = $this->catalogAdvertisingItems($existing['settings'] ?? []);

        $categories = Category::whereIn('id', $existing['category_ids'] ?? [])
            ->orderBy('order_level')->orderBy('name')->get();
        $products = Product::whereIn('id', $existing['product_ids'] ?? [])->where('lowest_price', '>', 0)->get();
        $productsByCategory = $this->productsByCategory($categories, $existing['product_ids'] ?? []);
        $filePath = $this->renderCatalogPdf($existing['name'], $categories, $products, $productsByCategory, $settings);

        $oldPath = public_path($existing['file_path'] ?? '');
        if (! empty($existing['file_path']) && file_exists($oldPath)) {
            unlink($oldPath);
        }

        $updated = array_merge($existing, [
            'settings' => $settings,
            'file_path' => $filePath,
            'updated_at' => now()->format('Y-m-d H:i:s'),
        ]);

        $this->saveCatalogs($catalogs->map(function ($item) use ($catalog, $updated) {
            return $item['id'] === $catalog ? $updated : $item;
        })->values()->all());

        flash(translate('Catalog configuration updated successfully'))->success();
        return redirect()->route('product_catalogs.index');
    }

    public function categoryProducts(Request $request)
    {
        $request->validate([
            'category_ids' => 'required|array|min:1',
            'category_ids.*' => 'integer|exists:categories,id',
        ]);

        $categories = Category::whereIn('id', array_values(array_unique($request->category_ids)))
            ->orderBy('order_level')->orderBy('name')->get();

        return response()->json($categories->map(function ($category) {
            $products = Product::whereHas('categories', function ($query) use ($category) {
                    $query->where('categories.id', $category->id);
                })->get()->map(function ($product) {
                    return [
                        'id' => $product->id,
                        'name' => $product->getTranslation('name'),
                        'price' => format_price($product->lowest_price),
                        'is_disabled' => (float) $product->lowest_price <= 0,
                    ];
                })->sortBy('name', SORT_NATURAL | SORT_FLAG_CASE)->values();

            return [
                'category_id' => $category->id,
                'category_name' => $category->getTranslation('name'),
                'products' => $products,
            ];
        })->filter(function ($group) {
            return $group['products']->isNotEmpty();
        })->values());
    }

    public function store(Request $request)
    {
        $catalog = $this->buildCatalog($request);

        if (! $catalog) {
            return back()->withInput();
        }

        $catalogs = $this->catalogs();
        array_unshift($catalogs, $catalog);
        $this->saveCatalogs($catalogs);

        flash(translate('Catalog generated successfully'))->success();
        return redirect()->route('product_catalogs.index');
    }

    public function update(Request $request, $catalog)
    {
        $catalogs = collect($this->catalogs());
        $existing = $catalogs->firstWhere('id', $catalog);

        if (! $existing) {
            flash(translate('Catalog was not found'))->error();
            return redirect()->route('product_catalogs.index');
        }

        $updated = $this->buildCatalog($request, $existing);

        if (! $updated) {
            return back()->withInput();
        }

        $oldPath = public_path($existing['file_path'] ?? '');
        if (! empty($existing['file_path']) && file_exists($oldPath)) {
            unlink($oldPath);
        }

        $this->saveCatalogs($catalogs->map(function ($item) use ($catalog, $updated) {
            return $item['id'] === $catalog ? $updated : $item;
        })->values()->all());

        flash(translate('Catalog updated successfully'))->success();
        return redirect()->route('product_catalogs.index');
    }

    public function download($catalog)
    {
        $catalog = collect($this->catalogs())->firstWhere('id', $catalog);

        if (! $catalog) {
            flash(translate('Catalog was not found'))->error();
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
        $catalogs = collect($this->catalogs());
        $catalogToDelete = $catalogs->firstWhere('id', $catalog);

        if (! $catalogToDelete) {
            flash(translate('Catalog was not found'))->error();
            return back();
        }

        $path = public_path($catalogToDelete['file_path']);

        if (file_exists($path)) {
            unlink($path);
        }

        $this->saveCatalogs($catalogs->reject(function ($item) use ($catalog) {
            return $item['id'] === $catalog;
        })->values()->all());

        flash(translate('Catalog deleted successfully'))->success();
        return redirect()->route('product_catalogs.index');
    }

    protected function formView($catalog = null)
    {
        $categories = Category::orderBy('order_level')->orderBy('name')->get();
        $catalogs = collect($this->catalogs())->sortByDesc('created_at')->values();
        $sharedBlocks = $this->sharedBlocksConfig();
        $settings = array_merge($this->defaultSettings(), $catalog['settings'] ?? []);
        $mode = $catalog ? 'edit' : 'create';

        return view('backend.product.catalogs.index', compact('categories', 'catalogs', 'catalog', 'sharedBlocks', 'settings', 'mode'));
    }

    protected function buildCatalog(Request $request, $existing = null)
    {
        $request->validate([
            'category_ids' => 'required|array|min:1',
            'category_ids.*' => 'integer|exists:categories,id',
            'product_ids' => 'required|array|min:1',
            'product_ids.*' => 'integer|exists:products,id',
            'name' => 'nullable|string|max:255',
            'cover_image' => 'nullable|string|max:255',
            'cover_title_position' => 'nullable|in:top,middle,bottom',
            'advisor_name' => 'nullable|string|max:120',
            'advisor_phone' => 'nullable|string|max:60',
            'advisor_email_1' => 'nullable|email|max:120',
            'advisor_email_2' => 'nullable|email|max:120',
            'advertising_images' => 'nullable|array',
            'advertising_images.*' => 'nullable|string|max:255',
            'advertising_letters' => 'nullable|array',
            'advertising_letters.*' => 'nullable|string|max:1',
        ]);

        $categoryIds = array_values(array_unique($request->category_ids));
        $productIds = array_values(array_unique($request->product_ids));
        $categories = Category::whereIn('id', $categoryIds)->orderBy('order_level')->orderBy('name')->get();
        $categoryNames = $categories->map(function ($category) { return $category->getTranslation('name'); })->values();

        $products = Product::whereIn('id', $productIds)
            ->where('lowest_price', '>', 0)
            ->whereHas('categories', function ($query) use ($categoryIds) {
                $query->whereIn('categories.id', $categoryIds);
            })->get()->sortBy(function ($product) {
                return Str::lower($product->getTranslation('name'));
            })->values();

        if ($products->isEmpty()) {
            flash(translate('Select at least one product from the selected categories'))->error();
            return null;
        }

        $settings = array_merge($this->catalogDefaultsConfig(), $existing['settings'] ?? [], [
            'cover_image' => $request->cover_image,
            'cover_title_position' => $request->cover_title_position ?: 'middle',
            'advisor_name' => $request->advisor_name,
            'advisor_phone' => $request->advisor_phone,
            'advisor_email_1' => $request->advisor_email_1,
            'advisor_email_2' => $request->advisor_email_2,
            'advertising_items' => $this->sanitizeAdvertisingItems($request),
        ]);

        $catalogName = $request->name ?: translate('Catalog') . ' - ' . $categoryNames->join(', ') . ' - ' . now()->format('Y-m-d H:i');
        $productsByCategory = $this->productsByCategory($categories, $productIds);
        $filePath = $this->renderCatalogPdf($catalogName, $categories, $products, $productsByCategory, $settings);

        return [
            'id' => $existing['id'] ?? (string) Str::uuid(),
            'name' => $catalogName,
            'category_name' => $categoryNames->join(', '),
            'category_ids' => $categoryIds,
            'category_names' => $categoryNames->all(),
            'product_ids' => $products->pluck('id')->values()->all(),
            'file_path' => $filePath,
            'products_count' => $products->count(),
            'settings' => $settings,
            'created_by' => $existing['created_by'] ?? auth()->id(),
            'created_at' => $existing['created_at'] ?? now()->format('Y-m-d H:i:s'),
            'updated_at' => now()->format('Y-m-d H:i:s'),
        ];
    }

    protected function productsByCategory($categories, array $productIds)
    {
        return $categories->map(function ($category) use ($productIds) {
            $products = Product::whereIn('id', $productIds)
                ->where('lowest_price', '>', 0)
                ->whereHas('categories', function ($query) use ($category) {
                    $query->where('categories.id', $category->id);
                })->get()->sortBy(function ($product) {
                    return Str::lower($product->getTranslation('name'));
                })->values();

            return [
                'category' => $category,
                'letter_groups' => $products->groupBy(function ($product) {
                    $letter = Str::upper(Str::substr(trim($product->getTranslation('name')), 0, 1));
                    return preg_match('/[A-Z0-9]/', $letter) ? $letter : '#';
                })->sortKeys(),
            ];
        })->filter(function ($group) {
            return $group['letter_groups']->isNotEmpty();
        })->values();
    }

    protected function renderCatalogPdf($catalogName, $categories, $products, $productsByCategory, array $settings)
    {
        $directory = public_path('uploads/catalogs');

        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $fileName = Str::slug($catalogName) . '-' . now()->format('YmdHis') . '.pdf';
        $relativePath = 'uploads/catalogs/' . $fileName;
        $absolutePath = public_path($relativePath);

        $viewData = [
            'catalogName' => $catalogName,
            'categories' => $categories,
            'products' => $products,
            'productsByCategory' => $productsByCategory,
            'settings' => $settings,
            'sharedBlocks' => $this->sharedBlocksConfig(),
            'letterPalette' => $this->letterPalette(),
            'fallbackImage' => uploaded_asset(get_setting('header_logo')) ?: static_asset('assets/img/logo.png'),
        ];

        $this->renderCatalogWithBrowser($viewData, $absolutePath);

        return $relativePath;
    }

    protected function renderCatalogWithBrowser(array $viewData, $absolutePath)
    {
        $chromePath = config('services.browsershot.chrome_path');

        if (! $chromePath || ! file_exists($chromePath)) {
            throw new \RuntimeException('Chrome executable was not found. Check BROWSERSHOT_CHROME_PATH.');
        }

        $tempDirectory = storage_path('app/product_catalogs/browser');

        if (! is_dir($tempDirectory)) {
            mkdir($tempDirectory, 0755, true);
        }

        $htmlPath = $tempDirectory . DIRECTORY_SEPARATOR . Str::uuid() . '.html';
        $userDataDirectory = $tempDirectory . DIRECTORY_SEPARATOR . 'chrome-' . Str::uuid();

        if (! is_dir($userDataDirectory)) {
            mkdir($userDataDirectory, 0755, true);
        }

        $html = view('backend.product.catalogs.pdf', $viewData)->render();
        file_put_contents($htmlPath, $html);

        $fileUrl = 'file:///' . str_replace('\\', '/', $htmlPath);
        $process = new Process([
            $chromePath,
            '--headless=new',
            '--user-data-dir=' . $userDataDirectory,
            '--disable-gpu',
            '--disable-dev-shm-usage',
            '--disable-setuid-sandbox',
            '--no-sandbox',
            '--ignore-certificate-errors',
            '--allow-file-access-from-files',
            '--run-all-compositor-stages-before-draw',
            '--virtual-time-budget=10000',
            '--print-to-pdf-no-header',
            '--print-to-pdf=' . $absolutePath,
            $fileUrl,
        ], base_path(), null, null, 300);

        $process->run();
        $pdfReady = $this->waitForPdfFile($absolutePath);

        if (! $process->isSuccessful() || ! $pdfReady) {
            Log::error('Chrome catalog PDF generation failed', [
                'exit_code' => $process->getExitCode(),
                'exit_code_text' => $process->getExitCodeText(),
                'output' => trim($process->getOutput()),
                'error_output' => trim($process->getErrorOutput()),
                'html_path' => $htmlPath,
                'pdf_path' => $absolutePath,
                'pdf_exists' => file_exists($absolutePath),
                'pdf_size' => file_exists($absolutePath) ? filesize($absolutePath) : 0,
            ]);

            throw new \RuntimeException(trim($process->getErrorOutput() ?: $process->getOutput() ?: 'Chrome could not generate the PDF. HTML debug file: '.$htmlPath));
        }

        if (file_exists($htmlPath)) {
            @unlink($htmlPath);
        }

        if (is_dir($userDataDirectory)) {
            $this->deleteDirectory($userDataDirectory);
        }
    }

    protected function waitForPdfFile($absolutePath, int $seconds = 30): bool
    {
        $deadline = microtime(true) + $seconds;
        $lastSize = 0;
        $stableChecks = 0;

        while (microtime(true) < $deadline) {
            clearstatcache(true, $absolutePath);

            if (file_exists($absolutePath)) {
                $size = filesize($absolutePath);

                if ($size > 0 && $size === $lastSize) {
                    $stableChecks++;

                    if ($stableChecks >= 2) {
                        return true;
                    }
                } else {
                    $stableChecks = 0;
                    $lastSize = $size;
                }
            }

            usleep(250000);
        }

        clearstatcache(true, $absolutePath);

        return file_exists($absolutePath) && filesize($absolutePath) > 0;
    }

    protected function deleteDirectory($directory)
    {
        $items = @scandir($directory);

        if ($items === false) {
            return;
        }

        foreach (array_diff($items, ['.', '..']) as $item) {
            $path = $directory . DIRECTORY_SEPARATOR . $item;

            is_dir($path) ? $this->deleteDirectory($path) : @unlink($path);
        }

        @rmdir($directory);
    }

    protected function catalogs()
    {
        $catalogs = $this->readJson($this->catalogIndexPath(), []);

        return collect(is_array($catalogs) ? $catalogs : [])->map(function ($catalog) {
            $catalog['settings'] = array_merge($this->defaultSettings(), $catalog['settings'] ?? []);
            $catalog['category_ids'] = $catalog['category_ids'] ?? [];
            $catalog['product_ids'] = $catalog['product_ids'] ?? [];
            $catalog['updated_at'] = $catalog['updated_at'] ?? null;
            return $catalog;
        })->all();
    }

    protected function saveCatalogs(array $catalogs)
    {
        $this->saveJson($this->catalogIndexPath(), $catalogs);
    }

    protected function sharedBlocksConfig()
    {
        return array_merge($this->defaultSharedBlocks(), $this->readJson($this->sharedBlocksPath(), []));
    }

    protected function catalogDefaultsConfig()
    {
        return array_merge($this->defaultSettings(), $this->readJson($this->catalogDefaultsPath(), []));
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
        ]);

        return array_merge($this->defaultSettings(), [
            'show_prices' => $request->has('show_prices'),
            'show_payment_page' => $request->has('show_payment_page'),
            'show_info_page' => $request->has('show_info_page'),
            'show_page_four' => $request->has('show_page_four'),
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

    protected function readJson($path, $default)
    {
        if (! file_exists($path)) {
            return $default;
        }

        $content = json_decode(file_get_contents($path), true);

        return is_array($content) ? $content : $default;
    }

    protected function saveJson($path, array $content)
    {
        $directory = dirname($path);

        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        file_put_contents($path, json_encode($content, JSON_PRETTY_PRINT));
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
        $allowedFonts = [
            'DejaVu Sans',
            'Arial',
            'Georgia',
            'Times New Roman',
            'Verdana',
            'Tahoma',
            'Courier New',
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

    protected function defaultSettings()
    {
        return [
            'show_prices' => true,
            'show_payment_page' => true,
            'show_info_page' => true,
            'show_page_four' => false,
            'description_limit' => 90,
            'cover_image' => null,
            'cover_title_position' => 'middle',
            'advisor_name' => '',
            'advisor_phone' => '',
            'advisor_email_1' => '',
            'advisor_email_2' => '',
            'advertising_image' => null,
            'advertising_position' => 'before_products',
            'advertising_items' => [],
            'payment_page_image' => null,
            'payment_bank_icon' => null,
            'payment_debit_icon' => null,
            'payment_credit_icon' => null,
            'payment_cash_icon' => null,
            'info_page_image' => null,
            'page_four_image' => null,
            'payment_title' => 'MEDIOS DE PAGO',
            'payment_delivery_title' => 'EFECTIVO CONTRA ENTREGA, DEPOSITO O TRANSFERENCIA DIRECTA',
            'payment_bank_info' => '',
            'payment_debit_title' => 'TARJETAS DEBITO',
            'payment_debit_info' => '',
            'payment_credit_title' => 'TARJETAS CREDITO',
            'payment_credit_info' => '',
            'payment_cash_title' => 'PAGUE EN EFECTIVO EN MAS DE 14.000 PUNTOS',
            'payment_cash_info' => '',
            'info_page_title' => 'INFORMACION',
            'info_page_content' => '',
            'info_table_rows' => [],
            'product_title_font_family' => 'DejaVu Sans',
            'product_title_font_size' => 12,
            'product_description_font_family' => 'DejaVu Sans',
            'product_description_font_size' => 10,
            'product_price_font_family' => 'DejaVu Sans',
            'product_price_font_size' => 16,
            'product_reference_font_family' => 'DejaVu Sans',
            'product_reference_font_size' => 12,
            'product_box_colors' => [],
            'product_text_colors' => [],
        ];
    }

    protected function defaultSharedBlocks()
    {
        return [
            'payment_page_image' => null,
            'info_page_image' => null,
            'updated_at' => null,
        ];
    }

    protected function letterPalette()
    {
        return [
            'A' => '#f36f21', 'B' => '#00a86b', 'C' => '#0f75bc', 'D' => '#ec1c24',
            'E' => '#8dc63f', 'F' => '#662d91', 'G' => '#f7941d', 'H' => '#00a99d',
            'I' => '#2e3192', 'J' => '#ed145b', 'K' => '#39b54a', 'L' => '#f15a24',
            'M' => '#0072bc', 'N' => '#92278f', 'O' => '#d4145a', 'P' => '#009245',
            'Q' => '#fbb03b', 'R' => '#1b75bb', 'S' => '#c1272d', 'T' => '#006837',
            'U' => '#9e005d', 'V' => '#29abe2', 'W' => '#f7931e', 'X' => '#7ac943',
            'Y' => '#3fa9f5', 'Z' => '#ff5a5f', '#' => '#4d4d4d',
        ];
    }

    protected function catalogIndexPath()
    {
        return storage_path('app/product_catalogs/catalogs.json');
    }

    protected function sharedBlocksPath()
    {
        return storage_path('app/product_catalogs/shared_blocks.json');
    }

    protected function catalogDefaultsPath()
    {
        return storage_path('app/product_catalogs/defaults.json');
    }
}
