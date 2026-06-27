<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\Api\ProductController;

class CronUpdateAlegra extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alegra:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Actualiza los productos de Alegra, no elimina productos ya no existentes, pendiente crear logica';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->alegraUpdate = new ProductController();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->alegraUpdate->alegra();
        return 0;
    }
}
