<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\UpdatePaymentStatusJob;

class DispatchUpdatePayments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payments:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Despacha el job que actualiza los pagos desde Wompi';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        UpdatePaymentStatusJob::dispatch();
    }
}
