<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('order_adjustments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('amount');
            $table->json('original_parameters');
            $table->boolean('apply_to_subtotal')->nullable();
            $table->foreignIdFor(\Antidote\LaravelCart\Models\Order::class);
            //$table->foreignIdFor(config('laravel-cart.classes.adjustment'));
            $table->string('class');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('test_order_adjustments');
    }
};
