<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCollectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('collections', function (Blueprint $table) {
            $table->id();
            $table->integer('shop_id')->nullable();
            $table->text('reference')->nullable();
            $table->string('name', 200);
            $table->bigInteger('brand_id')->nullable();
            $table->string('photos', 2000)->nullable();
            $table->string('thumbnail_img', 100)->nullable();
            $table->longText('description')->nullable();
            $table->double('lowest_price', 20, 2)->default(0.00);
            $table->double('highest_price', 20, 2)->default(0.00);
            $table->double('discount', 20, 2)->default(0.00);
            $table->string('discount_type', 10)->default('flat');
            $table->integer('discount_start_date')->nullable();
            $table->integer('discount_end_date')->nullable();
            $table->text('currency')->nullable();
            $table->tinyInteger('stock')->default(1);
            $table->tinyInteger('published')->default(1);
            $table->tinyInteger('is_variant')->default(0);
            $table->tinyInteger('has_warranty')->default(0);
            $table->text('warranty_text', 1)->nullable();
            $table->mediumText('meta_title')->nullable();
            $table->longText('meta_description')->nullable();
            $table->integer('meta_image')->nullable();
            $table->mediumText('slug', 10);
            $table->double('rating', 8, 2)->default(0.00);
            $table->integer('num_of_sale')->default(0);
            $table->text('material')->nullable();
            $table->text('unit_metering')->nullable();
            $table->text('engaste')->nullable();
            $table->string('medida_de_proyecto')->nullable();
            $table->string('si1')->nullable();
            $table->string('aforo')->nullable();
            $table->string('diagrama_proyecto')->nullable();
            $table->string('postventa')->nullable();
            $table->string('imagen1')->nullable();
            $table->string('imagen2')->nullable();
            $table->string('imagen3')->nullable();
            $table->string('imagen4')->nullable();
            $table->string('video1')->nullable();
            $table->string('video2')->nullable();
            $table->string('video3')->nullable();
            $table->string('video4')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('collections');
    }
}
