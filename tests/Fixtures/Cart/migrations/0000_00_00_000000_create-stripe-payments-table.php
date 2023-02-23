<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create(app(config('laravel-cart.classes.payment'))->getTable(), function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(config('laravel-cart.classes.order'));

            match(config('laravel-cart.classes.payment')) {
                \Antidote\LaravelCartStripe\Models\StripePayment::class => $table->string('client_secret')->default(''),
                \Antidote\LaravelCart\Tests\Fixtures\App\Models\TestPayment::class => $table->json('body')->nullable()
            };

            //$table->string('client_secret')->default('');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('$table_name$');
    }
};
