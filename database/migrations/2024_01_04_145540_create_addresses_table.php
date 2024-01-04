<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->string('address_line_1');
            $table->string('address_line_2')->nullable();
            $table->string('town_city');
            $table->string('county');
            $table->string('postcode');
            $table->foreignIdFor(\Antidote\LaravelCart\Models\Customer::class);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
