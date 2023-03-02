<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('status')->nullable();
            $table->integer('customer_id');
            //@todo change to $table->morphs();
            $table->string('payment_id')->nullable();
            $table->string('payment_type')->nullable();

            //@todo create facade to simplify comparing objects and classes - maybe package it
            if(is_subclass_of(config('laravel-cart.classes.order'), \Antidote\LaravelCartStripe\Models\StripeOrder::class) ||
                config('laravel-cart.classes.order') == \Antidote\LaravelCartStripe\Models\StripeOrder::class) {
                $table->string('payment_intent_id')->nullable();
            }

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('test_orders');
    }
};
