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
        Schema::table('carts', function (Blueprint $table) {
            $table->integer('collection_id')->after('product_variation_id')->nullable();
        });

        Schema::table('order_details', function (Blueprint $table) {
            $table->integer('collection_id')->after('product_variation_id')->nullable();
        });

        Schema::table('reviews', function (Blueprint $table) {
            $table->integer('collection_id')->after('service_id')->nullable();
        });

        Schema::table('wishlists', function (Blueprint $table) {
            $table->integer('collection_id')->after('service_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('carts', function (Blueprint $table) {
            $table->dropColumn('collection_id');
        });

        Schema::table('order_details', function (Blueprint $table) {
            $table->dropColumn('collection_id');
        });

        Schema::table('reviews', function (Blueprint $table) {
            $table->dropColumn('collection_id');
        });

        Schema::table('wishlists', function (Blueprint $table) {
            $table->dropColumn('collection_id');
        });
    }
};
