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
        Schema::create('test_order_items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('test_product_id');
            $table->json('product_data')->nullable();
            $table->integer('price');
            $table->integer('quantity');
            //$table->integer('test_order_id');
            $table->foreignIdFor(config('laravel-cart.classes.order'));
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
        Schema::dropIfExists('test_order_items');
    }
};
