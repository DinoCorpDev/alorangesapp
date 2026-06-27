<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\AllCategoryCollection;
use App\Http\Resources\CategoryCollection;
use App\Http\Services\AlegraServices;
use App\Models\Setting;
use App\Models\Category;
use App\Models\CategoryTranslation;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CategoryController extends Controller
{

    public function index()
    {
        return Category::all();
    }

    public function featured()
    {
        return new CategoryCollection(Category::where('featured', 1)->get());
    }

    public function first_level_categories()
    {
        return new CategoryCollection(Category::where('level', 0)->get());
    }

    public function get_category_by_name($name)
    {
        return new CategoryCollection(Category::where('name', $name)->get());
    }

    public function categories_home()
    {
        return new CategoryCollection(
            Category::where('status', 1)
                ->orderBy('order_level', 'asc')
                ->orderBy('name', 'asc')
                ->get()
        );
    }

    public function alegra()
    {
        try {
            $categories = (new AlegraServices)->getCategories();
            $counter = 0;
            $created = 0;
            $updated = 0;

            foreach ($categories as $alegraCategory) {
                if (!isset($alegraCategory['id'], $alegraCategory['name'])) {
                    continue;
                }

                $category = Category::where('id', $alegraCategory['id'])
                    ->orWhere('name', $alegraCategory['name'])
                    ->first();

                $isNewCategory = $category === null;

                if ($isNewCategory) {
                    $category = new Category;
                    $category->id = $alegraCategory['id'];
                    $category->slug = $this->generateUniqueSlug($alegraCategory['name']);
                    $category->parent_id = 0;
                    $category->level = 0;
                    $category->featured = 0;
                    $category->status = 0;
                }

                $category->name = $alegraCategory['name'];
                $category->status = $category->status == 1 ? 1 : 0;

                if (empty($category->slug)) {
                    $category->slug = $this->generateUniqueSlug($alegraCategory['name'], $category->id);
                }

                if (empty($category->meta_title)) {
                    $category->meta_title = $alegraCategory['name'];
                }

                if (empty($category->meta_description) && !empty($alegraCategory['description'])) {
                    $category->meta_description = $alegraCategory['description'];
                }

                $category->save();

                $categoryTranslation = CategoryTranslation::firstOrNew([
                    'lang' => env('DEFAULT_LANGUAGE', 'en'),
                    'category_id' => $category->id
                ]);
                $categoryTranslation->name = $alegraCategory['name'];
                $categoryTranslation->save();

                $counter++;
                $isNewCategory ? $created++ : $updated++;
            }

            $url = config('app.url') . '/admin/categories';
            $message = "Las categorias han sido actualizadas correctamente. Creadas: {$created}. Actualizadas: {$updated}.";

            if (request()->hasSession()) {
                return redirect($url)->with('success', $message);
            }

            return redirect($url . '?alegra_categories=updated');
        } catch (\Throwable $th) {
            Log::error('Alegra categories update failed', [
                'message' => $th->getMessage(),
                'exception' => $th
            ]);

            $url = config('app.url') . '/admin/categories';

            if (request()->hasSession()) {
                return redirect($url)->withErrors('There was a problem updating the categories!');
            }

            return redirect($url . '?alegra_categories=error');
        }
    }

    private function generateUniqueSlug($name, $ignoreCategoryId = null)
    {
        $baseSlug = Str::slug($name, '-');
        $slug = $baseSlug;
        $counter = 1;

        while (
            Category::where('slug', $slug)
                ->when($ignoreCategoryId, function ($query) use ($ignoreCategoryId) {
                    $query->where('id', '!=', $ignoreCategoryId);
                })
                ->exists()
        ) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
}
