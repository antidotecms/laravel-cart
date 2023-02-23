<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create(app(config('laravel-cart.classes.order_log_item'))->getTable(), function (Blueprint $table) {
            $table->id();

            $table->string('message');

            match(config('laravel-cart.classes.order_log_item')) {
                \Antidote\LaravelCart\Tests\Fixtures\App\Models\TestStripeOrderLogItem::class => $table->json('event')->nullable(),
                \Antidote\LaravelCart\Tests\Fixtures\App\Models\TestOrderLogItem::class => null
            };

            $table->foreignIdFor(config('laravel-cart.classes.order'));

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('test_order_log_items');
    }
};
