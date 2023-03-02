<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('adjustments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('class');
            $table->json('parameters');
            $table->boolean('apply_to_subtotal')->default(true);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_valid')->default(true);
            $table->boolean('calculated_amount')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('test_adjustments');
    }
};
