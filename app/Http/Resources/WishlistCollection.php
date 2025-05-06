<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class WishlistCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            'data' => $this->collection->map(function ($data) {
                return [
                    'id' => (int) $data->product->id,
                    'name' => $data->product->name,
                    'brand' => [
                        'name' => optional($data->product->brand)->getTranslation('name'),
                        'slug' => optional($data->product->brand)->slug,
                    ],
                    'slug' => $data->product->slug,
                    'thumbnail_image' => $data->product->thumbnail_img,
                    'base_price' => (float) product_base_price($data->product),
                    'base_discounted_price' => (float) product_discounted_base_price($data->product),
                    'stock' => $data->product->stock,
                    'unit' => $data->product->unit,
                    'min_qty' => $data->product->min_qty,
                    'max_qty' => $data->product->max_qty,
                    'rating' => (float) $data->product->rating,
                    'is_variant' => (int) $data->product->is_variant,
                    'variations' => $data->product->variations,
                ];
            })
        ];
    }

    public function with($request)
    {
        return [
            'success' => true,
            'status' => 200
        ];
    }
}
