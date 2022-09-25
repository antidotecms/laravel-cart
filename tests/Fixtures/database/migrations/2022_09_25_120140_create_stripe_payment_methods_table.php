<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('stripe_payment_methods', function (Blueprint $table) {
            $table->id();

            $table->integer('test_order_id');
            $table->json('data');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('stripe_payment_methods');
    }
};
