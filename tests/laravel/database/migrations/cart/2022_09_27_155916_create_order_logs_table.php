<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('test_order_log_items', function (Blueprint $table) {
            $table->id();

            $table->string('message');
            $table->string('test_order_id');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('test_order_log_items');
    }
};
