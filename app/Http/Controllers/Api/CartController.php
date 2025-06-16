<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\CartCollection;
use App\Http\Resources\ShopCollection;
use App\Http\Resources\ShopResource;
use App\Models\Brand;
use App\Models\Cart;
use App\Models\Collection;
use App\Models\CollectionProduct;
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
            $collection_carts = Cart::with(['collection'])
                ->where('user_id', auth('api')->user()->id)
                ->whereNotNull('collection_id')
                ->get();
        } elseif ($request->has('temp_user_id') && $request->temp_user_id) {
            $product_carts = Cart::with(['product', 'variation.combinations.attribute', 'variation.combinations.attribute_value'])
                ->where('temp_user_id', $request->temp_user_id)
                ->whereNotNull('product_id')
                ->get();
            $collection_carts = Cart::with(['collection'])
                ->where('temp_user_id', $request->temp_user_id)
                ->whereNotNull('collection_id')
                ->get();
        } else {
            $product_carts = collect();
            $collection_carts = collect();
        }

        $product_carts = $product_carts->filter(function ($cart_item) {
            return $cart_item->product;
        });

        $carts = $product_carts->merge($collection_carts);

        $product_shops = $product_carts->pluck('product.shop_id')->unique()->toArray();
        $collection_shops = $collection_carts->pluck('collection.shop_id')->unique()->toArray();

        $shops = array_unique(array_merge($product_shops, $collection_shops));

        return response()->json([
            'success' => true,
            'cart_items' => new CartCollection($carts)
        ]);
    }

    public function add(Request $request)
    {
        $data = $request->product_id;

        $productToSell = Product::findOrFail($data['id']);

        $user_id = (auth('api')->check()) ? auth('api')->user()->id : null;
        $temp_user_id = $request->temp_user_id;

        $cart = Cart::updateOrCreate([
            'user_id' => $user_id,
            'temp_user_id' => $temp_user_id,
            'product_id' => $productToSell['id'],
            'quantity' => (int) $request->qty,
        ]);

        $product = [
            'cart_id' => (int) $cart->id,
            'product_id' => (int) $cart->product_id,
            'shop_id' => (int) $productToSell->shop_id,
            'earn_point' => (float) $cart->product->earn_point,
            'name' => $productToSell->name,
            'thumbnail_image' => $productToSell->thumbnail_img,
            'regular_price' => (float) variation_price($productToSell, $productToSell),
            'dicounted_price' => (float) variation_discounted_price($productToSell, $productToSell),
            'stock' => (int) $productToSell->stock,
            'min_qty' => (int) $productToSell->min_qty,
            'max_qty' => (int) $productToSell->max_qty,
            'standard_delivery_time' => (int) $productToSell->standard_delivery_time,
            'express_delivery_time' => (int) $productToSell->express_delivery_time,
            'qty' => (int) $request->qty,
        ];

        return response()->json([
            'success' => true,
            'data' => $product,
            'message' => translate('Product added to cart successfully'),
        ], 200);
    }

    public function addCollection(Request $request)
    {
        $collection = Collection::findOrFail($request->variation_id);
        $user_id = (auth('api')->check()) ? auth('api')->user()->id : null;

        return response()->json([
            'success' => true,
            'message' => translate('Collection added to cart successfully'),
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

            if (!$cart) {
                return response()->json([
                    'success' => false,
                    'message' => translate('Cart not found'),
                ]);
            }

            $isOwner = (auth('api')->check() && auth('api')->user()->id == $cart->user_id);
            $isTempOwner = $request->has('temp_user_id') && $request->temp_user_id == $cart->temp_user_id;

            if (!$isOwner && !$isTempOwner) {
                return response()->json(null, 401);
            }

            $type = $request->type;
            $validTypes = ['set', 'plus', 'minus'];

            if (!in_array($type, $validTypes)) {
                return response()->json([
                    'success' => false,
                    'message' => translate('Invalid operation type'),
                ]);
            }

            $minQty = $cart->product->min_qty ?? 1;
            $maxQty = $cart->product->max_qty ?? 0;

            if ($type === 'set') {
                $newQty = intval($request->qty);

                if ($newQty < $minQty || ($maxQty > 0 && $newQty > $maxQty)) {
                    return response()->json([
                        'success' => false,
                        'message' => translate('Invalid quantity'),
                    ]);
                }

                $cart->update(['quantity' => $newQty]);

                return response()->json([
                    'success' => true,
                    'message' => translate('Cart updated'),
                ]);
            }

            if ($type === 'plus') {
                if ($maxQty > 0 && $cart->quantity >= $maxQty) {
                    return response()->json([
                        'success' => false,
                        'message' => translate('Max quantity reached'),
                    ]);
                }

                $cart->update(['quantity' => DB::raw('quantity + 1')]);

                return response()->json([
                    'success' => true,
                    'message' => translate('Cart updated'),
                ]);
            }

            if ($type === 'minus') {
                if ($cart->quantity > $minQty) {
                    $cart->update(['quantity' => DB::raw('quantity - 1')]);

                    return response()->json([
                        'success' => true,
                        'message' => translate('Cart updated'),
                    ]);
                } elseif ($cart->quantity == $minQty) {
                    $cart->delete();

                    return response()->json([
                        'success' => true,
                        'message' => translate('Cart deleted due to minimum quantity'),
                    ]);
                }
            }

            return response()->json([
                'success' => false,
                'message' => translate('Something went wrong'),
            ]);
        } else {
            //
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
