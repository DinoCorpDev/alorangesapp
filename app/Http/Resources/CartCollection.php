<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class CartCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            'data' => $this->collection->map(function ($data) {
                $objectType = 'product';

                if ($data->collection_id) {
                    $objectType = 'collection';
                } elseif ($data->service_id) {
                    $objectType = 'service';
                }

                $newData = [
                    'cart_id' => (int) $data->id,
                    'shop_id' => (int) $data->$objectType->shop_id,
                    'earn_point' => (float) $data->$objectType->earn_point,
                    'variation_id' => (int) $data[$objectType . '_variation_id'],
                    'name' => $data->$objectType->name,
                    'brand' => [
                        'name' => optional($data->$objectType->brand)->getTranslation('name'),
                        'slug' => optional($data->$objectType->brand)->slug,
                    ],
                    'thumbnail' => $data->variation->img ? api_asset($data->variation->img) : api_asset($data->$objectType->thumbnail_img),
                    'regular_price' => (float) variation_price($data->$objectType, $data->variation),
                    'dicounted_price' => (float) variation_discounted_price($data->$objectType, $data->variation),
                    'stock' => (int) $data->variation->stock,
                    'min_qty' => (int) $data->$objectType->min_qty,
                    'max_qty' => (int) $data->$objectType->max_qty,
                    'standard_delivery_time' => (int) $data->$objectType->standard_delivery_time,
                    'express_delivery_time' => (int) $data->$objectType->express_delivery_time,
                    'qty' => (int) $data->quantity,
                    'is_digital' => $data->$objectType->digital,
                    'slug' => $data->$objectType->slug,
                ];

                if ($data->product_id) {
                    $newData['product_id'] = (int) $data->product_id;
                    $newData['combinations'] = filter_variation_combinations($data->variation->combinations);
                    $newData['tax'] = (float) product_variation_tax($data->$objectType, $data->variation);
                } elseif ($data->collection_id) {
                    $newData['collection_id'] = (int) $data->collection_id;
                } elseif ($data->service_id) {
                    $newData['service_id'] = (int) $data->service_id;
                }

                return $newData;
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
