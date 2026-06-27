<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App;

class Product extends Model
{

    protected $guarded = ['id'];

    protected $with = ['product_translations', 'taxes'];

    protected $fillable = [
        'shop_id',
        'reference',
        'name',
        'brand_id',
        'photos',
        'thumbnail_img',
        'tags',
        'description',
        'lowest_price',
        'highest_price',
        'discount',
        'discount_type',
        'discount_start_date',
        'discount_end_date',
        'currency ',
        'stock',
        'published',
        'approved',
        'unit',
        'min_qty',
        'max_qty',
        'is_variant',
        'has_warranty',
        'warranty_text ',
        'tax',
        'standard_delivery_time',
        'express_delivery_time',
        'weight',
        'height',
        'length',
        'width',
        'meta_title',
        'meta_description',
        'meta_image',
        'slug',
        'rating',
        'num_of_sale',
        'shipping',
        'intake',
        'material',
        'unit_metering',
        'engaste',
        'si2',
        'medidas_de_embalaje',
        'si3',
        'peso_de_producto',
        'si4',
        'peso_de_envio',
        'tipo_de_coneccion',
        'eficiencia',
        'caracteristica1',
        'caracteristica2',
        'caracteristica3',
        'caracteristica4',
        'caracteristica5',
        'caracteristica6',
        'caracteristica7',
        'caracteristica8',
        'beneficio1',
        'beneficio2',
        'beneficio3',
        'beneficio4',
        'beneficio5',
        'postventa',
        'imagen1',
        'imagen2',
        'imagen3',
        'imagen4',
        'video',
        'video2',
        'video3',
        'video4',
        'ficha_tecnica_imagen1',
        'ficha_tecnica_imagen2',
        'ficha_tecnica_imagen3',
        'ficha_tecnica_imagen4',
        'manual_de_producto',
        'ficha_tecnica_del_producto',
        'manual_de_instalacion',
        'logo',
        'vida_util',
        'plastico',
        'peso_plastico',
        'carton',
        'peso_carton',
        'papel',
        'peso_papel',
        'metal',
        'peso_metal',
        'vidrio',
        'peso_vidrio',
        'madera',
        'peso_madera',
        'peso_textil',
        'bateria_electrico',
        'peso_bateria_electrico',
        'impacto_ambiental',
        'earn_point',
        'digital',
        'file_name',
        'created_at',
        'updated_at',
        'deleted_at',
        'iterated_in',
    ];

    public function getTranslation($field = '', $lang = false)
    {
        $lang = $lang == false ? App::getLocale() : $lang;
        $product_translations = $this->product_translations->where('lang', $lang)->first();
        return $product_translations != null ? $product_translations->$field : $this->$field;
    }

    public function product_translations()
    {
        return $this->hasMany(ProductTranslation::class);
    }

    public function product_categories()
    {
        return $this->hasMany(ProductCategory::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'product_categories', 'product_id', 'category_id');
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class);
    }

    public function taxes()
    {
        return $this->hasMany(ProductTax::class);
    }

    public function product_taxes()
    {
        return $this->belongsToMany(Tax::class, 'product_taxes', 'product_id', 'tax_id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class)->where('status', 1);
    }

    public function reviews_5()
    {
        return $this->hasMany(Review::class)->where('status', 1)->where('rating', 5);
    }

    public function reviews_4()
    {
        return $this->hasMany(Review::class)->where('status', 1)->where('rating', 4);
    }

    public function reviews_3()
    {
        return $this->hasMany(Review::class)->where('status', 1)->where('rating', 3);
    }

    public function reviews_2()
    {
        return $this->hasMany(Review::class)->where('status', 1)->where('rating', 2);
    }

    public function reviews_1()
    {
        return $this->hasMany(Review::class)->where('status', 1)->where('rating', 1);
    }

    public function offers()
    {
        return $this->hasMany(OfferProduct::class);
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function carts()
    {
        return $this->hasMany(Cart::class);
    }

    public function attributes()
    {
        return $this->hasMany(ProductAttribute::class);
    }

    public function attribute_values()
    {
        return $this->hasMany(ProductAttributeValue::class);
    }

    public function variations()
    {
        return $this->hasMany(ProductVariation::class);
    }

    public function variation_combinations()
    {
        return $this->hasMany(ProductVariationCombination::class);
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }
}
