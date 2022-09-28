<?php

use Antidote\LaravelCartStripe\Commands\MakeStripePaymentTableCommand;
use Carbon\Carbon;
use Illuminate\Filesystem\Filesystem;

it('will create the stripe migration', function() {

    $migration_dir = database_path('migrations');
    $date = Carbon::now()->format('Y_m_d_Gis');
    $filename =  $date . '_create_stripe_payments_table.php';

    $contents = (new MakeStripePaymentTableCommand(app(Filesystem::class)))->substituteVariables();

    $this->assertStringContainsString("\$table->integer('test_order_id');", $contents);

    File::shouldReceive('put')
        ->withArgs([
            $migration_dir . '/' . $filename,
            $contents
        ])
    ->once()
    ->andReturnNull();

    $this->artisan('cart:stripe-payment-table')
        ->expectsOutput("Migration $filename created.")
        ->assertExitCode(0);
});
