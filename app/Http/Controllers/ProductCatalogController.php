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
            'category_ids' => 'required|array|min:1',
            'category_ids.*' => 'integer|exists:categories,id',
        ]);

        $categoryIds = array_values(array_unique($request->category_ids));

        $categories = Category::whereIn('id', $categoryIds)
            ->orderBy('name')
            ->get();

        $categoryProducts = $categories->map(function ($category) {
            $products = Product::whereHas('categories', function ($query) use ($category) {
                    $query->where('categories.id', $category->id);
                })
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

            return [
                'category_id' => $category->id,
                'category_name' => $category->getTranslation('name'),
                'products' => $products,
            ];
        })->filter(function ($category) {
            return $category['products']->isNotEmpty();
        })->values();

        return response()->json($categoryProducts);
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_ids' => 'required|array|min:1',
            'category_ids.*' => 'integer|exists:categories,id',
            'product_ids' => 'required|array|min:1',
            'product_ids.*' => 'integer|exists:products,id',
            'name' => 'nullable|string|max:255',
        ]);

        $categoryIds = array_values(array_unique($request->category_ids));
        $categories = Category::whereIn('id', $categoryIds)
            ->orderBy('name')
            ->get();
        $categoryNames = $categories
            ->map(function ($category) {
                return $category->getTranslation('name');
            })
            ->values();
        $productIds = array_values(array_unique($request->product_ids));

        $products = Product::whereIn('id', $productIds)
            ->where('lowest_price', '>', 0)
            ->whereHas('categories', function ($query) use ($categoryIds) {
                $query->whereIn('categories.id', $categoryIds);
            })
            ->get()
            ->sortBy(function ($product) {
                return Str::lower($product->getTranslation('name'));
            })
            ->values();

        if ($products->isEmpty()) {
            flash(translate('Select at least one product from the selected categories'))->error();
            return back();
        }

        $productsByCategory = $categories->map(function ($category) use ($productIds) {
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

        $catalogName = $request->name ?: translate('Catalog') . ' - ' . $categoryNames->join(', ') . ' - ' . now()->format('Y-m-d H:i');
        $directory = public_path('uploads/catalogs');

        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $fileName = Str::slug($catalogName) . '-' . now()->format('YmdHis') . '.pdf';
        $relativePath = 'uploads/catalogs/' . $fileName;

        PDF::loadView('backend.product.catalogs.pdf', [
            'catalogName' => $catalogName,
            'categories' => $categories,
            'products' => $products,
            'productsByCategory' => $productsByCategory,
        ], [], [])->save(public_path($relativePath));

        $catalogs = $this->catalogs();
        array_unshift($catalogs, [
            'id' => (string) Str::uuid(),
            'name' => $catalogName,
            'category_name' => $categoryNames->join(', '),
            'category_ids' => $categoryIds,
            'category_names' => $categoryNames->all(),
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
