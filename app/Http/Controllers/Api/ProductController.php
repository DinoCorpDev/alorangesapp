<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\AttributeCollection;
use App\Http\Resources\BrandCollection;
use App\Http\Resources\CategoryCollection;
use App\Http\Resources\CategorySingleCollection;
use App\Http\Resources\ProductCollection;
use App\Http\Resources\ProductSingleCollection;
use App\Http\Resources\ShopCollection;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Attribute;
use App\Models\AttributeCategory;
use App\Models\OrderDetail;
use App\Models\Shop;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use App\Utility\CategoryUtility;

use App\Http\Services\AlegraServices;
use App\Http\Services\WompiServices;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function __construct(){
        $wompiData = (new WompiServices)->getAceptanceToken();
        $token = $wompiData['presigned_acceptance'];
        $this->acceptance_token = $wompiData['presigned_acceptance']['acceptance_token'];
        $this->signatureWompi = 'prod_integrity_h9ukTOEnWfo9EM3hkTLCR6XiEpRGCfG5';
        //$this->signatureWompi = 'test_integrity_uKHYzUy57fASMOf8nmdVOB4aeBhgjYyn';
    }

    public function index()
    {
        return new ProductCollection(Product::latest()->paginate(10));
    }

    public function show($product_slug)
    {
        $product = filter_products(Product::query())
            ->where('slug', $product_slug)
            ->with(['brand', 'variations', 'variation_combinations', 'shop' => function ($query) {
                $query->withCount('reviews');
            }])
            ->withCount(['reviews', 'reviews_1', 'reviews_2', 'reviews_3', 'reviews_4', 'reviews_5'])
            ->first();
        if ($product) {
            return new ProductSingleCollection($product);
        } else {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => translate('Product not found')
            ]);
        }
    }

    public function get_by_ids(Request $request)
    {
        if ($request->has('product_ids') && is_array($request->product_ids)) {
            return new ProductCollection(filter_products(Product::whereIn('id', $request->product_ids))->get());
        } else {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => translate('Products not found')
            ], 200);
        }
    }

    public function related($product_id)
    {
        $products = filter_products(Product::query())->whereHas('product_categories', function ($query) use ($product_id) {
            $query->whereIn('category_id', Product::find($product_id)->product_categories->pluck('category_id')->toArray());
        })->where('id', '!=', $product_id)->limit(10)->get();
        return new ProductCollection($products);
    }

    public function bought_together($product_id)
    {
        $order_ids = OrderDetail::where('product_id', $product_id)->pluck('order_id')->toArray();
        $product_ids = OrderDetail::distinct()->whereIn('order_id', $order_ids)->where('product_id', '!=', $product_id)->pluck('product_id')->toArray();
        $products = filter_products(Product::whereIn('id', $product_ids))->limit(10)->get();
        return new ProductCollection($products);
    }

    public function random_products($limit, $product_id = null)
    {
        return new ProductCollection(filter_products(Product::where('id', '!=', $product_id))->inRandomOrder()->limit($limit)->get());
    }

    public function latest_products($limit)
    {
        return new ProductCollection(filter_products(Product::query())->latest()->limit($limit)->get());
    }

    function buscarObjetosPorLetra($array, $letra) {
        $resultados = [];
        foreach ($array as $objeto) {
            if (isset($objeto->name) && strtoupper($objeto->name[0]) === strtoupper($letra)) {
                $resultados[] = $objeto;
            }
        }
        return $resultados;
    }

    public function search(Request $request){
        $products = [];

        if ($request->form == 'search') {
            if ($request->keyword) {
                $products = Product::where('name','LIKE','%'.$request->keyword.'%')->get();
            }

            $collection = new ProductCollection($products);
            
            return response()->json([
                'success' => true,
                'products' => $collection,
            ]);
        }

        $category = Category::where('name','=',$request->category_slug)->first();
        if (empty($category)) {
            return response()->json([
                'success' => true,
                'products' => $products,
            ]);
        }
        
        $productsCategories = ProductCategory::where('category_id',$category['id'])->with('product')->get();
        
        foreach ($productsCategories as $key => $prodCat) {
            array_push($products,$prodCat->product);
        }

        if ($request->keyword) {
            $products = $this->buscarObjetosPorLetra($products,$request->keyword);
        }

        $collection = new ProductCollection($products);

        return response()->json([
            'success' => true,
            'products' => $collection,
        ]);
    }

    public function searchOld(Request $request)
    {
        $category                   = $request->category_slug ? Category::where('name', $request->category_slug)->first() : null;
        $search_keyword             = $request->keyword;
        $sort_by                    = $request->sort_by;
        $category_id                = optional($category)->id;
        $brand_ids                  = $request->brand_ids ? explode(',', $request->brand_ids) : null;
        $min_price                  = $request->min_price;
        $max_price                  = $request->max_price;
        $attributes                 = Attribute::with('attribute_values')->get();
        $selected_attribute_values  = $request->attribute_values ? explode(',', $request->attribute_values) : null;

        $products = filter_products(Product::with(['variations']));

        //brand check
        if ($brand_ids != null) {
            $products->whereIn('brand_id', $brand_ids);
        }

        // search keyword check
        if ($search_keyword != null) {
            $products->where('name', 'like', $search_keyword.'%')->get();
        }

        // category + child category check
        if ($category_id != null) {

            $category_ids = CategoryUtility::children_ids($category_id);
            $category_ids[] = $category_id;

            $products->with('product_categories')->whereHas('product_categories', function ($query) use ($category_ids) {
                return $query->whereIn('category_id', $category_ids);
            });

            $attribute_ids = AttributeCategory::whereIn('category_id', $category_ids)->pluck('attribute_id')->toArray();
            $attributes = Attribute::with('attribute_values')->whereIn('id', $attribute_ids)->get();
        } else {
            $category_ids = [];
            if ($search_keyword != null) {
                foreach (explode(' ', trim($search_keyword)) as $word) {
                    $ids = Category::where('name', 'like', '%' . $word . '%')->pluck('id')->toArray();
                    if (count($ids) > 0) {
                        foreach ($ids as $id) {
                            $category_ids[] = $id;
                            array_merge($category_ids, CategoryUtility::children_ids($id));
                        }
                    }
                }

                $attribute_ids = AttributeCategory::whereIn('category_id', $category_ids)->pluck('attribute_id')->toArray();
                $attributes = Attribute::with('attribute_values')->whereIn('id', $attribute_ids)->get();
            }
        }

        //price range
        if ($min_price != null) {
            $products->where('lowest_price', '>=', $min_price);
        }

        if ($max_price != null) {
            $products->where('highest_price', '<=', $max_price);
        }

        //filter by attribute value
        if ($selected_attribute_values) {
            $products->with('attribute_values')->whereHas('attribute_values', function ($query) use ($selected_attribute_values) {
                return $query->whereIn('attribute_value_id', $selected_attribute_values);
            });
        }

        //sorting
        switch ($sort_by) {
            case 'latest':
                $products->orderBy('created_at', 'desc');
                break;
            case 'oldest':
                $products->orderBy('created_at', 'asc');
                break;
            case 'highest_price':
                $products->orderBy('highest_price', 'desc');
                break;
            case 'lowest_price':
                $products->orderBy('lowest_price', 'asc');
                break;
            default:
                $products->orderBy('num_of_sale', 'desc');
                break;
        }

        $collection = new ProductCollection($products->paginate(20));
        $products = $collection['data'];
        
        return response()->json([
            'success' => true,
            'metaTitle' => $category ? $category->meta_title : get_setting('meta_title'),
            'products' => $collection,
            'totalPage' => $collection->lastPage(),
            'currentPage' => $collection->currentPage(),
            'total' => $collection->total(),
            'parentCategory' => $category && $category->parent_id != 0 ? new CategorySingleCollection(Category::find($category->parent_id)) : null,
            'currentCategory' => $category ? new CategorySingleCollection($category) : null,
            'childCategories' => $category ? new CategoryCollection($category->childrenCategories) : null,
            'rootCategories' => new CategoryCollection(Category::where('level', 0)->orderBy('order_level', 'desc')->get()),
            'allBrands' => new BrandCollection(Brand::all()),
            'attributes' => new AttributeCollection($attributes)
        ]);
    }

    public function ajax_search($search_keyword)
    {
        $keywords = array();
        $products = Product::where('published', 1)->where('approved', 1)->where('tags', 'like', '%' . $search_keyword . '%')->get();

        foreach ($products as $key => $product) {
            foreach (explode(',', $product->tags) as $key => $tag) {
                if (stripos($tag, $search_keyword) !== false) {
                    if (sizeof($keywords) > 5) {
                        break;
                    } else {
                        if (!in_array(strtolower($tag), $keywords)) {
                            array_push($keywords, strtolower($tag));
                        }
                    }
                }
            }
        }

        $products_query = filter_products(Product::query());
        $products_query = $products_query->where('published', 1)->where('approved', 1)
            ->where(function ($q) use ($search_keyword) {
                foreach (explode(' ', trim($search_keyword)) as $word) {
                    $q->where('name', 'like', '%' . $word . '%')
                        ->orWhere('tags', 'like', '%' . $word . '%')
                        ->orWhereHas('product_translations', function ($q) use ($word) {
                            $q->where('name', 'like', '%' . $word . '%');
                        })
                        ->orWhereHas('variations', function ($q) use ($word) {
                            $q->where('sku', 'like', '%' . $word . '%');
                        });
                }
            });


        $case1 = $search_keyword . '%';
        $case2 = '%' . $search_keyword . '%';

        $products_query->orderByRaw("CASE 
                WHEN name LIKE '$case1' THEN 1 
                WHEN name LIKE '$case2' THEN 2 
                ELSE 3 
                END");

        $products = new ProductCollection($products_query->limit(3)->get());

        $categories = Category::where('level', 0)->where('name', 'like', '%' . $search_keyword . '%')->get()->take(3);
        $brands = Brand::where('name', 'like', '%' . $search_keyword . '%')->get()->take(3);
        $shops = new ShopCollection(filter_shops(Shop::where('name', 'like', '%' . $search_keyword . '%')->get()->take(3)));

        if (sizeof($keywords) > 0 || sizeof($categories) > 0 || sizeof($products) > 0 || sizeof($shops) > 0 || sizeof($brands) > 0) {
            return response()->json([
                'success' => true,
                'keywords' => $keywords,
                'categories' => $categories,
                'brands' => $brands,
                'products' => $products,
                'shops' => $shops,
            ]);
        } else {
            return response()->json([
                'success' => false
            ]);
        }
    }

    public function productComparedList(Request $request)
    {
        $products = Product::whereIn('id', $request->data)->get();
        $products_array = array();

        foreach ($products as $product) {
            $products_array['name'][] = $product->name;
            $products_array['image'][] = api_asset($product->thumbnail_img);

            if ($product->lowest_price != $product->highest_price) {
                $products_array['price'][] = format_price($product->lowest_price) . "-" . format_price($product->highest_price);
            } else {
                $products_array['price'][] = format_price($product->lowest_price);
            }

            $products_array['brand'][] = $product->brand->name ?? "none";
            $products_array['Shop'][] = $product->shop->name ?? "none";
            $products_array['slug'][] = $product->slug;
            $products_array['id'][] = $product->id;
        }
        return response()->json([
            'success' => true,
            'specifications' => $products_array,
        ]);
    }

    public function wompiPaymentCard(Request $request){
        try {
            $wompiTokenizeCard = (new WompiServices)->tokenizeCard($request['cardData']);
            $mount = $request->mount;
            $currency = $request->currency;
            $reference = $request->reference;
            $installments = null;
            if (isset($request['cardData']['installments'])) {
                $installments = $request['cardData']['installments'];
            }else{
                $installments = 1;
            }
            $llaveConcatenada = $reference.$mount.$currency.$this->signatureWompi;
            $payment_information = [
                "acceptance_token" => $this->acceptance_token,
                "amount_in_cents" => $mount,
                "currency" => $currency,
                "signature" => hash("sha256",$llaveConcatenada),
                "customer_email" => $request->customer_email,
                "payment_method" => [
                    "type" => "CARD",
                    "token" => $wompiTokenizeCard['data']['id'],
                    "installments" => $installments, //Numero de cuotas
                ],
                // "redirect_url" => "https =>//mitienda.com.co/pago/resultado",
                "reference" => $reference,
                // "expiration_time" => "2023-06-09T20 =>28 =>50.000Z",
                "customer_data" => $request['customer_data'],
                "shipping_address" => $request['shipping_address'],
            ];

            $PaymentResult = (new WompiServices)->wompiTransaction($payment_information);

            return response()->json([
                'success' => true,
                'PaymentResult' => $PaymentResult,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'PaymentResult' => 'Verifica los datos e intenta nuevamente',
            ]);
        }
    }

    public function getPSEBanksOptions(){
        $wompiData = (new WompiServices)->wompiPSEPayments();
        return response()->json([
            'success' => true,
            'banks' => $wompiData,
        ]);
    }

    public function getTransactionWompi(Request $request){
        $transactionResult = (new WompiServices)->wompiGetResultTransaction($request->id);
        return response()->json([
            'success' => true,
            'TransactionResult' => $transactionResult,
        ]);
    }

    public function wompiPaymentPSE(Request $request){
        $mount = $request->mount;
        $currency = $request->currency;
        $reference = $request->reference;
        $llaveConcatenada = $reference.$mount.$currency.$this->signatureWompi;      
        $payment_information = [
            "acceptance_token" => $this->acceptance_token,
            "amount_in_cents" => $mount,
            "currency" => $currency,
            "signature" => hash("sha256",$llaveConcatenada),
            "customer_email" => $request->customer_email,
            "payment_method" => [
                "type" => $request['payment_method']['type'],
                "user_type" => $request['payment_method']['user_type'],
                "user_legal_id_type" => $request['payment_method']['user_legal_id_type'],
                "user_legal_id" => $request['payment_method']['user_legal_id'],
                "financial_institution_code" => $request['payment_method']['financial_institution_code'],
                "payment_description" => $request['payment_method']['payment_description'],
            ],
            //"redirect_url" => "http://alorangesapp.test/user/checkout",
            "reference" => $reference,
            // "expiration_time" => "2023-06-09T20 =>28 =>50.000Z",
            "customer_data" => $request['customer_data'],
            //"shipping_address" => $request['shipping_address'],
        ];
        
        $PaymentResult = (new WompiServices)->wompiTransaction($payment_information);
        
        return response()->json([
            'success' => true,
            'PaymentResult' => $PaymentResult,
        ]);
    }

    public function alegra(){
        $products = [];
        $counter = 0;
        $categoryId = 0;

        Product::truncate();
        ProductCategory::truncate();

        try {
            $categories = Category::all();
            foreach($categories as $category){
                $products = (new AlegraServices)->getProductsByCategory($category['id']);
                foreach($products as $product){
                    $productStorage = Product::where('id', $product['id'])->first();
                    if(empty($productStorage)){
                        $productStorage = new Product;
                    }
                    $productStorage->id = $product['id'];
                    $productStorage->name = $product['name'];
                    $productStorage->reference = $product['reference'];
                    $productStorage->description = $product['description'];
                    $percentage = $product['tax'] ? $product['tax'][0]['percentage'] : 0;
                    $price = 0;
                    foreach ($product['price'] as $key => $listPrices) {
                        if ($listPrices['name'] === 'PUNTO DE VENTA') {
                            $finalPrice = (int)$listPrices['price'];
                            if ($percentage > 0) {
                                $percentageToPrice = (int)$percentage / 100 + 1;
                            }else{
                                $percentageToPrice = 1;
                            }
                            
                            $total = $finalPrice * $percentageToPrice;
                            $totalSinCentavos = floor($total);
                            $price = $totalSinCentavos;
                        }
                    }
                    $productStorage->tax = $percentage;
                    $productStorage->lowest_price = $price;
                    $productStorage->highest_price = $price;
                    $productStorage->description = $product['description'];
                    $productStorage->shop_id = 1;
                    $productStorage->slug = Str::slug($product['name'], '-') . '-' . strtolower(Str::random(5));
                    $productStorage->published = $product['status'] == 'active' ? 1 : 0;
                    
                    if (isset($product['images']) && count($product['images']) > 0 ) {
                        $productStorage->thumbnail_img = $product['images'][0]['url'];
                    }else{
                        $productStorage->thumbnail_img = '';
                    }
                    
                    $images = $product['images'] ?? [];

                    foreach($images as $index => $image)
                    {
                        $productStorage['imagen'.($index+1)] = $image['url'];
                    }

                    $productCategories = ProductCategory::where('product_id',$product['id'])->first();
                    if(empty($productCategories)){
                        $productCategories = new ProductCategory;
                    }
                    $productCategories->product_id = $product['id'];
                    $productCategories->category_id = $category['id'];
                    $productCategories->created_at = now();
                    $productCategories->updated_at = now();

                    $productCategories->save();
                    $productStorage->save();

                    $productStorage->categories()->sync([$category['id']]);

                    $counter++;
                    $categoryId = $product['id'];
                }
            }
            $url = config('app.url').'/admin/product';
            return redirect($url)->with('Actualizado', 'Los productos han sido actualizados correctamente');
        } catch (Exception $e) {
            $error_code = $e->errorInfo[1];
            $categoryId = $e->errorInfo[1];
            return back()->withErrors('There was a problem uploading the data!');
        }
    }

    public function products_by_letter($letter) {
        $products = Product::where('published', 1)
            ->where('approved', 1)
            ->where('name', 'like', $letter . '%')
            ->get();

        return response()->json([
           'success' => true,
            'products' => $products,
        ]);
    }

    private function updateProductsFromAlegra($search_keyword){
        $updateProducts = (new AlegraServices)->getProductsByQuery($search_keyword);
        foreach($updateProducts as $product){
            $productStorage = Product::where('reference', $product['reference'])->first();
            if(empty($productStorage)){
                $productStorage = new Product;
            }
            $productStorage->id = $product['id'];
            $productStorage->name = $product['name'];
            $productStorage->reference = $product['reference'];
            $productStorage->description = $product['description'];
            $price = array_filter($product['price'], function ($object) {
                return $object['name'] == 'PRECIOS APPWEB';
            });
            $priceValue = reset($price);
            $priceWithIva = ($priceValue['price'] * $product['tax'][0]['percentage']/100) + $priceValue['price'];
            $productStorage->lowest_price = $priceWithIva;
            $productStorage->highest_price = $priceWithIva;
            $productStorage->description = $product['description'];
            $productStorage->shop_id = 1;
            $productStorage->slug = Str::slug($product['name'], '-') . '-' . strtolower(Str::random(5));
            $productStorage->published = $product['status'] == 'active' ? 1 : 0;

            $images = $product['images'] ?? [];

            foreach($images as $index => $image){
                $productStorage['imagen'.($index+1)] = $image['url'];
            }

            $productStorage->save();

            $productStorage->categories()->sync([$product['category']['id']]);
        }
    }
}