<?php

namespace App\Console\Commands;

use App\Jobs\GenerateProductCatalogJob;
use Illuminate\Console\Command;

class GenerateProductCatalogCommand extends Command
{
    protected $signature = 'catalogs:generate {catalog : Id del catalogo en catalogs.json}';

    protected $description = 'Genera el PDF de un catalogo de productos (worker invocado en segundo plano)';

    public function handle(): int
    {
        (new GenerateProductCatalogJob)->handle((string) $this->argument('catalog'));

        return self::SUCCESS;
    }
}
