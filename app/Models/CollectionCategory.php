<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CollectionCategory extends Model
{
    protected $guarded = [];

    public function collection()
    {
        return $this->belongsTo(Collection::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
