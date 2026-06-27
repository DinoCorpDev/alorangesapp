<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('collection_variations', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('collection_id');
            $table->string('code', 50)->nullable();
            $table->string('sku', 100)->nullable();
            $table->double('price', 20, 2);
            $table->tinyInteger('stock');
            $table->integer('img');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('collection_variations');
    }
};
