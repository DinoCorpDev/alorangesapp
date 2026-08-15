<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('quotation_items', function (Blueprint $table) {
            // Ruta de la imagen del producto en el momento de cotizar.
            // Se copia igual que la referencia y el nombre: si manana cambia
            // la foto del producto, la cotizacion ya enviada no debe cambiar.
            $table->string('image_path', 500)->nullable()->after('name');
        });
    }

    public function down()
    {
        Schema::table('quotation_items', function (Blueprint $table) {
            $table->dropColumn('image_path');
        });
    }
};
