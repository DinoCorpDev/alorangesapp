<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        $this->ensureSpanishLanguage($now);
        $this->moveLegacySpanishTranslations($now);
        $this->seedSpanishTranslations($now);
        $this->setEnvValue('DEFAULT_LANGUAGE', 'es');

        Artisan::call('optimize:clear');
    }

    public function down(): void
    {
        DB::table('translations')->where('lang', 'es')->delete();
        DB::table('languages')->where('code', 'es')->update([
            'status' => 0,
            'updated_at' => now(),
        ]);

        $this->setEnvValue('DEFAULT_LANGUAGE', 'en');
        Artisan::call('optimize:clear');
    }

    private function ensureSpanishLanguage($now): void
    {
        $spanish = DB::table('languages')->where('code', 'es')->first();
        $legacy = DB::table('languages')
            ->where('code', '888888')
            ->orWhere('name', 'Epanol')
            ->first();

        if ($spanish) {
            DB::table('languages')->where('id', $spanish->id)->update([
                'name' => 'Español',
                'flag' => 'es',
                'rtl' => 0,
                'status' => 1,
                'deleted_at' => null,
                'updated_at' => $now,
            ]);

            if ($legacy && $legacy->id !== $spanish->id) {
                DB::table('languages')->where('id', $legacy->id)->update([
                    'status' => 0,
                    'deleted_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            return;
        }

        if ($legacy) {
            DB::table('languages')->where('id', $legacy->id)->update([
                'name' => 'Español',
                'flag' => 'es',
                'code' => 'es',
                'rtl' => 0,
                'status' => 1,
                'deleted_at' => null,
                'updated_at' => $now,
            ]);

            return;
        }

        DB::table('languages')->insert([
            'name' => 'Español',
            'flag' => 'es',
            'code' => 'es',
            'rtl' => 0,
            'status' => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    private function moveLegacySpanishTranslations($now): void
    {
        DB::table('translations')->where('lang', '888888')->update([
            'lang' => 'es',
            'updated_at' => $now,
        ]);
    }

    private function seedSpanishTranslations($now): void
    {
        $englishRows = DB::table('translations')
            ->where('lang', 'en')
            ->whereNotNull('lang_key')
            ->get(['lang_key', 'lang_value']);

        foreach ($englishRows as $row) {
            $langKey = (string) $row->lang_key;
            $spanishValue = $this->spanishValue($langKey, (string) $row->lang_value);

            DB::table('translations')->updateOrInsert(
                ['lang' => 'es', 'lang_key' => $langKey],
                [
                    'lang_value' => $spanishValue,
                    'updated_at' => $now,
                    'created_at' => $now,
                ]
            );
        }

        foreach ($this->manualTranslations() as $langKey => $spanishValue) {
            DB::table('translations')->updateOrInsert(
                ['lang' => 'es', 'lang_key' => $langKey],
                [
                    'lang_value' => $spanishValue,
                    'updated_at' => $now,
                    'created_at' => $now,
                ]
            );
        }
    }

    private function spanishValue(string $langKey, string $englishValue): string
    {
        $manual = $this->manualTranslations();

        if (array_key_exists($langKey, $manual)) {
            return $manual[$langKey];
        }

        $value = trim(str_replace(["\r", "\n", "\r\n"], ' ', $englishValue));

        if ($value === '') {
            return $value;
        }

        $manualByValue = array_change_key_case($this->manualValueTranslations(), CASE_LOWER);
        $lookupValue = strtolower($value);

        if (array_key_exists($lookupValue, $manualByValue)) {
            return $manualByValue[$lookupValue];
        }

        return $this->fallbackSpanish($value);
    }

    private function fallbackSpanish(string $value): string
    {
        $phrases = [
            'Add New' => 'Agregar nuevo',
            'All products' => 'Todos los productos',
            'All Products' => 'Todos los productos',
            'Product Name' => 'Nombre del producto',
            'Product Information' => 'Información del producto',
            'Product Images' => 'Imágenes del producto',
            'Product price, stock' => 'Precio y stock del producto',
            'Regular price' => 'Precio regular',
            'Unit Price' => 'Precio unitario',
            'Shipping Cost' => 'Costo de envío',
            'Coupon Discount' => 'Descuento del cupón',
            'Grand Total' => 'Total general',
            'Sub Total' => 'Subtotal',
            'Total Tax' => 'Impuesto total',
            'Payment Method' => 'Método de pago',
            'Payment Methods' => 'Métodos de pago',
            'Payment Status' => 'Estado del pago',
            'Delivery Status' => 'Estado de entrega',
            'Order Code' => 'Código del pedido',
            'Order Date' => 'Fecha del pedido',
            'Billing address' => 'Dirección de facturación',
            'Shipping address' => 'Dirección de envío',
            'General Settings' => 'Configuración general',
            'SMTP Settings' => 'Configuración SMTP',
            'Third Party Settings' => 'Configuración de terceros',
            'File System Configuration' => 'Configuración del sistema de archivos',
            'Social media Logins' => 'Inicios de sesión con redes sociales',
            'Website Setup' => 'Configuración del sitio web',
            'Uploaded Files' => 'Archivos subidos',
            'Delete Confirmation' => 'Confirmación de eliminación',
            'Are you sure to delete this?' => '¿Seguro que deseas eliminar esto?',
            'All data related to this will be deleted.' => 'Se eliminarán todos los datos relacionados.',
            'Something went wrong' => 'Algo salió mal',
            'Nothing selected' => 'Nada seleccionado',
            'Nothing found' => 'No se encontró nada',
            'Choose file' => 'Elegir archivo',
            'Choose File' => 'Elegir archivo',
            'File selected' => 'Archivo seleccionado',
            'Files selected' => 'Archivos seleccionados',
            'Add more files' => 'Agregar más archivos',
            'Upload complete' => 'Carga completada',
            'Upload paused' => 'Carga pausada',
            'Resume upload' => 'Reanudar carga',
            'Pause upload' => 'Pausar carga',
            'Retry upload' => 'Reintentar carga',
            'Cancel upload' => 'Cancelar carga',
            'Sort by' => 'Ordenar por',
            'Type & Enter' => 'Escribe y presiona Enter',
            'High > Low' => 'Mayor a menor',
            'Low > High' => 'Menor a mayor',
            'Edit Product' => 'Editar producto',
            'New Password' => 'Nueva contraseña',
            'Confirm Password' => 'Confirmar contraseña',
            'Forgot Password' => 'Olvidé mi contraseña',
            'Reset Password' => 'Restablecer contraseña',
            'Email Address' => 'Dirección de correo',
            'Phone Number' => 'Número de teléfono',
            'Save Configuration' => 'Guardar configuración',
            'PDF Catalogs' => 'Catálogos PDF',
            'Catalog Configuration' => 'Configuración del catálogo',
            'Generated Catalogs' => 'Catálogos generados',
            'Create Catalog' => 'Crear catálogo',
            'Edit Catalog' => 'Editar catálogo',
            'Generate PDF Catalog' => 'Generar catálogo PDF',
            'Update PDF Catalog' => 'Actualizar catálogo PDF',
            'Create New Catalog' => 'Crear nuevo catálogo',
        ];

        foreach ($phrases as $from => $to) {
            $value = str_ireplace($from, $to, $value);
        }

        $words = [
            'Dashboard' => 'Panel',
            'Products' => 'Productos',
            'Product' => 'Producto',
            'Categories' => 'Categorías',
            'Category' => 'Categoría',
            'Brands' => 'Marcas',
            'Brand' => 'Marca',
            'Attributes' => 'Atributos',
            'Attribute' => 'Atributo',
            'Reviews' => 'Reseñas',
            'Orders' => 'Pedidos',
            'Order' => 'Pedido',
            'Customers' => 'Clientes',
            'Customer' => 'Cliente',
            'Marketing' => 'Marketing',
            'Offers' => 'Ofertas',
            'Offer' => 'Oferta',
            'Newsletters' => 'Boletines',
            'Subscribers' => 'Suscriptores',
            'Coupon' => 'Cupón',
            'Support' => 'Soporte',
            'Header' => 'Encabezado',
            'Footer' => 'Pie de página',
            'Banners' => 'Banners',
            'Pages' => 'Páginas',
            'Page' => 'Página',
            'Appearance' => 'Apariencia',
            'Settings' => 'Configuración',
            'Languages' => 'Idiomas',
            'Language' => 'Idioma',
            'Currency' => 'Moneda',
            'Shipping' => 'Envío',
            'Tax' => 'Impuesto',
            'Staffs' => 'Personal',
            'Staff' => 'Personal',
            'Roles' => 'Roles',
            'System' => 'Sistema',
            'Update' => 'Actualizar',
            'Create' => 'Crear',
            'Save' => 'Guardar',
            'Submit' => 'Enviar',
            'Cancel' => 'Cancelar',
            'Delete' => 'Eliminar',
            'Edit' => 'Editar',
            'Duplicate' => 'Duplicar',
            'Download' => 'Descargar',
            'Upload' => 'Subir',
            'Browse' => 'Buscar',
            'Search' => 'Buscar',
            'Filter' => 'Filtrar',
            'Reset' => 'Restablecer',
            'Name' => 'Nombre',
            'Email' => 'Correo',
            'Phone' => 'Teléfono',
            'Address' => 'Dirección',
            'Status' => 'Estado',
            'Options' => 'Opciones',
            'Info' => 'Información',
            'Price' => 'Precio',
            'Published' => 'Publicado',
            'Rating' => 'Calificación',
            'Notifications' => 'Notificaciones',
            'Notification' => 'Notificación',
            'Profile' => 'Perfil',
            'Logout' => 'Cerrar sesión',
            'Login' => 'Iniciar sesión',
            'Registration' => 'Registro',
            'Register' => 'Registrarse',
            'Password' => 'Contraseña',
            'Description' => 'Descripción',
            'Image' => 'Imagen',
            'Images' => 'Imágenes',
            'Thumbnail' => 'Miniatura',
            'Gallery' => 'Galería',
            'Variant' => 'Variante',
            'Unit' => 'Unidad',
            'Quantity' => 'Cantidad',
            'Qty' => 'Cant.',
            'Total' => 'Total',
            'Date' => 'Fecha',
            'Amount' => 'Monto',
            'Paid' => 'Pagado',
            'Unpaid' => 'Pendiente',
            'Processing' => 'Procesando',
            'Complete' => 'Completado',
            'Completed' => 'Completado',
            'Pending' => 'Pendiente',
            'Approved' => 'Aprobado',
            'Rejected' => 'Rechazado',
            'Active' => 'Activo',
            'Inactive' => 'Inactivo',
            'Enabled' => 'Habilitado',
            'Disabled' => 'Deshabilitado',
            'Success' => 'Éxito',
            'Warning' => 'Advertencia',
            'Error' => 'Error',
            'Default' => 'Predeterminado',
            'Current' => 'Actual',
            'All' => 'Todos',
            'New' => 'Nuevo',
            'Old' => 'Antiguo',
            'First' => 'Primero',
            'Last' => 'Último',
            'Next' => 'Siguiente',
            'Prev' => 'Anterior',
            'Previous' => 'Anterior',
            'Back' => 'Volver',
            'Yes' => 'Sí',
            'No' => 'No',
            'File' => 'Archivo',
            'Files' => 'Archivos',
            'Title' => 'Título',
            'Slug' => 'Slug',
            'URL' => 'URL',
            'Menu' => 'Menú',
            'Website' => 'Sitio web',
            'Configuration' => 'Configuración',
            'Manager' => 'Administrador',
        ];

        foreach ($words as $from => $to) {
            $value = preg_replace('/\b' . preg_quote($from, '/') . '\b/i', $to, $value);
        }

        return $value;
    }

    private function manualTranslations(): array
    {
        return [
            'all_products' => 'Todos los productos',
            'add_new_product' => 'Agregar nuevo producto',
            'sort_by' => 'Ordenar por',
            'rating_high__low' => 'Calificación (mayor a menor)',
            'rating_low__high' => 'Calificación (menor a mayor)',
            'num_of_sale_high__low' => 'Número de ventas (mayor a menor)',
            'num_of_sale_low__high' => 'Número de ventas (menor a mayor)',
            'base_price_high__low' => 'Precio base (mayor a menor)',
            'base_price_low__high' => 'Precio base (menor a mayor)',
            'type__enter' => 'Escribe y presiona Enter',
            'name' => 'Nombre',
            'info' => 'Información',
            'categories' => 'Categorías',
            'brand' => 'Marca',
            'published' => 'Publicado',
            'options' => 'Opciones',
            'rating' => 'Calificación',
            'toal_sold' => 'Total vendido',
            'price' => 'Precio',
            'edit' => 'Editar',
            'duplicate' => 'Duplicar',
            'delete' => 'Eliminar',
            'delete_confirmation' => 'Confirmación de eliminación',
            'are_you_sure_to_delete_this' => '¿Seguro que deseas eliminar esto?',
            'all_data_related_to_this_will_be_deleted' => 'Se eliminarán todos los datos relacionados.',
            'cancel' => 'Cancelar',
            'yes_delete' => 'Sí, eliminar',
            'published_products_updated_successfully' => 'Productos publicados actualizados correctamente',
            'something_went_wrong' => 'Algo salió mal',
            'nothing_selected' => 'Nada seleccionado',
            'nothing_found' => 'No se encontró nada',
            'choose_file' => 'Elegir archivo',
            'file_selected' => 'Archivo seleccionado',
            'files_selected' => 'Archivos seleccionados',
            'add_more_files' => 'Agregar más archivos',
            'adding_more_files' => 'Agregando más archivos',
            'drop_files_here_paste_or' => 'Suelta archivos aquí, pega o',
            'browse' => 'Buscar',
            'upload_complete' => 'Carga completada',
            'upload_paused' => 'Carga pausada',
            'resume_upload' => 'Reanudar carga',
            'pause_upload' => 'Pausar carga',
            'retry_upload' => 'Reintentar carga',
            'cancel_upload' => 'Cancelar carga',
            'uploading' => 'Subiendo',
            'processing' => 'Procesando',
            'complete' => 'Completado',
            'file' => 'Archivo',
            'files' => 'Archivos',
            'dashboard' => 'Panel',
            'product' => 'Producto',
            'products' => 'Productos',
            'category' => 'Categoría',
            'attributes' => 'Atributos',
            'reviews' => 'Reseñas',
            'orders' => 'Pedidos',
            'customers' => 'Clientes',
            'offers' => 'Ofertas',
            'newsletters' => 'Boletines',
            'subscribers' => 'Suscriptores',
            'uploaded_files' => 'Archivos subidos',
            'support' => 'Soporte',
            'website_setup' => 'Configuración del sitio web',
            'appearance' => 'Apariencia',
            'settings' => 'Configuración',
            'general_settings' => 'Configuración general',
            'languages' => 'Idiomas',
            'currency' => 'Moneda',
            'smtp_settings' => 'Configuración SMTP',
            'payment_methods' => 'Métodos de pago',
            'file_system_configuration' => 'Configuración del sistema de archivos',
            'social_media_logins' => 'Inicios de sesión con redes sociales',
            'third_party_settings' => 'Configuración de terceros',
            'shipping' => 'Envíos',
            'shipping_countries' => 'Países de envío',
            'shipping_states' => 'Departamentos/estados de envío',
            'shipping_cities' => 'Ciudades de envío',
            'shipping_zones' => 'Zonas de envío',
            'tax' => 'Impuesto',
            'staffs' => 'Personal',
            'all_staffs' => 'Todo el personal',
            'roles' => 'Roles',
            'system' => 'Sistema',
            'update' => 'Actualizar',
            'server_status' => 'Estado del servidor',
            'addon_manager' => 'Administrador de complementos',
            'browse_website' => 'Ver sitio web',
            'add_new' => 'Agregar nuevo',
            'add_new_coupon' => 'Agregar nuevo cupón',
            'add_new_offer' => 'Agregar nueva oferta',
            'add_new_staff' => 'Agregar nuevo usuario del personal',
            'notifications' => 'Notificaciones',
            'new_notification' => 'Nueva notificación',
            'profile' => 'Perfil',
            'logout' => 'Cerrar sesión',
            'edit_product' => 'Editar producto',
            'product_information' => 'Información del producto',
            'product_name' => 'Nombre del producto',
            'translatable' => 'Traducible',
            'unit' => 'Unidad',
            'minimum_purchase_qty' => 'Cantidad mínima de compra',
            'maximum_purchase_qty' => 'Cantidad máxima de compra',
            'product_images' => 'Imágenes del producto',
            'thumbnail_image' => 'Imagen miniatura',
            'gallery_images' => 'Imágenes de galería',
            'product_price_stock' => 'Precio y stock del producto',
            'variant_product' => 'Producto con variantes',
            'regular_price' => 'Precio regular',
            'sku' => 'SKU',
            'pdf_catalogs' => 'Catálogos PDF',
            'catalog_configuration' => 'Configuración del catálogo',
            'create_catalog' => 'Crear catálogo',
            'edit_catalog' => 'Editar catálogo',
            'catalog_cover' => 'Portada del catálogo',
            'catalog_name' => 'Nombre del catálogo',
            'catalog_details' => 'Detalles del catálogo',
            'generated_catalogs' => 'Catálogos generados',
            'generate_pdf_catalog' => 'Generar catálogo PDF',
            'update_pdf_catalog' => 'Actualizar catálogo PDF',
            'create_new_catalog' => 'Crear nuevo catálogo',
            'first_catalog_image' => 'Primera imagen del catálogo',
            'title_position' => 'Posición del título',
            'advisor_name' => 'Nombre del asesor',
            'advisor_phone' => 'Teléfono del asesor',
            'advisor_email' => 'Correo del asesor',
            'advertising' => 'Publicidad',
            'add_advertising' => 'Agregar publicidad',
            'advertising_image' => 'Imagen de publicidad',
            'show_after_letter' => 'Mostrar después de la letra',
            'selected_products' => 'Productos seleccionados',
            'loaded_products' => 'Productos cargados',
            'unavailable_products' => 'Productos no disponibles',
            'select_all_visible' => 'Seleccionar todos los visibles',
            'select_categories_to_load_products' => 'Selecciona categorías para cargar productos',
            'loading_products' => 'Cargando productos',
            'no_products_found_for_the_selected_categories' => 'No se encontraron productos para las categorías seleccionadas',
            'products_could_not_be_loaded' => 'No se pudieron cargar los productos',
            'no_products_match_your_search' => 'Ningún producto coincide con tu búsqueda',
            'download' => 'Descargar',
            'created_at' => 'Creado el',
            'updated' => 'Actualizado',
        ];
    }

    private function manualValueTranslations(): array
    {
        return [
            'Language changed to ' => 'Idioma cambiado a ',
            'Default language updated successfully' => 'Idioma predeterminado actualizado correctamente',
            'Language has been inserted successfully' => 'Idioma creado correctamente',
            'Language has been updated successfully' => 'Idioma actualizado correctamente',
            'Translations updated for ' => 'Traducciones actualizadas para ',
            'Status updated successfully' => 'Estado actualizado correctamente',
            'RTL status updated successfully' => 'Estado RTL actualizado correctamente',
            'Default language can not be disbaled' => 'El idioma predeterminado no se puede deshabilitar',
            'Minimum 1 language need to be enabled' => 'Debe haber al menos un idioma habilitado',
            'Default language can not be deleted' => 'El idioma predeterminado no se puede eliminar',
            'English language can not be deleted' => 'El idioma inglés no se puede eliminar',
            'Language has been deleted successfully' => 'Idioma eliminado correctamente',
            'This code is already used for another language' => 'Este código ya está en uso por otro idioma',
            'Default language code can not be changed' => 'El código del idioma predeterminado no se puede cambiar',
            'English language code can not be changed' => 'El código del idioma inglés no se puede cambiar',
        ];
    }

    private function setEnvValue(string $key, string $value): void
    {
        $path = base_path('.env');

        if (! file_exists($path)) {
            return;
        }

        $content = file_get_contents($path);
        $line = $key . '="' . $value . '"';

        if (preg_match('/^' . preg_quote($key, '/') . '=.*$/m', $content)) {
            $content = preg_replace('/^' . preg_quote($key, '/') . '=.*$/m', $line, $content);
        } else {
            $content = rtrim($content) . PHP_EOL . $line . PHP_EOL;
        }

        file_put_contents($path, $content);
    }
};
