<?php

namespace Antidote\LaravelCart\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class CreateMigrationCommand extends Command
{
    protected $signature = 'cart:migration
        {--type=}
    ';

    protected $description = 'Create database tables for use with laravel-cart';

    protected Filesystem $files;

    public function __construct(Filesystem $files)
    {
        parent::__construct();
        $this->files = $files;
    }

    public function getMigrationName() : string
    {
        return
            Str::of(class_basename(getClassNameFor($this->option('type'))))->snake()->lower().'s_table';
    }

    public function getStubVariables() : array
    {
        $type = $this->option('type');

        return match($type) {
            'payment' => [
                'order_key' => getKeyFor('order'),
                'table_name' => getTableNameFor('payment')
            ],
            'stripe_payment' => [
                'order_key' => getKeyFor('order'),
                'table_name' => 'stripe_payments'
            ],
            'order_adjustment' => [
                'order_key' => getKeyFor('order'),
                'table_name' => getTableNameFor('order_adjustment')
            ],
            'order' => [
                'customer_key' => getKeyFor('customer'),
                'table_name' => getTableNameFor('order')
            ],
            'order_item' => [
                'product_key' => getKeyFor('product'),
                'order_key' => getKeyFor('order'),
                'table_name' => getTableNameFor('order_item')
            ],
            'customer' => [
                'table_name' => getTableNameFor('customer')
            ],
            'order_log_item' => [
                'order_key' => getKeyFor('order'),
                'table_name' => getTableNameFor('order_log_item')
            ],
            'product' => [
                'table_name' => getTableNameFor('product')
            ],
            'stripe_order_log_item' => [
                'order_key' => getKeyFor('order'),
                'table_name' => 'stripe_order_log_items'
            ]

        };
    }

    public function getStubPath()
    {
        return match($this->option('type')) {
            'payment' => __DIR__.'../../../stubs/migrations/create-payments-table.stub',
            'order_adjustment' => __DIR__.'../../../stubs/migrations/create-order-adjustments-table.stub',
            'order' => __DIR__.'../../../stubs/migrations/create-orders-table.stub',
            'order_item' => __DIR__.'../../../stubs/migrations/create-order-items-table.stub',
            'customer' => __DIR__.'../../../stubs/migrations/create-customers-table.stub',
            'order_log_item' => __DIR__.'../../../stubs/migrations/create-order-log-items-table.stub',
            'product' => __DIR__.'../../../stubs/migrations/create-products-table.stub',
            'stripe_order_log_item' => __DIR__.'../../../../stripe/stubs/migrations/create-stripe-order-log-items-table.stub',
            'stripe_payment' => __DIR__.'../../../../stripe/stubs/migrations/create-stripe-payments-table.stub'
        };
    }

    public function handle()
    {
        $contents = $this->substituteVariables();

        //$filename = $this->getMigrationFilename();
        $filename = Carbon::now()->format('Y_m_d_Gis') .
            '_create_' .
            $this->getStubVariables()['table_name'].'_table.php';

        $this->files->put(
            database_path('migrations') . '/' . $filename,
            $contents
        );

        $this->info("Migration $filename created.");
    }

    private function getMigrationFilename() : string
    {
        return Carbon::now()->format('Y_m_d_Gis') .
            '_create_' . Str::snake($this->getMigrationName()) . '.php';
    }

    public function substituteVariables()
    {
        $contents = file_get_contents($this->getStubPath());

        $variables = $this->getStubVariables();

        foreach($variables as $search => $replace)
        {
            $contents = str_replace('$'.$search.'$', $replace, $contents);
        }

        return $contents;
    }
}
