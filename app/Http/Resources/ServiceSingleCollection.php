<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ServiceSingleCollection extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $serviceArray = [
            'id' => (int) $this->id,
            'name' => $this->getTranslation('name'),
            'slug' => $this->slug,
            'metaTitle' => $this->meta_title,
            'brand' => [
                'id' => optional($this->brand)->id,
                'name' => optional($this->brand)->getTranslation('name'),
                'slug' => optional($this->brand)->slug,
                'logo' => api_asset(optional($this->brand)->logo),
            ],
            'photos' => $this->convertPhotos($this),
            'thumbnail_image' => api_asset($this->thumbnail_img),
            'tags' => explode(',', $this->tags),
            'featured' => (int) $this->featured,
            'stock' => (int) $this->stock,
            'min_qty' => (int) $this->min_qty,
            'max_qty' => (int) $this->max_qty,
            'unit' => $this->getTranslation('unit'),
            'discount' => $this->discount,
            'discount_type' => $this->discount_type,
            'base_price' => (float) product_base_price($this),
            'lowest_price' => $this->lowest_price,
            'highest_price' => (float) product_highest_price($this),
            'highest_price' => $this->highest_price,
            'base_discounted_price' => (float) product_discounted_base_price($this),
            'highest_discounted_price' => (float) product_discounted_highest_price($this),
            'standard_delivery_time' => (int) $this->standard_delivery_time,
            'express_delivery_time' => (int) $this->express_delivery_time,
            'is_variant' => $this->is_variant,
            'has_warranty' => $this->has_warranty,
            'review_summary' => [
                'average' => (float) $this->rating,
                'total_count' => (int) $this->reviews_count,
                'count_5' => (int) $this->reviews_5_count,
                'count_4' => (int) $this->reviews_4_count,
                'count_3' => (int) $this->reviews_3_count,
                'count_2' => (int) $this->reviews_2_count,
                'count_1' => (int) $this->reviews_1_count,
            ],
            'description' => $this->getTranslation('description'),
            'variations' => filter_product_variations($this->variations, $this),
            'variation_options' => generate_variation_options($this->variation_combinations),

            'intake' => $this->intake,
            'material' => $this->material,
            'unit_metering' => $this->unit_metering,
            'medida_servicio' => $this->width . " - " . $this->height . " - " . $this->length,
            'medidas_de_embalaje' => $this->medidas_de_embalaje,
            'engaste' => $this->engaste,
            'peso_de_servicio' => $this->peso_de_servicio,
            'peso_de_envio' => $this->peso_de_envio,
            'tipo_de_coneccion' => $this->tipo_de_coneccion,
            'eficiencia' => $this->eficiencia,
            'caracteristica1' => $this->caracteristica1,
            'caracteristica2' => $this->caracteristica2,
            'caracteristica3' => $this->caracteristica3,
            'caracteristica4' => $this->caracteristica4,
            'caracteristica5' => $this->caracteristica5,
            'caracteristica6' => $this->caracteristica6,
            'caracteristica7' => $this->caracteristica7,
            'manual_de_servicio' => $this->manual_de_servicio,
            'ficha_tecnica_del_servicio' => $this->ficha_tecnica_del_servicio,
            'manual_de_instalacion' => $this->manual_de_instalacion,
            'beneficio1' => $this->beneficio1,
            'beneficio2' => $this->beneficio2,
            'beneficio3' => $this->beneficio3,
            'beneficio4' => $this->beneficio4,
            'beneficio5' => $this->beneficio5,
            'postventa' => $this->postventa,
            'vida_util' => $this->vida_util,
            'plastico' => $this->plastico,
            'peso_plastico' => $this->peso_plastico,
            'carton' => $this->carton,
            'peso_carton' => $this->peso_carton,
            'papel' => $this->papel,
            'peso_papel' => $this->peso_papel,
            'metal' => $this->metal,
            'peso_metal' => $this->peso_metal,
            'vidrio' => $this->vidrio,
            'peso_vidrio' => $this->peso_vidrio,
            'madera' => $this->madera,
            'peso_madera' => $this->peso_madera,
            'textil' => $this->textil,
            'peso_textil' => $this->peso_textil,
            'bateria_electrico' => $this->bateria_electrico,
            'peso_bateria_electrico' => $this->peso_bateria_electrico,
            'impacto_ambiental' => $this->impacto_ambiental,
            'reference' => $this->reference,
            'currency' => $this->currency,
            'warranty_text' => $this->warranty_text,
            'shipping' => $this->shipping,
            'imagenes' => [
                array(
                    'src' => api_asset($this->thumbnail_img),
                    'type' => 'image'
                ),
                array(
                    'src' => api_asset($this->imagen1),
                    'type' => 'image'
                ),
                array(
                    'src' => api_asset($this->imagen2),
                    'type' => 'image'
                ),
                array(
                    'src' => api_asset($this->imagen3),
                    'type' => 'image'
                ),
                array(
                    'src' => api_asset($this->imagen4),
                    'type' => 'image'
                )
            ],
            'videos' => [
                array(
                    'src' => api_asset($this->video),
                    'type' => 'video'
                ),
                array(
                    'src' => api_asset($this->video2),
                    'type' => 'video'
                ),
                array(
                    'src' => api_asset($this->video3),
                    'type' => 'video'
                ),
                array(
                    'src' => api_asset($this->video4),
                    'type' => 'video'
                )
            ],
            'ficha_tecnica_del_servicio' => api_asset($this->ficha_tecnica_del_servicio),
            /*
            'shop' => [
                'name' => $this->shop->name,
                'logo' => api_asset($this->shop->logo),
                'rating' => (double) $this->shop->rating,
                'review_count' => $this->shop->reviews_count,
                'slug' => $this->shop->slug,
            ]*/
        ];

        $serviceArray['imagenes'] = array_filter($serviceArray['imagenes'], function ($value) {
            return !empty($value['src']);
        });

        $serviceArray['videos'] = array_filter($serviceArray['videos'], function ($value) {
            return !empty($value['src']);
        });

        return $serviceArray;
    }

    public function with($request)
    {
        return [
            'success' => true,
            'status' => 200
        ];
    }

    protected function convertPhotos()
    {
        $result = array();
        foreach (explode(',', $this->photos) as $item) {
            array_push($result, api_asset($item));
        }
        return $result;
    }
}
