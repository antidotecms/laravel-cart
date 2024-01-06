<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            $table->string('payment_method_type');
            $table->json('data')->nullable();
            $table->foreignIdFor(\Antidote\LaravelCart\Models\Order::class);

            $table->timestamps();
        });
    }
};