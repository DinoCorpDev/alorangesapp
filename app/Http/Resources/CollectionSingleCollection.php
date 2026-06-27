<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CollectionSingleCollection extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // dd($this->variations);
        return [
            'id' => (int) $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'metaTitle' => $this->meta_title,
            'brand' => [
                'id' => optional($this->brand)->id,
                'name' => optional($this->brand)->name,
                'slug' => optional($this->brand)->slug,
                'logo' => api_asset(optional($this->brand)->logo),
            ],
            'photos' => $this->convertPhotos($this),
            'thumbnail_image' => api_asset($this->thumbnail_img),
            'featured' => (int) $this->featured,
            'stock' => (int) $this->stock,
            'discount' => $this->discount,
            'discount_type' => $this->discount_type,
            'base_price' => (float) product_base_price($this, false),
            'highest_price' => (float) product_highest_price($this, false),
            'base_discounted_price' => (float) product_discounted_base_price($this, false),
            'highest_discounted_price' => (float) product_discounted_highest_price($this, false),
            'standard_delivery_time' => (int) $this->standard_delivery_time,
            'express_delivery_time' => (int) $this->express_delivery_time,
            'is_variant' => $this->is_variant,
            'has_warranty' => $this->has_warranty,
            'description' => $this->description,
            'variations' => filter_product_variations($this->variations, $this),
            'shop' => [
                'name' => $this->shop->name,
                'logo' => api_asset($this->shop->logo),
                'rating' => (float) $this->shop->rating,
                'review_count' => $this->shop->reviews_count,
                'slug' => $this->shop->slug,
            ],
        ];
    }

    public function with($request)
    {
        return [
            'success' => true,
            'status' => 200
        ];
    }

    protected function convertPhotos()
    {
        $result = array();

        foreach (explode(',', $this->photos) as $item) {
            array_push($result, api_asset_new($item));
        }

        return $result;
    }
}
