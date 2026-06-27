<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Localidad extends Model
{
    protected $table = "localidades";

    protected $fillable = [
        'state_id',
        'localidad',
    ];

	public function state(){
        return $this->belongsTo(State::class, 'state_id');
    }
}
