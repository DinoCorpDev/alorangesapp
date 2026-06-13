<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\AllCategoryCollection;
use App\Http\Resources\CategoryCollection;
use App\Http\Services\AlegraServices;
use App\Models\Setting;
use App\Models\Category;
use App\Models\CategoryTranslation;
use Illuminate\Support\Facades\DB;
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
            try {
                DB::statement('SET FOREIGN_KEY_CHECKS=0');
                CategoryTranslation::truncate();
                Category::truncate();
            } finally {
                DB::statement('SET FOREIGN_KEY_CHECKS=1');
            }

            $categories = (new AlegraServices)->getCategories();
            $counter = 0;

            foreach ($categories as $alegraCategory) {
                if (!isset($alegraCategory['id'], $alegraCategory['name'])) {
                    continue;
                }

                $category = new Category;
                $category->id = $alegraCategory['id'];
                $category->name = $alegraCategory['name'];
                $category->slug = Str::slug($alegraCategory['name'], '-') . '-' . strtolower(Str::random(5));
                $category->parent_id = 0;
                $category->level = 0;
                $category->featured = 0;
                $category->status = $alegraCategory['status'] == 'active' ? 1 : 0;
                $category->order_level = $counter;
                $category->meta_title = $alegraCategory['name'];
                $category->meta_description = $alegraCategory['description'] ?? null;
                $category->save();

                $categoryTranslation = CategoryTranslation::firstOrNew([
                    'lang' => env('DEFAULT_LANGUAGE', 'en'),
                    'category_id' => $category->id
                ]);
                $categoryTranslation->name = $alegraCategory['name'];
                $categoryTranslation->save();

                $counter++;
            }

            $url = config('app.url') . '/admin/categories';

            if (request()->hasSession()) {
                return redirect($url)->with('success', 'Las categorias han sido actualizadas correctamente');
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
}
