<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('test_payment_methods', function (Blueprint $table) {
            $table->id();

            $table->integer('test_order_id');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('test_payment_methods');
    }
};
