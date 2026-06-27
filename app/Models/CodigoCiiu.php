<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class CodigoCiiu extends Model
{
    protected $table = "codigo_ciiu";

    protected $fillable = [
        'codigo',
        'actividad',
    ];
}
