<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class AddressCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            'data' => $this->collection->map(function ($data) {
                return [
                    'id'      => $data->id,
                    'user_id' => $data->user_id,
                    'address' => $data->address,
                    'name' => $data->name,
                    'details' => $data->details,
                    'country' => $data->country,
                    'country_id' => $data->country_id,
                    'state' => $data->state,
                    'state_id' => $data->state_id,
                    'city' => $data->city,
                    'city_id' => $data->city_id,
                    'neighborhood' => $data->neighborhood,
                    'postal_code' => $data->postal_code,
                    'phone' => $data->phone,
                    'default_shipping' => $data->default_shipping,
                    'default_billing' => $data->default_billing,
                    'default_service' => $data->default_service,
                    'editar' => false,
                    'mostrarDatos' => false,
                    'favorite' => $data->favorite
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
