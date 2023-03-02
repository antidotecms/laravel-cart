<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(\Antidote\LaravelCart\Models\Order::class);

            match(config('laravel-cart.classes.payment')) {
                \Antidote\LaravelCartStripe\Models\StripePayment::class => $table->string('client_secret')->default(''),
                //@todo remove below as only used for testing?
                \Antidote\LaravelCart\Tests\Fixtures\App\Models\TestPayment::class => $table->json('body')->nullable(),
                default => null
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
