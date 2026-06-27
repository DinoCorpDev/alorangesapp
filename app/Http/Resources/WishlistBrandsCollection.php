<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class WishlistBrandsCollection extends ResourceCollection
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
                return [
                    'id' => (int) $data->brands->id,
                    'name' => $data->brands->name,
                    'logo' => $data->brands->logo,
                    'slug' => $data->brands->slug,
                    'meta_title' => $data->brands->meta_title,
                    'meta_description' => $data->brands->meta_description,
                    'sales_amount' => $data->brands->sales_amount,
                    'biografia' => $data->brands->biografia,
                    'banner' => api_asset($data->brands->banner),
                    'fundacion' => $data->brands->fundacion,
                    'ensamblado' => $data->brands->ensamblado,
                    'diseno' => $data->brands->diseno,
                    'resumen' => $data->brands->resumen,
                    'pais' => $data->brands->pais
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
