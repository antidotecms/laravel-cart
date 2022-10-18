<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('stripe_payments', function (Blueprint $table) {
            $table->id();

            $table->integer('test_stripe_order_id');
            $table->string('client_secret')->default('');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('stripe_payments');
    }
};
