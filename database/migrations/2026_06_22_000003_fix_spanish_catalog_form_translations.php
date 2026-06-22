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
            'choose_one_or_more_categories_and_name_the_catalog' => 'Elige una o más categorías y escribe el nombre del catálogo',
            'optional_fullpage_images_shown_after_the_selected_letter' => 'Imágenes opcionales de página completa que se muestran después de la letra seleccionada',
            'only_products_with_price_greater_than_zero_can_be_selected' => 'Solo se pueden seleccionar productos con precio mayor que cero',
            'search_products_by_name_id_or_category' => 'Buscar productos por nombre, ID o categoría',
        ];
    }
};
