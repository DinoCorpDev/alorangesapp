<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Wishlist extends Model
{
    protected $fillable = [
        'user_id', 'product_id', 'services_id', 'brands_id'
    ];
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function brands()
    {
        return $this->belongsTo(Brand::class);
    }

    public function services()
    {
        return $this->belongsTo(Service::class);
    }
}
