<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddServiceIdToTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('carts', function (Blueprint $table) {
            $table->integer('service_id')->after('product_variation_id')->nullable();
        });

        Schema::table('order_details', function (Blueprint $table) {
            $table->integer('service_id')->after('product_variation_id')->nullable();
        });

        Schema::table('reviews', function (Blueprint $table) {
            $table->integer('service_id')->after('product_id')->nullable();
        });

        Schema::table('wishlists', function (Blueprint $table) {
            $table->integer('service_id')->after('product_id')->nullable();
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
            $table->dropColumn('service_id');
        });

        Schema::table('order_details', function (Blueprint $table) {
            $table->dropColumn('service_id');
        });

        Schema::table('reviews', function (Blueprint $table) {
            $table->dropColumn('service_id');
        });

        Schema::table('wishlists', function (Blueprint $table) {
            $table->dropColumn('service_id');
        });
    }
}
