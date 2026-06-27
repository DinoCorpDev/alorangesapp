<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class CodigoPostal extends Model
{
    protected $table = "codigo_postal";

    protected $fillable = [
        'state_id',
        'codigo',
    ];
}
