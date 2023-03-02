<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('order_data', function (Blueprint $table) {
            $table->id();

            $table->string('key');
            $table->string('value');
            $table->foreignIdFor(\Antidote\LaravelCart\Models\Order::class);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_data');
    }
};
