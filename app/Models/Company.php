<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Company extends Model
{
    protected $table = "empresas";


    protected $fillable = [
        'user_id', 'person_type', 'first_name', 'second_name', 'first_lastname', 'second_lastname', 'document_type', 'document_number', 'company_razon', 'company_type', 'company_email', 'company_actividad', 'company_phone', 'company_document_type', 'company_document_number', 'documento_file', 'camara_file', 'rut_file', 'regimen_fiscal', 'responsabilidad_tribut',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
