<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\ServiceCollection;
use App\Http\Resources\WishlistBrandsCollection;
use App\Http\Resources\WishlistCollection;
use App\Http\Resources\WishlistServicesCollection;
use App\Models\Brand;
use App\Models\Product;
use App\Models\Service;
use App\Models\Wishlist;
use Illuminate\Http\Request;

class WishlistController extends Controller
{

    public function index()
    {
        return new WishlistCollection(Wishlist::has('product')->where('user_id', auth('api')->user()->id)->latest()->get());
    }

    public function favoriteServices()
    {
        return new WishlistServicesCollection(Wishlist::has('services')->where('user_id', auth('api')->user()->id)->latest()->get());
    }

    public function favoriteBrands()
    {
        return new WishlistBrandsCollection(Wishlist::has('brands')->where('user_id', auth('api')->user()->id)->latest()->get());
    }

    public function store(Request $request)
    {
        Wishlist::updateOrCreate([
            'user_id' => auth('api')->user()->id,
            'product_id' => $request->product_id
        ]);

        $product = Product::with('variations')->find($request->product_id);

        return response()->json([
            'success' => true,
            'message' => translate('Producto agregado exitosamente a MI LISTA'),
            'product' => [
                'id' => (int) $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'thumbnail_image' => api_asset($product->thumbnail_img),
                'base_price' => (float) product_base_price($product),
                'base_discounted_price' => (float) product_discounted_base_price($product),
                'stock' => $product->stock,
                'unit' => $product->unit,
                'min_qty' => $product->min_qty,
                'max_qty' => $product->max_qty,
                'rating' => (float) $product->rating,
                'is_variant' => (int) $product->is_variant,
                'variations' => $product->variations,
            ]
        ], 200);
    }

    public function storeService(Request $request)
    {
        Wishlist::updateOrCreate([
            'user_id' => auth('api')->user()->id,
            'services_id' => $request->services_id
        ]);

        $service = Service::find($request->services_id);

        return response()->json([
            'success' => true,
            'message' => translate('Service is successfully added to your wishlist'),
            'service' => [
                'id' => (int) $service->id,
                'reference' => $service->reference,
                'name' => $service->name,
                'photos' => $service->photos,
                'thumbnail_img' => $service->thumbnail_img,
                'description' => $service->description,
                'lowest_price' => $service->lowest_price,
                'highest_price' => $service->highest_price,
                'discount' => api_asset($service->discount),
                'discount_type' => $service->discount_type,
                'discount_start_date' => $service->discount_start_date,
                'discount_end_date  ' => $service->discount_end_date,
                'currency' => $service->currency,
                'published' => $service->published,
                'unit' => $service->unit,
                'min_qty' => $service->min_qty,
                'max_qty' => $service->max_qty,
                'tinyint' => $service->tinyint,
                'has_warranty,' => $service->has_warranty,
                'warranty_text,' => $service->warranty_text,
                'tax,' => $service->tax,
                'meta_title,' => $service->meta_title,
                'meta_description,' => $service->meta_description,
                'meta_description,' => $service->meta_description,
                'meta_image,' => $service->meta_image,
                'slug,' => $service->slug,
                'rating,' => $service->rating
            ]
        ], 200);
    }

    public function storeBrand(Request $request)
    {
        Wishlist::updateOrCreate([
            'user_id' => auth('api')->user()->id,
            'brands_id' => $request->brands_id
        ]);

        $brand = Brand::find($request->brands_id);

        return response()->json([
            'success' => true,
            'message' => translate('Brand is successfully added to your wishlist'),
            'brand' => [
                'id' => (int) $brand->id,
                'name' => $brand->name,
                'logo' => $brand->logo,
                'slug' => $brand->slug,
                'meta_title' => $brand->meta_title,
                'meta_description' => $brand->meta_description,
                'sales_amount' => $brand->sales_amount,
                'biografia' => $brand->biografia,
                'banner' => api_asset($brand->banner),
                'fundacion' => $brand->fundacion,
                'ensamblado' => $brand->ensamblado,
                'diseno' => $brand->diseno,
                'resumen' => $brand->resumen,
                'pais' => $brand->pais
            ]
        ], 200);
    }

    public function destroy($product_id)
    {
        Wishlist::where('user_id', auth('api')->user()->id)->where('product_id', $product_id)->delete();

        return response()->json([
            'success' => true,
            'message' => translate('Producto eliminado de MI LISTA')
        ], 200);
    }

    public function destroyService($services_id)
    {
        Wishlist::where('user_id', auth('api')->user()->id)->where('services_id', $services_id)->delete();

        return response()->json([
            'success' => true,
            'message' => translate('Service is successfully removed from your wishlist')
        ], 200);
    }

    public function destroyBrand($brands_id)
    {
        Wishlist::where('user_id', auth('api')->user()->id)->where('brands_id', $brands_id)->delete();

        return response()->json([
            'success' => true,
            'message' => translate('Brand is successfully removed from your wishlist')
        ], 200);
    }
}
