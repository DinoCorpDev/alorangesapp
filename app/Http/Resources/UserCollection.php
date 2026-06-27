<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserCollection extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'balance' => $this->balance,
            'name' => $this->first_name . ' ' . $this->first_lastname,
            'firstName' => $this->first_name,
            'secondName' => $this->second_name,
            'firstLastname' => $this->first_lastname,
            'secondLastname' => $this->second_lastname,
            'email' => $this->email,
            'phone' => $this->phone,
            'avatar' => $this->avatar ? asset('public/avatars/'.$this->avatar) : '',
            'registerSince' => $this->created_at,
            'personType' => $this->person_type,
            'documentType' => $this->document_type,
            'documentNumber' => $this->document_number,
            'company_razon' => $this->company_razon,
            'company_phone' => $this->company_phone,
            'company_email' => $this->company_email,
            'company_actividad' => $this->company_actividad,
            'company_type' => $this->company_type,
            'company_document_type' => $this->company_document_type,
            'company_document_number' => $this->company_document_number,
            'policiesAndCookiesConsent' => $this->policies_and_cookies_consent,
            'offersConsent' => $this->offers_consent,
            'termsCondTrade' => $this->terms_cond_trade,
            'guaranteePolicies' => $this->guarantee_policies,
            'termsLogistics' => $this->terms_logistics,

        ];
    }
}
