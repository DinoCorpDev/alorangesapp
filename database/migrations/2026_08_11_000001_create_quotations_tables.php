<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('quotations', function (Blueprint $table) {
            $table->id();

            // Numero visible de la cotizacion (el del PDF, p.ej. 5819).
            // Unico para que no se dupliquen entre comerciales.
            $table->unsignedInteger('number')->unique();

            // Datos del cliente. Se guardan copiados y no por relacion: una
            // cotizacion es un documento historico y no debe cambiar si
            // luego se edita la ficha del cliente.
            $table->string('customer_name');
            $table->string('customer_document')->nullable();
            $table->text('customer_address')->nullable();
            $table->string('customer_phone')->nullable();
            $table->string('customer_email')->nullable();

            $table->date('issue_date');
            $table->date('expiry_date')->nullable();

            $table->text('observations')->nullable();

            // Totales calculados en el servidor al guardar. Se almacenan para
            // que el listado y el PDF no tengan que recalcular cada vez.
            $table->decimal('subtotal', 20, 2)->default(0);
            $table->decimal('discount_total', 20, 2)->default(0);
            $table->decimal('tax_total', 20, 2)->default(0);
            $table->decimal('total', 20, 2)->default(0);

            // Desglose de IVA por tarifa ({"19": 33051.00, "5": 2096.00}),
            // porque la plantilla muestra una linea por cada porcentaje.
            $table->json('tax_breakdown')->nullable();

            $table->unsignedBigInteger('created_by')->nullable();
            $table->string('created_by_name')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index('issue_date');
            $table->index('customer_name');
        });

        Schema::create('quotation_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('quotation_id');

            // Nullable a proposito: permite anadir lineas libres que no
            // correspondan a un producto del catalogo.
            $table->unsignedBigInteger('product_id')->nullable();

            // Referencia y nombre se copian del producto en el momento de
            // cotizar, por el mismo motivo que los datos del cliente.
            $table->string('reference')->nullable();
            $table->string('name');

            $table->decimal('unit_price', 20, 2)->default(0);
            $table->decimal('quantity', 12, 2)->default(1);
            $table->decimal('discount_percent', 5, 2)->default(0);
            $table->decimal('tax_percent', 5, 2)->default(0);

            $table->decimal('line_subtotal', 20, 2)->default(0);
            $table->decimal('line_tax', 20, 2)->default(0);
            $table->decimal('line_total', 20, 2)->default(0);

            $table->unsignedInteger('position')->default(0);

            $table->timestamps();

            $table->index('quotation_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('quotation_items');
        Schema::dropIfExists('quotations');
    }
};
