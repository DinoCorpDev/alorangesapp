<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use PDF;

class ProductCatalogController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:show_categories']);
    }

    public function index()
    {
        $categories = Category::orderBy('name')->get();
        $catalogs = collect($this->catalogs())->sortByDesc('created_at')->values();

        return view('backend.product.catalogs.index', compact('categories', 'catalogs'));
    }

    public function categoryProducts(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
        ]);

        $products = Product::whereHas('categories', function ($query) use ($request) {
                $query->where('categories.id', $request->category_id);
            })
            ->orderBy('name')
            ->get()
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->getTranslation('name'),
                    'price' => format_price($product->lowest_price),
                    'is_disabled' => (float) $product->lowest_price <= 0,
                ];
            })
            ->sortBy('name', SORT_NATURAL | SORT_FLAG_CASE)
            ->values();

        return response()->json($products);
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'product_ids' => 'required|array|min:1',
            'product_ids.*' => 'integer|exists:products,id',
            'name' => 'nullable|string|max:255',
        ]);

        $category = Category::findOrFail($request->category_id);
        $productIds = array_values(array_unique($request->product_ids));

        $products = Product::whereIn('id', $productIds)
            ->where('lowest_price', '>', 0)
            ->whereHas('categories', function ($query) use ($category) {
                $query->where('categories.id', $category->id);
            })
            ->get()
            ->sortBy(function ($product) {
                return Str::lower($product->getTranslation('name'));
            })
            ->values();

        if ($products->isEmpty()) {
            flash(translate('Select at least one product from the selected category'))->error();
            return back();
        }

        $catalogName = $request->name ?: translate('Catalog') . ' - ' . $category->getTranslation('name') . ' - ' . now()->format('Y-m-d H:i');
        $directory = public_path('uploads/catalogs');

        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $fileName = Str::slug($catalogName) . '-' . now()->format('YmdHis') . '.pdf';
        $relativePath = 'uploads/catalogs/' . $fileName;

        PDF::loadView('backend.product.catalogs.pdf', [
            'catalogName' => $catalogName,
            'category' => $category,
            'products' => $products,
        ], [], [])->save(public_path($relativePath));

        $catalogs = $this->catalogs();
        array_unshift($catalogs, [
            'id' => (string) Str::uuid(),
            'name' => $catalogName,
            'category_name' => $category->getTranslation('name'),
            'product_ids' => $products->pluck('id')->values()->all(),
            'file_path' => $relativePath,
            'products_count' => $products->count(),
            'created_by' => auth()->id(),
            'created_at' => now()->format('Y-m-d H:i:s'),
        ]);
        $this->saveCatalogs($catalogs);

        flash(translate('Catalog generated successfully'))->success();
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

    protected function catalogs()
    {
        $path = $this->catalogIndexPath();

        if (! file_exists($path)) {
            return [];
        }

        $catalogs = json_decode(file_get_contents($path), true);

        return is_array($catalogs) ? $catalogs : [];
    }

    protected function saveCatalogs(array $catalogs)
    {
        $directory = dirname($this->catalogIndexPath());

        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        file_put_contents($this->catalogIndexPath(), json_encode($catalogs, JSON_PRETTY_PRINT));
    }

    protected function catalogIndexPath()
    {
        return storage_path('app/product_catalogs/catalogs.json');
    }
}
