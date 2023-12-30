<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\CartCollection;
use App\Http\Resources\ShopCollection;
use App\Http\Resources\ShopResource;
use App\Models\Brand;
use App\Models\Cart;
use App\Models\Collection;
use App\Models\CollectionProduct;
use App\Models\CollectionVariation;
use App\Models\Product;
use App\Models\ProductVariation;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    public function index(Request $request)
    {
        if (auth('api')->check()) {
            $product_carts = Cart::with(['product', 'variation.combinations.attribute', 'variation.combinations.attribute_value'])
                ->where('user_id', auth('api')->user()->id)
                ->whereNotNull('product_id')
                ->get();
            $collection_carts = Cart::with(['collection', 'variation'])
                ->where('user_id', auth('api')->user()->id)
                ->whereNotNull('collection_id')
                ->get();
        } elseif ($request->has('temp_user_id') && $request->temp_user_id) {
            $product_carts = Cart::with(['product', 'variation.combinations.attribute', 'variation.combinations.attribute_value'])
                ->where('temp_user_id', $request->temp_user_id)
                ->whereNotNull('product_id')
                ->get();
            $collection_carts = Cart::with(['collection', 'variation'])
                ->where('temp_user_id', $request->temp_user_id)
                ->whereNotNull('collection_id')
                ->get();
        } else {
            $product_carts = collect();
            $collection_carts = collect();
        }

        $product_carts = $product_carts->filter(function ($cart_item) {
            return $cart_item->variation && $cart_item->product;
        });

        $carts = $product_carts->merge($collection_carts);

        $product_shops = $product_carts->pluck('product.shop_id')->unique()->toArray();
        $collection_shops = $collection_carts->pluck('collection.shop_id')->unique()->toArray();

        $shops = array_unique(array_merge($product_shops, $collection_shops));

        return response()->json([
            'success' => true,
            'cart_items' => new CartCollection($carts),
            'shops' => new ShopCollection(Shop::with('categories')
                ->withCount([
                    'products',
                    'collections',
                    'reviews'
                ])->find($shops))
        ]);
    }

    public function add(Request $request)
    {
        $product_type = 'product';

        if ($request->collection_id) {
            $product_variation = CollectionVariation::with(['collection.shop'])->findOrFail($request->variation_id);
            $product_type = 'collection';
        } else {
            $product_variation = ProductVariation::with(['product.shop', 'combinations.attribute', 'combinations.attribute_value'])->findOrFail($request->variation_id);
        }

        $user_id = (auth('api')->check()) ? auth('api')->user()->id : null;
        $temp_user_id = $request->temp_user_id;

        $cart = Cart::updateOrCreate([
            'user_id' => $user_id,
            'temp_user_id' => $temp_user_id,
            $product_type . '_id' => $product_variation->$product_type->id,
            $product_type . '_variation_id' => $product_variation->id
        ], [
            'quantity' => DB::raw('quantity + ' . $request->qty)
        ]);

        $item = [
            'cart_id' => (int) $cart->id,
            $product_type . '_id' => (int) $cart[$product_type . '_id'],
            'shop_id' => (int) $product_variation->$product_type->shop_id,
            'earn_point' => (float) $cart->$product_type->earn_point,
            'variation_id' => (int) $cart[$product_type . '_variation_id'],
            'name' => $product_variation->$product_type->name,
            'combinations' => filter_variation_combinations($product_variation->combinations),
            'thumbnail' => api_asset($product_variation->$product_type->thumbnail_img),
            'regular_price' => (float) variation_price($product_variation->$product_type, $product_variation, false),
            'dicounted_price' => (float) variation_discounted_price($product_variation->$product_type, $product_variation),
            'tax' => (float) product_variation_tax($product_variation->$product_type, $product_variation),
            'stock' => (int) $product_variation->stock,
            'min_qty' => (int) $product_variation->$product_type->min_qty,
            'max_qty' => (int) $product_variation->$product_type->max_qty,
            'standard_delivery_time' => (int) $product_variation->$product_type->standard_delivery_time,
            'express_delivery_time' => (int) $product_variation->$product_type->express_delivery_time,
            'qty' => (int) $request->qty,
        ];

        return response()->json([
            'success' => true,
            'data' => $item,
            'shop' => new ShopResource($product_variation->$product_type->shop),
            'message' => translate(ucfirst($product_type) . ' added to cart successfully'),
        ], 200);
    }

    public function changeQuantity(Request $request)
    {
        $isCollection = false;

        if (isset($request->isCollection)) {
            $isCollection = $request->isCollection;
        }

        if ($isCollection == false) {
            $cart = Cart::find($request->cart_id);
            if ($cart != null) {
                if ((auth('api')->check() && auth('api')->user()->id == $cart->user_id) || ($request->has('temp_user_id') && $request->temp_user_id == $cart->temp_user_id)) {

                    if ($request->type == 'plus' && ($cart->product->max_qty == 0 || $cart->quantity < $cart->product->max_qty)) {
                        $cart->update([
                            'quantity' => DB::raw('quantity + 1')
                        ]);
                        return response()->json([
                            'success' => true,
                            'message' => translate('Cart updated')
                        ]);
                    } elseif ($request->type == 'plus' && $cart->quantity == $cart->product->max_qty) {
                        return response()->json([
                            'success' => false,
                            'message' => translate('Max quantity reached')
                        ]);
                    } elseif ($request->type == 'minus' && $cart->quantity > $cart->product->min_qty) {
                        $cart->update([
                            'quantity' => DB::raw('quantity - 1')
                        ]);
                        return response()->json([
                            'success' => true,
                            'message' => translate('Cart updated')
                        ]);
                    } elseif ($request->type == 'minus' && $cart->quantity == $cart->product->min_qty) {
                        $cart->delete();
                        return response()->json([
                            'success' => true,
                            'message' => translate('Cart deleted due to minimum quantity')
                        ]);
                    }
                    return response()->json([
                        'success' => false,
                        'message' => translate('Something went wrong')
                    ]);
                } else {
                    return response()->json(null, 401);
                }
            }
        } else {
        }
    }

    public function destroy(Request $request)
    {
        $cart = Cart::find($request->cart_id);

        if ($cart != null) {
            if ((auth('api')->check() && auth('api')->user()->id == $cart->user_id) || ($request->has('temp_user_id') && $request->temp_user_id == $cart->temp_user_id)) {
                $cart->delete();
                return response()->json([
                    'success' => true,
                    'message' => translate('Product has been successfully removed from your cart')
                ], 200);
            } else {
                return response()->json(null, 401);
            }
        }
    }
}
