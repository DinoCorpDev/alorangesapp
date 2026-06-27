<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\CollectionCollection;
use App\Http\Resources\CollectionSingleCollection;
use App\Models\Collection;
use App\Models\CollectionOrderDetail;
use Illuminate\Http\Request;

class CollectionController extends Controller
{
    public function index()
    {
        return new CollectionCollection(Collection::latest()->paginate(10));
    }

    public function show($collection_slug)
    {
        $collection = Collection::where('slug', $collection_slug)
            ->with(['brand', 'variations', 'shop'])
            ->first();

        if ($collection) {
            return new CollectionSingleCollection($collection);
        } else {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => translate('Collection not found')
            ]);
        }
    }
}
