<?php

namespace App\Console\Commands;

use App\Jobs\SyncAlegraProductsJob;
use Illuminate\Console\Command;

class SyncAlegraProductsCommand extends Command
{
    protected $signature = 'alegra:sync-products';

    protected $description = 'Importa/actualiza los productos desde Alegra (worker invocado en segundo plano)';

    public function handle(): int
    {
        (new SyncAlegraProductsJob)->handle();

        return self::SUCCESS;
    }
}
