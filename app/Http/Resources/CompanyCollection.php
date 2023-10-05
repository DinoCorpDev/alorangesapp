<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class CompanyCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            'data' => $this->collection->map(function ($data) {
                return [
                    'id'      => $data->id,
                    'user_id'      => $data->user_id,
                    'first_name' => $data->first_name,
                    'second_name' => $data->second_name,
                    'first_lastname' => $data->first_lastname,
                    'secondLastname' => $data->second_lastname,
                    'registerSince' => $data->created_at,
                    'person_type' => $data->person_type,
                    'document_type' => $data->document_type,
                    'document_number' => $data->document_number,
                    'company_razon' => $data->company_razon,
                    'company_phone' => $data->company_phone,
                    'company_email' => $data->company_email,
                    'company_actividad' => $data->company_actividad,
                    'company_type' => $data->company_type,
                    'company_document_type' => $data->company_document_type,
                    'company_document_number' => $data->company_document_number,
                    'editar' => false,
                    'mostrarDatos' => false
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
