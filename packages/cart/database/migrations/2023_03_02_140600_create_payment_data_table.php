<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payment_data', function (Blueprint $table) {
            $table->id();

            $table->string('key');
            $table->string('value');
            $table->foreignIdFor(\Antidote\LaravelCart\Models\Payment::class);
            $table->unique(['payment_id', 'key']);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_data');
    }
};
