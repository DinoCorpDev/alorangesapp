<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductTranslation;
use App\Models\ProductVariation;
use App\Models\ProductVariationCombination;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeValue;
use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Category;
use App\Models\ProductTax;
use App\Models\ShopBrand;
use App\Models\Brand;
use App\Models\User;
use App\Utility\CategoryUtility;
use CoreComponentRepository;
use Artisan;
use Carbon\Carbon;
use App\Models\Upload;
use App\Jobs\SyncAlegraProductsJob;
use Illuminate\Support\Facades\Cache;
use Auth;

use PhpOffice\PhpSpreadsheet\Reader\Exception;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:show_products'])->only('index');
        $this->middleware(['permission:add_products'])->only('create');
        $this->middleware(['permission:view_products'])->only('show');
        $this->middleware(['permission:edit_products'])->only('edit');
        $this->middleware(['permission:duplicate_products'])->only('duplicate');
        $this->middleware(['permission:delete_products'])->only('destroy');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index(Request $request)
    {
        $col_name = null;
        $query = null;
        $sort_search = null;

        $products = Product::select('products.*')
            ->with(['categories' => function ($q) {
                $q->orderBy('order_level');
            }])
            ->leftJoin('product_categories', 'product_categories.product_id', '=', 'products.id')
            ->leftJoin('categories', 'categories.id', '=', 'product_categories.category_id')
            ->where('products.shop_id', auth()->user()->shop_id)
            ->whereNotNull('categories.id')
            ->groupBy('products.id');

        if ($request->search != null) {
            $products = $products->where('products.name', 'like', '%' . $request->search . '%');
            $sort_search = $request->search;
        }

        if ($request->type != null) {
            $var = explode(",", $request->type);
            $col_name = $var[0];
            $query = $var[1];
            $products = $products->orderBy('products.' . $col_name, $query);
            $sort_type = $request->type;
        } else {
            // Group products by their category's Ordering Number, ascending.
            $products = $products->orderByRaw('MAX(categories.order_level) ASC')
                ->orderBy('products.created_at', 'desc');
        }

        $products = $products->paginate(15);
        $type = 'All';

        $uncategorizedCount = Product::where('shop_id', auth()->user()->shop_id)
            ->whereDoesntHave('categories')
            ->count();

        return view('backend.product.products.index', compact('products', 'type', 'col_name', 'query', 'sort_search', 'uncategorizedCount'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = Category::where('level', 0)->get();
        $attributes = Attribute::get();

        return view('backend.product.products.create', compact('categories', 'attributes'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        CoreComponentRepository::instantiateShopRepository();

        if ($request->has('is_variant') && !$request->has('variations')) {
            flash(translate('Invalid product variations'))->error();
            return redirect()->back();
        }

        $product                    = new Product;
        $product->name              = $request->name;
        $product->shop_id           = auth()->user()->shop_id;
        $product->brand_id          = $request->brand_id;
        $product->unit              = $request->unit;
        $product->min_qty           = $request->min_qty;
        $product->max_qty           = $request->max_qty;
        $product->photos            = $request->photos;
        $product->thumbnail_img     = $request->thumbnail_img;
        $product->description       = $request->description;
        $product->published         = $request->status;

        // SEO meta
        $product->meta_title        = (!is_null($request->meta_title)) ? $request->meta_title : $product->name;
        $product->meta_description  = (!is_null($request->meta_description)) ? $request->meta_description : strip_tags($product->description);
        $product->meta_image        = (!is_null($request->meta_image)) ? $request->meta_image : $product->thumbnail_img;
        $product->slug              = Str::slug($request->name, '-') . '-' . strtolower(Str::random(5));

        // warranty
        $product->has_warranty      = $request->has('has_warranty') && $request->has_warranty == 'on' ? 1 : 0;

        // tag
        $tags                       = array();

        if ($request->tags != null) {
            foreach (json_decode($request->tags) as $key => $tag) {
                array_push($tags, $tag->value);
            }
        }

        $product->tags              = implode(',', $tags);

        // lowest highest price
        if ($request->has('is_variant') && $request->has('variations')) {
            $product->lowest_price  =  min(array_column($request->variations, 'price'));
            $product->highest_price =  max(array_column($request->variations, 'price'));
        } else {
            $product->lowest_price  =  $request->price;
            $product->highest_price =  $request->price;
        }

        // stock based on all variations
        $product->stock             = ($request->has('is_variant') && $request->has('variations')) ? max(array_column($request->variations, 'stock')) : $request->stock;

        // discount
        $product->discount          = $request->discount;
        $product->discount_type     = $request->discount_type;

        if ($request->date_range != null) {
            $date_var               = explode(" to ", $request->date_range);
            $product->discount_start_date = strtotime($date_var[0]);
            $product->discount_end_date   = strtotime($date_var[1]);
        }

        // Club Point
        if (get_setting('club_point')) {
            $product->earn_point = $request->earn_point;
        }

        // Product essential information
        $product->reference                 = $request->reference;
        $product->currency                  = $request->currency ?? null;
        $product->warranty_text             = $request->warranty_text;
        
        // Shipping info
        $product->standard_delivery_time    = $request->standard_delivery_time;
        $product->express_delivery_time     = $request->express_delivery_time;
        $product->weight                    = $request->weight;
        $product->height                    = $request->height;
        $product->length                    = $request->length;
        $product->width                     = $request->width;

        $product->save();

        // Product Translations
        $lang = env('DEFAULT_LANGUAGE') ?: 'en';
        $product_translation = ProductTranslation::firstOrNew(['lang' => $lang, 'product_id' => $product->id]);
        $product_translation->name = $request->name;
        $product_translation->unit = $request->unit;
        $product_translation->description = $request->description;
        $product_translation->save();

        // category
        $product->categories()->sync($request->category_ids);

        // shop category ids
        $shop_category_ids = [];

        foreach ($request->category_ids ?? [] as $id) {
            $shop_category_ids[] = CategoryUtility::get_grand_parent_id($id);
        }

        $shop_category_ids =  array_merge(array_filter($shop_category_ids), $product->shop->shop_categories->pluck('category_id')->toArray());
        $product->shop->categories()->sync($shop_category_ids);

        // shop brand
        if ($request->brand_id) {
            ShopBrand::updateOrCreate([
                'shop_id' => $product->shop_id,
                'brand_id' => $request->brand_id,
            ]);
        }

        //taxes
        $tax_data = array();
        $tax_ids = array();

        if ($request->has('taxes')) {
            foreach ($request->taxes as $key => $tax) {
                array_push($tax_data, [
                    'tax' => $tax,
                    'tax_type' => $request->tax_types[$key]
                ]);
            }
            $tax_ids = $request->tax_ids;
        }

        $taxes = array_combine($tax_ids, $tax_data);

        $product->product_taxes()->sync($taxes);

        // product variation
        $product->is_variant        = ($request->has('is_variant') && $request->has('variations')) ? 1 : 0;

        if ($request->has('is_variant') && $request->has('variations')) {
            foreach ($request->variations as $variation) {
                $p_variation              = new ProductVariation;
                $p_variation->product_id  = $product->id;
                $p_variation->code        = $variation['code'];
                $p_variation->price       = $variation['price'];
                $p_variation->stock       = $variation['stock'];
                $p_variation->sku         = $variation['sku'];
                $p_variation->img         = $variation['img'];
                $p_variation->save();

                foreach (array_filter(explode("/", $variation['code'])) as $combination) {
                    $p_variation_comb                         = new ProductVariationCombination;
                    $p_variation_comb->product_id             = $product->id;
                    $p_variation_comb->product_variation_id   = $p_variation->id;
                    $p_variation_comb->attribute_id           = explode(":", $combination)[0];
                    $p_variation_comb->attribute_value_id     = explode(":", $combination)[1];
                    $p_variation_comb->save();
                }
            }
        } else {
            $variation              = new ProductVariation;
            $variation->product_id  = $product->id;
            $variation->sku         = $request->sku;
            $variation->price       = $request->price;
            $variation->stock       = $request->stock;
            $variation->save();
        }

        // attribute
        if ($request->has('product_attributes') && $request->product_attributes[0] != null) {
            foreach ($request->product_attributes as $attr_id) {
                $attribute_values = 'attribute_' . $attr_id . '_values';
                if ($request->has($attribute_values) && $request->$attribute_values != null) {
                    $p_attribute = new ProductAttribute;
                    $p_attribute->product_id = $product->id;
                    $p_attribute->attribute_id = $attr_id;
                    $p_attribute->save();

                    foreach ($request->$attribute_values as $val_id) {
                        $p_attr_value = new ProductAttributeValue;
                        $p_attr_value->product_id = $product->id;
                        $p_attr_value->attribute_id = $attr_id;
                        $p_attr_value->attribute_value_id = $val_id;
                        $p_attr_value->save();
                    }
                }
            }
        }

        $product->save();

        flash(translate('Product has been inserted successfully'))->success();
        return redirect()->route('product.index');
    }

    private function saveArchive($archive)
    {
        $type = array(
            "jpg" => "image",
            "jpeg" => "image",
            "png" => "image",
            "svg" => "image",
            "webp" => "image",
            "gif" => "image",
            "mp4" => "video",
            "mpg" => "video",
            "mpeg" => "video",
            "webm" => "video",
            "ogg" => "video",
            "avi" => "video",
            "mov" => "video",
            "flv" => "video",
            "swf" => "video",
            "mkv" => "video",
            "wmv" => "video",
            "wma" => "audio",
            "aac" => "audio",
            "wav" => "audio",
            "mp3" => "audio",
            "zip" => "archive",
            "rar" => "archive",
            "7z" => "archive",
            "doc" => "document",
            "txt" => "document",
            "docx" => "document",
            "pdf" => "document",
            "csv" => "document",
            "xml" => "document",
            "ods" => "document",
            "xlr" => "document",
            "xls" => "document",
            "xlsx" => "document"
        );

        $carpeta = public_path() . "/uploads/all/";
        if ($archive !== "" && $archive !== null) {
            if (strpos($archive, 'file') !== false) {
                if (@fopen($archive, "r")) {
                    $destino = $carpeta . substr($archive, strrpos($archive, "/") + 1);
                    file_put_contents($destino, file_get_contents($archive));

                    $upload = new Upload;
                    $upload->file_original_name = substr($archive, strrpos($archive, "/") + 1);
                    $upload->file_name = "/uploads/all/" . substr($archive, strrpos($archive, "/") + 1);
                    $upload->user_id = Auth::user()->id;

                    $upload->extension = substr($archive, strrpos($archive, ".") + 1);

                    if (isset($type[$upload->extension])) {
                        $upload->type = $type[$upload->extension];
                    } else {
                        $upload->type = "others";
                    }

                    $upload->file_size = 1024;
                    $upload->save();

                    return $upload->id;
                } else {
                    return "";
                }
            } else {
                return "";
            }
        }
    }

    /**
     * Stores a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function import(Request $request)
    {
        $the_file = $request->file('uploaded_file');

        try {
            $spreadsheet = IOFactory::load($the_file->getRealPath());
            $sheet        = $spreadsheet->getActiveSheet();
            $row_limit    = $sheet->getHighestDataRow();
            $column_limit = $sheet->getHighestDataColumn();
            $row_range    = range(2, $row_limit);
            $column_range = range('O', $column_limit);
            $startcount = 2;
            $data = array();

            // referencia,categoria,subcategoria,marca,precio,descuento,divisa,stock,garantia,envio,consumo,material,medida_de_producto,si1,medida_de_engaste

            foreach ($row_range as $row) {
                $data[] = [
                    'referencia' => $sheet->getCell('A' . $row)->getValue(),
                    'categoria' => $sheet->getCell('B' . $row)->getValue(),
                    'subcategoria' => $sheet->getCell('C' . $row)->getValue(),
                    'marca' => $sheet->getCell('D' . $row)->getValue(),
                    'precio' => preg_replace('/[^0-9]/', '', $sheet->getCell('E' . $row)->getValue()),
                    'descuento' => preg_replace('/[^0-9]/', '', $sheet->getCell('F' . $row)->getValue()),
                    'divisa' => $sheet->getCell('G' . $row)->getValue(),
                    'stock' => preg_replace('/[^0-9]/', '', $sheet->getCell('H' . $row)->getValue()),
                    'garantia' => $sheet->getCell('I' . $row)->getValue(),
                    'envio' => $sheet->getCell('J' . $row)->getValue(),
                    'consumo' => $sheet->getCell('K' . $row)->getValue(),
                    'material' => $sheet->getCell('L' . $row)->getValue(),
                    'medida_de_producto' => $sheet->getCell('M' . $row)->getValue(),
                    'si1' => $sheet->getCell('N' . $row)->getValue(),
                    'medida_de_engaste' => $sheet->getCell('O' . $row)->getValue(),
                    'si2' => $sheet->getCell('P' . $row)->getValue(),
                    'medidas_de_embalaje' => $sheet->getCell('Q' . $row)->getValue(),
                    'si3' => $sheet->getCell('R' . $row)->getValue(),
                    'peso_de_producto' => $sheet->getCell('S' . $row)->getValue(),
                    'si4' => $sheet->getCell('T' . $row)->getValue(),
                    'peso_de_envio' => $sheet->getCell('U' . $row)->getValue(),
                    'tipo_de_coneccion' => $sheet->getCell('W' . $row)->getValue(),
                    'eficiencia' => $sheet->getCell('X' . $row)->getValue(),
                    'caracteristica1' => $sheet->getCell('V' . $row)->getValue(),
                    'caracteristica2' => $sheet->getCell('Z' . $row)->getValue(),
                    'caracteristica3' => $sheet->getCell('AA' . $row)->getValue(),
                    'caracteristica4' => $sheet->getCell('AB' . $row)->getValue(),
                    'caracteristica5' => $sheet->getCell('AC' . $row)->getValue(),
                    'caracteristica6' => $sheet->getCell('AD' . $row)->getValue(),
                    'caracteristica7' => $sheet->getCell('AE' . $row)->getValue(),
                    'caracteristica8' => $sheet->getCell('AF' . $row)->getValue(),
                    'beneficio1' => $sheet->getCell('AG' . $row)->getValue(),
                    'beneficio2' => $sheet->getCell('AH' . $row)->getValue(),
                    'beneficio3' => $sheet->getCell('AI' . $row)->getValue(),
                    'beneficio4' => $sheet->getCell('AJ' . $row)->getValue(),
                    'beneficio5' => $sheet->getCell('AK' . $row)->getValue(),
                    'postventa' => $sheet->getCell('AL' . $row)->getValue(),
                    'imagen1' => $sheet->getCell('AM' . $row)->getValue(),
                    'imagen2' => $sheet->getCell('AN' . $row)->getValue(),
                    'imagen3' => $sheet->getCell('AO' . $row)->getValue(),
                    'imagen4' => $sheet->getCell('AP' . $row)->getValue(),
                    'video' => $sheet->getCell('AQ' . $row)->getValue(),
                    'video2' => $sheet->getCell('AR' . $row)->getValue(),
                    'video3' => $sheet->getCell('AS' . $row)->getValue(),
                    'video4' => $sheet->getCell('AT' . $row)->getValue(),
                    'ficha_tecnica_imagen1' => $sheet->getCell('AU' . $row)->getValue(),
                    'ficha_tecnica_imagen2' => $sheet->getCell('AV' . $row)->getValue(),
                    'ficha_tecnica_imagen3' => $sheet->getCell('AW' . $row)->getValue(),
                    'ficha_tecnica_imagen4' => $sheet->getCell('AX' . $row)->getValue(),
                    'manual_de_producto' => $sheet->getCell('AY' . $row)->getValue(),
                    'ficha_tecnica_del_producto' => $sheet->getCell('AZ' . $row)->getValue(),
                    'manual_de_instalacion' => $sheet->getCell('BA' . $row)->getValue(),
                    'logo' => $sheet->getCell('BB' . $row)->getValue(),
                    'vida_util' => $sheet->getCell('BC' . $row)->getValue(),
                    'plastico' => $sheet->getCell('BD' . $row)->getValue(),
                    'peso_plastico' => $sheet->getCell('BE' . $row)->getValue(),
                    'carton' => $sheet->getCell('BF' . $row)->getValue(),
                    'peso_carton' => $sheet->getCell('BG' . $row)->getValue(),
                    'papel' => $sheet->getCell('BH' . $row)->getValue(),
                    'peso_papel' => $sheet->getCell('BI' . $row)->getValue(),
                    'metal' => $sheet->getCell('BJ' . $row)->getValue(),
                    'peso_metal' => $sheet->getCell('BK' . $row)->getValue(),
                    'vidrio' => $sheet->getCell('BL' . $row)->getValue(),
                    'peso_vidrio' => $sheet->getCell('BM' . $row)->getValue(),
                    'madera' => $sheet->getCell('BN' . $row)->getValue(),
                    'peso_madera' => $sheet->getCell('BO' . $row)->getValue(),
                    'textil' => $sheet->getCell('BP' . $row)->getValue(),
                    'peso_textil' => $sheet->getCell('BQ' . $row)->getValue(),
                    'bateria_electrico' => $sheet->getCell('BR' . $row)->getValue(),
                    'peso_bateria_electrico' => $sheet->getCell('BS' . $row)->getValue(),
                    'impacto_ambiental' => $sheet->getCell('BT' . $row)->getValue()
                ];

                $startcount++;
            }

            if (count($data) > 0) {
                foreach ($data as $row_data) {
                    $product = new Product;
                    $product->reference = $row_data["referencia"];
                    $product->name = $row_data["categoria"] . " " . $row_data["marca"];
                    $product->shop_id = auth()->user()->shop_id;

                    if ($row_data["marca"] != "") {
                        $marca = Brand::firstOrCreate(
                            ['name' => $row_data["marca"]],
                            ['name' => $row_data["marca"]]
                        );
                    }

                    $product->brand_id = $marca->id;

                    $array_categories = array();

                    if ($row_data["categoria"] != "") {
                        $categorie = Category::firstOrCreate(
                            ['name' => $row_data["categoria"]],
                            [
                                'parent_id' => 0,
                                'level' => 0,
                                'name' => $row_data["categoria"],
                                'order_level' => 0,
                                'slug' => Str::slug($row_data["categoria"], '-') . '-' . strtolower(Str::random(5))
                            ]
                        );

                        array_push($array_categories, $categorie->id);

                        if ($row_data["subcategoria"] != "") {
                            $subcategorie = Category::firstOrCreate(
                                ['name' => $row_data["subcategoria"]],
                                [
                                    'parent_id' => $categorie->id,
                                    'level' => 1,
                                    'name' => $row_data["subcategoria"],
                                    'order_level' => 1,
                                    'slug' => Str::slug($row_data["subcategoria"], '-') . '-' . strtolower(Str::random(5))
                                ]
                            );

                            array_push($array_categories, $subcategorie->id);
                        }
                    }

                    if ($row_data["stock"] != "") {
                        $unidades = explode(" ", $row_data["stock"]);
                        $product->stock = $unidades[0];
                    }

                    $product->description = $row_data["referencia"] . " " . $row_data["subcategoria"] . " marca:" . $row_data["marca"] . " medidas:" . $row_data["medida_de_producto"];
                    $product->published = 0;

                    $product->lowest_price  =  $row_data["precio"];
                    $product->highest_price =  $row_data["precio"];
                    $product->currency =  $row_data["divisa"];
                    $product->discount_type =  "flat";
                    $product->discount =  $row_data["descuento"];

                    if ($row_data["garantia"] != "") {
                        $product->has_warranty = 1;
                        $product->warranty_text = $row_data["garantia"];
                    } else {
                        $product->has_warranty = 0;
                    }

                    $product->shipping =  $row_data["envio"];
                    $product->intake =  $row_data["consumo"];
                    $product->material =  $row_data["material"];
                    $product->engaste =  $row_data["medida_de_engaste"];
                    $product->unit_metering =  $row_data["si1"];

                    $measures = explode("x", $row_data["medida_de_producto"]);
                    if (count($measures) > 0) {
                        $product->width = $measures[0];
                    }
                    if (count($measures) > 1) {
                        $product->height = $measures[1];
                    }
                    if (count($measures) > 2) {
                        $product->length = $measures[2];
                    }

                    $product->slug = Str::slug($row_data["categoria"], '-') . '-' . strtolower(Str::random(5));

                    $product->si2 =  $row_data["si2"];
                    $product->medidas_de_embalaje =  $row_data["medidas_de_embalaje"];
                    $product->si3 =  $row_data["si3"];
                    $product->peso_de_producto =  $row_data["peso_de_producto"];
                    $product->si4 =  $row_data["si4"];
                    $product->peso_de_envio =  $row_data["peso_de_envio"];
                    $product->tipo_de_coneccion =  $row_data["tipo_de_coneccion"];
                    $product->eficiencia =  $row_data["eficiencia"];
                    $product->caracteristica1 =  $row_data["caracteristica1"];
                    $product->caracteristica2 =  $row_data["caracteristica2"];
                    $product->caracteristica3 =  $row_data["caracteristica3"];
                    $product->caracteristica4 =  $row_data["caracteristica4"];
                    $product->caracteristica5 =  $row_data["caracteristica5"];
                    $product->caracteristica6 =  $row_data["caracteristica6"];
                    $product->caracteristica7 =  $row_data["caracteristica7"];
                    $product->caracteristica8 =  $row_data["caracteristica8"];
                    $product->beneficio1 =  $row_data["beneficio1"];
                    $product->beneficio2 =  $row_data["beneficio2"];
                    $product->beneficio3 =  $row_data["beneficio3"];
                    $product->beneficio4 =  $row_data["beneficio4"];
                    $product->beneficio5 =  $row_data["beneficio5"];
                    $product->postventa =  $row_data["postventa"];

                    // Archives and images
                    $product->imagen1 = self::saveArchive($row_data["imagen1"]);
                    $product->imagen2 =  self::saveArchive($row_data["imagen2"]);
                    $product->imagen3 =  self::saveArchive($row_data["imagen3"]);
                    $product->imagen4 =  self::saveArchive($row_data["imagen4"]);
                    $product->video =  self::saveArchive($row_data["video"]);
                    $product->video2 =  self::saveArchive($row_data["video2"]);
                    $product->video3 =  self::saveArchive($row_data["video3"]);
                    $product->video4 =  self::saveArchive($row_data["video4"]);
                    $product->ficha_tecnica_imagen1 =  self::saveArchive($row_data["ficha_tecnica_imagen1"]);
                    $product->ficha_tecnica_imagen2 =  self::saveArchive($row_data["ficha_tecnica_imagen2"]);
                    $product->ficha_tecnica_imagen3 =  self::saveArchive($row_data["ficha_tecnica_imagen3"]);
                    $product->ficha_tecnica_imagen4 =  self::saveArchive($row_data["ficha_tecnica_imagen4"]);
                    $product->manual_de_producto =  self::saveArchive($row_data["manual_de_producto"]);
                    $product->ficha_tecnica_del_producto =  self::saveArchive($row_data["ficha_tecnica_del_producto"]);
                    $product->manual_de_instalacion =  self::saveArchive($row_data["manual_de_instalacion"]);
                    $product->thumbnail_img =  self::saveArchive($row_data["logo"]);

                    $product->vida_util =  $row_data["vida_util"];
                    $product->plastico =  $row_data["plastico"];
                    $product->peso_plastico =  $row_data["peso_plastico"];
                    $product->carton =  $row_data["carton"];
                    $product->peso_carton =  $row_data["peso_carton"];
                    $product->papel =  $row_data["papel"];
                    $product->peso_papel =  $row_data["peso_papel"];
                    $product->metal =  $row_data["metal"];
                    $product->peso_metal =  $row_data["peso_metal"];
                    $product->vidrio =  $row_data["vidrio"];
                    $product->peso_vidrio =  $row_data["peso_vidrio"];
                    $product->madera =  $row_data["madera"];
                    $product->peso_madera =  $row_data["peso_madera"];
                    $product->textil =  $row_data["textil"];
                    $product->peso_textil =  $row_data["peso_textil"];
                    $product->bateria_electrico =  $row_data["bateria_electrico"];
                    $product->peso_bateria_electrico =  $row_data["peso_bateria_electrico"];
                    $product->impacto_ambiental =  $row_data["impacto_ambiental"];

                    $product->save();

                    if (count($array_categories) > 0) {
                        $product->categories()->sync($array_categories);
                    }
                }
            }
        } catch (Exception $e) {
            $error_code = $e->errorInfo[1];
            return back()->withErrors('There was a problem uploading the data!');
        }

        flash(translate('Products has been inserted successfully'))->success();
        return redirect()->route('product.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return view('backend.product.products.show', [
            'product' => Product::withCount('reviews', 'wishlists', 'carts')->with('variations.combinations')->findOrFail($id)
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        if ($product->shop_id != auth()->user()->shop_id) {
            abort(403);
        }

        $lang = !empty($request->lang) ? $request->lang : (env('DEFAULT_LANGUAGE') ?: 'en');
        $categories = Category::where('level', 0)->get();
        $all_attributes = Attribute::get();
        return view('backend.product.products.edit', compact('product', 'categories', 'lang', 'all_attributes'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if ($request->has('is_variant') && !$request->has('variations')) {
            flash(translate('Invalid product variations'))->error();
            return redirect()->back();
        }

        $product                    = Product::findOrFail($id);
        $oldProduct                 = clone $product;

        if ($product->shop_id != auth()->user()->shop_id) {
            abort(403);
        }

        $lang = !empty($request->lang) ? $request->lang : (env('DEFAULT_LANGUAGE') ?: 'en');

        if ($lang == (env('DEFAULT_LANGUAGE') ?: 'en')) {
            $product->name          = $request->name;
            $product->unit          = $request->unit;
            $product->description   = $request->description;
        }

        $product->brand_id          = $request->brand_id;
        CoreComponentRepository::instantiateShopRepository();
        $product->min_qty           = $request->min_qty;
        $product->max_qty           = $request->max_qty;
        $product->photos            = $request->photos;
        $product->thumbnail_img     = $request->thumbnail_img;
        $product->published         = $request->status;

        // Product Translations
        $product_translation                = ProductTranslation::firstOrNew(['lang' => $lang, 'product_id' => $product->id]);
        $product_translation->name          = $request->name;
        $product_translation->unit          = $request->unit;
        $product_translation->description   = $request->description;
        $product_translation->save();


        // SEO meta
        $product->meta_title        = (!is_null($request->meta_title)) ? $request->meta_title : $product->name;
        $product->meta_description  = (!is_null($request->meta_description)) ? $request->meta_description : strip_tags($product->description);
        $product->meta_image        = (!is_null($request->meta_image)) ? $request->meta_image : $product->thumbnail_img;
        $product->slug              = (!is_null($request->slug)) ? Str::slug($request->slug, '-') : Str::slug($request->name, '-') . '-' . strtolower(Str::random(5));

        // warranty
        $product->has_warranty      = $request->has('has_warranty') && $request->has_warranty == 'on' ? 1 : 0;


        // tag
        $tags                       = array();
        if ($request->tags != null) {
            foreach (json_decode($request->tags) as $key => $tag) {
                array_push($tags, $tag->value);
            }
        }
        $product->tags              = implode(',', $tags);

        // lowest highest price
        if ($request->has('is_variant') && $request->has('variations')) {
            $product->lowest_price  =  min(array_column($request->variations, 'price'));
            $product->highest_price =  max(array_column($request->variations, 'price'));
        } else {
            $product->lowest_price  =  $request->price;
            $product->highest_price =  $request->price;
        }

        // stock based on all variations
        $product->stock             = ($request->has('is_variant') && $request->has('variations')) ? max(array_column($request->variations, 'stock')) : $request->stock;

        // discount
        $product->discount          = $request->discount;
        $product->discount_type     = $request->discount_type;

        if ($request->date_range != null) {
            $date_var                     = explode(" to ", $request->date_range);
            $product->discount_start_date = strtotime($date_var[0]);
            $product->discount_end_date   = strtotime($date_var[1]);
        }

        // Club Point
        if (get_setting('club_point')) {
            $product->earn_point = $request->earn_point;
        }

        // shipping info
        $product->standard_delivery_time    = $request->standard_delivery_time;
        $product->express_delivery_time     = $request->express_delivery_time;
        $product->weight                    = $request->weight;
        $product->height                    = $request->height;
        $product->length                    = $request->length;
        $product->width                     = $request->width;

        // category
        $product->categories()->sync($request->category_ids);

        // shop category ids
        $shop_category_ids = [];
        foreach ($request->category_ids ?? [] as $id) {
            $shop_category_ids[] = CategoryUtility::get_grand_parent_id($id);
        }

        $shop_category_ids =  array_merge(array_filter($shop_category_ids), $product->shop->shop_categories->pluck('category_id')->toArray());
        $product->shop->categories()->sync($shop_category_ids);

        // shop brand
        if ($request->brand_id) {
            ShopBrand::updateOrCreate([
                'shop_id' => $product->shop_id,
                'brand_id' => $request->brand_id,
            ]);
        }

        // taxes
        $tax_data = array();
        $tax_ids = array();

        if ($request->has('taxes')) {
            foreach ($request->taxes as $key => $tax) {
                array_push($tax_data, [
                    'tax' => $tax,
                    'tax_type' => $request->tax_types[$key]
                ]);
            }
            $tax_ids = $request->tax_ids;
        }

        $taxes = array_combine($tax_ids, $tax_data);

        $product->product_taxes()->sync($taxes);


        //product variation
        $product->is_variant        = ($request->has('is_variant') && $request->has('variations')) ? 1 : 0;

        if ($request->has('is_variant') && $request->has('variations')) {

            $requested_variations = collect($request->variations);
            $requested_variations_code = $requested_variations->pluck('code')->toArray();
            $old_variations_codes = $product->variations->pluck('code')->toArray();
            $old_matched_variations = $requested_variations->whereIn('code', $old_variations_codes);
            $new_variations = $requested_variations->whereNotIn('code', $old_variations_codes);


            // delete old variations that didn't requested
            $product->variations->whereNotIn('code', $requested_variations_code)->each(function ($variation) {
                foreach ($variation->combinations as $comb) {
                    $comb->delete();
                }

                $variation->delete();
            });

            // update old matched variations
            foreach ($old_matched_variations as $variation) {
                $p_variation              = ProductVariation::where('product_id', $product->id)->where('code', $variation['code'])->first();
                $p_variation->price       = $variation['price'];
                $p_variation->stock       = $variation['stock'];
                $p_variation->sku         = $variation['sku'];
                $p_variation->img         = $variation['img'];
                $p_variation->save();
            }


            // insert new requested variations
            foreach ($new_variations as $variation) {
                $p_variation              = new ProductVariation;
                $p_variation->product_id  = $product->id;
                $p_variation->code        = $variation['code'];
                $p_variation->price       = $variation['price'];
                $p_variation->stock       = $variation['stock'];
                $p_variation->sku         = $variation['sku'];
                $p_variation->img         = $variation['img'];
                $p_variation->save();

                foreach (array_filter(explode("/", $variation['code'])) as $combination) {
                    $p_variation_comb                         = new ProductVariationCombination;
                    $p_variation_comb->product_id             = $product->id;
                    $p_variation_comb->product_variation_id   = $p_variation->id;
                    $p_variation_comb->attribute_id           = explode(":", $combination)[0];
                    $p_variation_comb->attribute_value_id     = explode(":", $combination)[1];
                    $p_variation_comb->save();
                }
            }
        } else {
            // check if old product is variant then delete all old variation & combinations
            if ($oldProduct->is_variant) {
                foreach ($product->variations as $variation) {
                    foreach ($variation->combinations as $comb) {
                        $comb->delete();
                    }

                    $variation->delete();
                }
            }

            // $variation              = $product->variations->first();
            // $variation->product_id  = $product->id;
            // $variation->code        = null;
            // $variation->sku         = $request->sku;
            // $variation->price       = $request->price;
            // $variation->stock       = $request->stock;
            // $variation->save();
        }


        // attributes + values
        foreach ($product->attributes as $attr) {
            $attr->delete();
        }

        foreach ($product->attribute_values as $attr_val) {
            $attr_val->delete();
        }

        if ($request->has('product_attributes') && $request->product_attributes[0] != null) {
            foreach ($request->product_attributes as $attr_id) {
                $attribute_values = 'attribute_' . $attr_id . '_values';

                if ($request->has($attribute_values) && $request->$attribute_values != null) {
                    $p_attribute = new ProductAttribute;
                    $p_attribute->product_id = $product->id;
                    $p_attribute->attribute_id = $attr_id;
                    $p_attribute->save();

                    foreach ($request->$attribute_values as $val_id) {
                        $p_attr_value = new ProductAttributeValue;
                        $p_attr_value->product_id = $product->id;
                        $p_attr_value->attribute_id = $attr_id;
                        $p_attr_value->attribute_value_id = $val_id;
                        $p_attr_value->save();
                    }
                }
            }
        }

        $product->name = $request->name;
        $product->reference = $request->reference;
        $product->currency = $request->currency ?? null;
        $product->lowest_price  = $request->highest_price;
        $product->highest_price = $request->highest_price;
        $product->warranty_text = $request->warranty_text;

        $product->save();

        flash(translate('Product has been updated successfully'))->success();
        return redirect()->route('product.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->product_translations()->delete();
        $product->variations()->delete();
        $product->variation_combinations()->delete();
        $product->reviews()->delete();
        $product->product_categories()->delete();
        $product->carts()->delete();
        $product->offers()->delete();
        $product->wishlists()->delete();
        $product->attributes()->delete();
        $product->attribute_values()->delete();
        $product->taxes()->delete();

        if (Product::destroy($id)) {
            flash(translate('Product has been deleted successfully'))->success();
            return redirect()->route('product.index');
        } else {
            flash(translate('Something went wrong'))->error();
            return back();
        }
    }

    /**
     * Duplicates the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function duplicate(Request $request, $id)
    {
        $product = Product::find($id);
        $product_new = $product->replicate();
        $product_new->slug = Str::slug($product_new->name, '-') . '-' . strtolower(Str::random(5));

        if ($product_new->save()) {

            // variation duplicate
            foreach ($product->variations as $key => $variation) {
                $p_variation              = new ProductVariation;
                $p_variation->product_id  = $product_new->id;
                $p_variation->code        = $variation->code;
                $p_variation->price       = $variation->price;
                $p_variation->stock       = $variation->stock;
                $p_variation->sku         = $variation->sku;
                $p_variation->img         = $variation->img;
                $p_variation->save();

                // variation combination duplicate
                foreach ($variation->combinations as $key => $combination) {
                    $p_variation_comb                         = new ProductVariationCombination;
                    $p_variation_comb->product_id             = $product_new->id;
                    $p_variation_comb->product_variation_id   = $p_variation->id;
                    $p_variation_comb->attribute_id           = $combination->attribute_id;
                    $p_variation_comb->attribute_value_id     = $combination->attribute_value_id;
                    $p_variation_comb->save();
                }
            }

            // attribute duplicate
            foreach ($product->attributes as $key => $attribute) {
                $p_attribute                = new ProductAttribute;
                $p_attribute->product_id    = $product_new->id;
                $p_attribute->attribute_id  = $attribute->attribute_id;
                $p_attribute->save();
            }

            // attribute value duplicate
            foreach ($product->attribute_values as $key => $attribute_value) {
                $p_attr_value                       = new ProductAttributeValue;
                $p_attr_value->product_id           = $product_new->id;
                $p_attr_value->attribute_id         = $attribute_value->attribute_id;
                $p_attr_value->attribute_value_id   = $attribute_value->attribute_value_id;
                $p_attr_value->save();
            }

            // translation duplicate
            foreach ($product->product_translations as $key => $translation) {
                $product_translation                = new ProductTranslation;
                $product_translation->product_id    = $product_new->id;
                $product_translation->name          = $translation->name;
                $product_translation->unit          = $translation->unit;
                $product_translation->description   = $translation->description;
                $product_translation->lang          = $translation->lang;
                $product_translation->save();
            }

            //categories duplicate
            foreach ($product->product_categories as $key => $category) {
                $p_category                 = new ProductCategory;
                $p_category->product_id     = $product_new->id;
                $p_category->category_id    = $category->category_id;
                $p_category->save();
            }

            // taxes duplicate
            foreach ($product->taxes as $key => $tax) {
                $p_tax                = new ProductTax;
                $p_tax->product_id    = $product_new->id;
                $p_tax->tax_id        = $tax->tax_id;
                $p_tax->tax           = $tax->tax;
                $p_tax->tax_type      = $tax->tax_type;
                $p_tax->save();
            }

            flash(translate('Product has been duplicated successfully'))->success();
            return redirect()->route('product.index');
        } else {
            flash(translate('Something went wrong'))->error();
            return back();
        }
    }

    public function get_products_by_subcategory(Request $request)
    {
        $products = Product::where('subcategory_id', $request->subcategory_id)->get();
        return $products;
    }

    public function get_products_by_brand(Request $request)
    {
        $products = Product::where('brand_id', $request->brand_id)->get();
        return view('partials.product_select', compact('products'));
    }

    public function updatePublished(Request $request)
    {
        $product = Product::findOrFail($request->id);
        $product->published = $request->status;
        $product->save();

        cache_clear();

        return 1;
    }

    public function sku_combination(Request $request)
    {
        $option_choices = array();

        if ($request->has('product_options')) {
            $product_options = $request->product_options;
            sort($product_options, SORT_NUMERIC);

            foreach ($product_options as $key => $option) {

                $option_name = 'option_' . $option . '_choices';
                $choices = array();

                if ($request->has($option_name)) {

                    $product_option_values = $request[$option_name];
                    sort($product_option_values, SORT_NUMERIC);

                    foreach ($product_option_values as $key => $item) {
                        array_push($choices, $item);
                    }

                    $option_choices[$option] =  $choices;
                }
            }
        }

        $combinations = array(array());

        foreach ($option_choices as $property => $property_values) {
            $tmp = array();

            foreach ($combinations as $combination_item) {
                foreach ($property_values as $property_value) {
                    $tmp[] = $combination_item + array($property => $property_value);
                }
            }
            $combinations = $tmp;
        }

        return view('backend.product.products.sku_combinations', compact('combinations'))->render();
    }

    public function new_attribute(Request $request)
    {
        $attributes = Attribute::query();
        if ($request->has('product_attributes')) {
            foreach ($request->product_attributes as $key => $value) {
                if ($value == NULL) {
                    return array(
                        'count' => -1,
                        'view' => view('backend.product.products.new_attribute', compact('attributes'))->render(),
                    );
                }
            }

            $attributes->whereNotIn('id', array_diff($request->product_attributes, [null]));
        }

        $attributes = $attributes->get();

        return array(
            'count' => count($attributes),
            'view' => view('backend.product.products.new_attribute', compact('attributes'))->render(),
        );
    }

    public function get_attribute_values(Request $request)
    {
        $attribute_id = $request->attribute_id;
        $attribute_values = AttributeValue::where('attribute_id', $attribute_id)->get();

        return view('backend.product.products.new_attribute_values', compact('attribute_values', 'attribute_id'));
    }

    public function new_option(Request $request)
    {
        $attributes = Attribute::query();

        if ($request->has('product_options')) {
            foreach ($request->product_options as $key => $value) {
                if ($value == NULL) {
                    return array(
                        'count' => -1,
                        'view' => view('backend.product.products.new_option', compact('attributes'))->render(),
                    );
                }
            }

            $attributes->whereNotIn('id', array_diff($request->product_options, [null]));

            if (count($request->product_options) === 3) {
                return array(
                    'count' => -2,
                    'view' => view('backend.product.products.new_option', compact('attributes'))->render(),
                );
            }
        }

        $attributes = $attributes->get();

        return array(
            'count' => count($attributes),
            'view' => view('backend.product.products.new_option', compact('attributes'))->render(),
        );
    }

    public function get_option_choices(Request $request)
    {

        $attribute_id = $request->attribute_id;
        $attribute_values = AttributeValue::where('attribute_id', $attribute_id)->get();

        return view('backend.product.products.new_option_choices', compact('attribute_values', 'attribute_id'));
    }

    public function updateProductApproval(Request $request)
    {
        $product = Product::findOrFail($request->id);
        $product->approved = $request->approved;
        $shop = $product->shop;

        if ($shop->user->user_type == 'seller') {
            if (
                $shop->package_invalid_at == null
                || Carbon::now()->diffInDays(Carbon::parse($shop->package_invalid_at), false) < 0
                || $shop->product_upload_limit <= $shop->products()->where('published', 1)->count()
            ) {
                return 0;
            }
        }

        $product->save();
        cache_clear();
        return 1;
    }

    public function alegraImport()
    {
        return response()->json(SyncAlegraProductsJob::trigger());
    }

    public function alegraImportStatus()
    {
        return response()->json(Cache::get(SyncAlegraProductsJob::CACHE_KEY, ['status' => 'idle']));
    }
}
