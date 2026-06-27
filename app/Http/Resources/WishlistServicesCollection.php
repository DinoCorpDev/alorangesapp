<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class WishlistServicesCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'data' => $this->collection->map(function ($data) {
                //dd($data);
                return [
                    'id' => (int) $data->services->id,
                    'reference' => $data->services->reference,
                    'name' => $data->services->name,
                    'photos' => $data->services->photos,
                    'thumbnail_img' => $data->services->thumbnail_img,
                    'description' => $data->services->description,
                    'lowest_price' => $data->services->lowest_price,
                    'highest_price' => $data->services->highest_price,
                    'discount' => api_asset($data->services->discount),
                    'discount_type' => $data->services->discount_type,
                    'discount_start_date' => $data->services->discount_start_date,
                    'discount_end_date  ' => $data->services->discount_end_date,
                    'currency' => $data->services->currency,
                    'published' => $data->services->published,
                    'unit' => $data->services->unit,
                    'min_qty' => $data->services->min_qty,
                    'max_qty' => $data->services->max_qty,
                    'tinyint' => $data->services->tinyint,
                    'has_warranty,' => $data->services->has_warranty,
                    'warranty_text,' => $data->services->warranty_text,
                    'tax,' => $data->services->tax,
                    'meta_title,' => $data->services->meta_title,
                    'meta_description,' => $data->services->meta_description,
                    'meta_description,' => $data->services->meta_description,
                    'meta_image,' => $data->services->meta_image,
                    'slug,' => $data->services->slug,
                    'rating,' => $data->services->rating
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
