<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        foreach ($this->translations() as $langKey => $langValue) {
            DB::table('translations')->updateOrInsert(
                ['lang' => 'es', 'lang_key' => $langKey],
                [
                    'lang_value' => $langValue,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }

        Artisan::call('optimize:clear');
    }

    public function down(): void
    {
        // Keep corrected Spanish translations in place on rollback.
    }

    private function translations(): array
    {
        return [
            'service' => 'Servicio',
            'services' => 'Servicios',
            'all_services' => 'Todos los servicios',
            'import_services' => 'Importar servicios',
            'add_new_service' => 'Agregar nuevo servicio',
            'inhouse_services' => 'Servicios propios',
            'service_information' => 'Información del servicio',
            'service_name' => 'Nombre del servicio',
            'service_images' => 'Imágenes del servicio',
            'service_price_stock' => 'Precio y stock del servicio',
            'variant_service' => 'Servicio con variantes',
            'service_discount' => 'Descuento del servicio',
            'service_description' => 'Descripción del servicio',
            'service_attributes' => 'Atributos del servicio',
            'service_status' => 'Estado del servicio',
            'service_brand' => 'Marca del servicio',
            'service_category' => 'Categoría del servicio',
            'service_tags' => 'Etiquetas del servicio',
            'upload_service' => 'Subir servicio',
            'edit_service' => 'Editar servicio',
            'update_service' => 'Actualizar servicio',
            'service_reviews' => 'Reseñas del servicio',
            'service__rating' => 'Servicio y calificación',
        ];
    }
};
