<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('test_adjustments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('class');
            $table->json('parameters');
//            $table->string('type');
//            $table->string('rate');
//            $table->integer('amount');
//            $table->foreignIdFor(config('laravel-cart.classes.order'));
//            $table->boolean('is_in_subtotal');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('test_adjustments');
    }
};
