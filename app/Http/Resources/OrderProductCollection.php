<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Models\Product;

class OrderProductCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            'data' => $this->collection->map(function ($data) {
                // Buscar producto directamente por ID si existe
                $product = $data->product_id ? Product::find($data->product_id) : null;

                return [
                    'id' => $product ? $product->id : null,
                    'name' => $product ? $product->getTranslation('name') : translate('Product has been removed'),
                    'thumbnail' => $product ? $product->thumbnail_img : '',
                    'thumbnail_image' => $product ? $product->thumbnail_img : '',
                    'combinations' => $data->variation ? filter_variation_combinations($data->variation->combinations) : [],
                    'price' => $data->price,
                    'tax' => $data->tax,
                    'total' => $data->total,
                    'quantity' => $data->quantity,
                    'order_detail_id' => $data->id,
                ];
            }),
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
