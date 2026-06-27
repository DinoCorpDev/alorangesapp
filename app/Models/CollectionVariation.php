<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CollectionVariation extends Model
{
    protected $guarded = [];

    public function collection()
    {
        return $this->belongsTo(Collection::class, 'collection_id');
    }
}
