<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $fillable = [
        'user_id',
        'temp_user_id',
        'product_id',
        'product_variation_id',
        'collection_id',
        'service_id',
        'quantity',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function variation()
    {
        return $this->belongsTo(ProductVariation::class, 'product_variation_id');
    }

    public function collection()
    {
        return $this->belongsTo(Collection::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
