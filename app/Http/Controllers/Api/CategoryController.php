<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\AllCategoryCollection;
use App\Http\Resources\CategoryCollection;
use App\Models\Setting;
use App\Models\Category;

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
        return new CategoryCollection(Category::where('status', 1)->get());
    }
}
