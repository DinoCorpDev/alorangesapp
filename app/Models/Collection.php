<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Collection extends Model
{
    protected $guarded = [];

    public function collection_categories()
    {
        return $this->hasMany(CollectionCategory::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'collection_categories', 'collection_id', 'category_id');
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class);
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function carts()
    {
        return $this->hasMany(Cart::class);
    }

    public function variations()
    {
        return $this->hasMany(CollectionVariation::class);
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }
}
