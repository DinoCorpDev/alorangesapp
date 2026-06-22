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
        // Keep Spanish translations in place if this migration is rolled back.
    }

    private function translations(): array
    {
        return [
            'clear_cache' => 'Limpiar caché',
            'pos_system' => 'Sistema POS',
            'items' => 'Artículos',
            'collections' => 'Colecciones',
            'plans' => 'Planes',
            'services' => 'Servicios',
            'digital_products' => 'Productos digitales',
            'brand' => 'Marca',
            'cover' => 'Portada',
            'top' => 'Arriba',
            'middle' => 'Centro',
            'bottom' => 'Abajo',
            'create_edit_and_download_product_catalogs_from_one_place' => 'Crear, editar y descargar catálogos de productos desde un solo lugar',
            'image_title_and_advisor' => 'Imagen, título y asesor',
            'choose_the_product_groups' => 'Elige los grupos de productos',
            'search_and_select_items' => 'Busca y selecciona productos',
            'generate_or_update_the_file' => 'Genera o actualiza el archivo',
            'start_by_choosing_categories_products_load_automatically_and_you_can_select_them_one_by_one_or_all_visible_results_at_once' => 'Empieza eligiendo categorías. Los productos se cargan automáticamente y puedes seleccionarlos uno por uno o todos los resultados visibles a la vez.',
            'complete_the_sections_below_to_generate_a_new_pdf' => 'Completa las secciones siguientes para generar un nuevo PDF',
            'adjust_the_catalog_details_and_regenerate_the_pdf' => 'Ajusta los detalles del catálogo y vuelve a generar el PDF',
            'this_information_appears_on_the_first_page_of_the_catalog' => 'Esta información aparece en la primera página del catálogo',
            'review_the_selected_products_before_generating_the_pdf' => 'Revisa los productos seleccionados antes de generar el PDF',
            'select_products_to_enable_this_button' => 'Selecciona productos para habilitar este botón',
            'products_selected' => 'productos seleccionados',
            'categories_selected' => 'categorías seleccionadas',
            'the_product_list_will_appear_here_grouped_by_category_and_letter' => 'La lista de productos aparecerá aquí agrupada por categoría y letra',
            'please_wait_while_the_category_products_are_loaded' => 'Espera mientras se cargan los productos de la categoría',
            'try_selecting_a_different_category' => 'Intenta seleccionar una categoría diferente',
            'please_try_again_or_review_the_selected_categories' => 'Intenta de nuevo o revisa las categorías seleccionadas',
            'download_edit_or_delete_existing_catalog_files' => 'Descarga, edita o elimina los catálogos existentes',
            'search_generated_catalogs' => 'Buscar catálogos generados',
            'no_catalogs_match_your_search' => 'Ningún catálogo coincide con tu búsqueda',
            'generated_catalogs_will_appear_here' => 'Los catálogos generados aparecerán aquí',
        ];
    }
};
