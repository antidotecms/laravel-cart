<?php

namespace Antidote\LaravelCartStripe\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class MakeStripePaymentTableCommand extends Command
{
    protected $signature = 'cart:stripe-payment-table';

    protected $description = 'Command description';

    protected Filesystem $files;

    public function __construct(Filesystem $files)
    {
        parent::__construct();
        $this->files = $files;
    }

    public function handle()
    {
        $contents = $this->substituteVariables();

        $migration_dir = database_path('migrations');

        $date = Carbon::now()->format('Y_m_d_Gis');

        $filename =  $date . '_create_stripe_payments_table.php';
        $this->files->put($migration_dir . '/' .$filename, $contents);
        $this->info("Migration $filename created.");
    }

    public function getStubVariables()
    {
        return [
            'order_key' => getKeyFor('order')
        ];
    }

    public function getStubPath()
    {
        return __DIR__.'../../../stubs/create-stripe-payment-table.stub';
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
