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
        Schema::create(app(config('laravel-cart.classes.order'))->getTable(), function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('status')->nullable();
            $table->integer('test_customer_id');
            $table->string('payment_id')->nullable();
            $table->string('payment_type')->nullable();

            match(config('laravel-cart.classes.order')) {
                \Antidote\LaravelCart\Tests\Fixtures\App\Models\TestOrder::class => $table->string('additional_field')->nullable(),
                \Antidote\LaravelCart\Tests\Fixtures\App\Models\TestStripeOrder::class => $table->string('payment_intent_id')->nullable()
            };

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
