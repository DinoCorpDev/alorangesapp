<?php

namespace App\Http\Services;

/**
 * Reads and writes the catalog index and configuration files under
 * storage/app/product_catalogs.
 *
 * Generation runs in a detached background process, so the web request and that process
 * both write catalogs.json. Every write goes through mutate(), which holds an exclusive
 * lock for the whole read-modify-write cycle — otherwise a status update and a
 * configuration save can overwrite each other.
 */
class ProductCatalogStore
{
    public const STATUS_QUEUED = 'queued';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_READY = 'ready';
    public const STATUS_FAILED = 'failed';

    public function all(): array
    {
        return array_map([$this, 'normalize'], $this->read($this->indexPath()));
    }

    public function find(?string $id): ?array
    {
        if (! $id) {
            return null;
        }

        foreach ($this->all() as $catalog) {
            if (($catalog['id'] ?? null) === $id) {
                return $catalog;
            }
        }

        return null;
    }

    /**
     * Inserts a new catalog at the top of the list, or replaces the existing one in place.
     */
    public function put(array $catalog): array
    {
        return $this->mutate(function (array $catalogs) use ($catalog) {
            $replaced = false;

            foreach ($catalogs as $index => $existing) {
                if (($existing['id'] ?? null) === $catalog['id']) {
                    $catalogs[$index] = $catalog;
                    $replaced = true;
                    break;
                }
            }

            if (! $replaced) {
                array_unshift($catalogs, $catalog);
            }

            return $catalogs;
        }, $catalog['id']);
    }

    /**
     * Merges $changes into a single catalog without touching the rest of the file.
     */
    public function update(string $id, array $changes): ?array
    {
        return $this->mutate(function (array $catalogs) use ($id, $changes) {
            foreach ($catalogs as $index => $existing) {
                if (($existing['id'] ?? null) === $id) {
                    $catalogs[$index] = array_merge($existing, $changes);
                    break;
                }
            }

            return $catalogs;
        }, $id);
    }

    public function forget(string $id): void
    {
        $this->mutate(function (array $catalogs) use ($id) {
            return array_values(array_filter($catalogs, function ($catalog) use ($id) {
                return ($catalog['id'] ?? null) !== $id;
            }));
        });
    }

    public function defaults(): array
    {
        return array_merge($this->defaultSettings(), $this->read($this->defaultsPath()));
    }

    public function saveDefaults(array $settings): void
    {
        $this->write($this->defaultsPath(), $settings);
    }

    public function sharedBlocks(): array
    {
        return array_merge([
            'payment_page_image' => null,
            'info_page_image' => null,
            'updated_at' => null,
        ], $this->read($this->sharedBlocksPath()));
    }

    public function defaultSettings(): array
    {
        return [
            'show_prices' => true,
            'show_payment_page' => true,
            'show_info_page' => true,
            'show_page_four' => false,
            'description_limit' => 90,
            'products_per_page' => 12,
            'cover_image' => null,
            'cover_category_images' => [],
            'cover_title_position' => 'middle',
            'advisor_name' => '',
            'advisor_phone' => '',
            'advisor_email_1' => '',
            'advisor_email_2' => '',
            'advertising_image' => null,
            'advertising_position' => 'before_products',
            'advertising_items' => [],
            'letter_intro_ads' => [],
            'payment_page_image' => null,
            'payment_bank_icon' => null,
            'payment_debit_icon' => null,
            'payment_credit_icon' => null,
            'payment_cash_icon' => null,
            'info_page_image' => null,
            'page_four_image' => null,
            'extra_page_images' => [],
            'final_page_image' => null,
            'final_page_blank' => false,
            'payment_page_position' => 'start',
            'info_page_position' => 'start',
            'extra_pages_position' => 'start',
            'additional_pages' => [],
            'payment_title' => 'MEDIOS DE PAGO',
            'payment_delivery_title' => 'EFECTIVO CONTRA ENTREGA, DEPOSITO O TRANSFERENCIA DIRECTA',
            'payment_bank_info' => '',
            'payment_debit_title' => 'TARJETAS DEBITO',
            'payment_debit_info' => '',
            'payment_credit_title' => 'TARJETAS CREDITO',
            'payment_credit_info' => '',
            'payment_cash_title' => 'PAGUE EN EFECTIVO EN MAS DE 14.000 PUNTOS',
            'payment_cash_info' => '',
            'info_page_title' => 'INFORMACION',
            'info_page_content' => '',
            'info_table_rows' => [],
            'product_title_font_family' => 'DejaVu Sans',
            'product_title_font_size' => 12,
            'product_description_font_family' => 'DejaVu Sans',
            'product_description_font_size' => 10,
            'product_price_font_family' => 'DejaVu Sans',
            'product_price_font_size' => 16,
            'product_reference_font_family' => 'DejaVu Sans',
            'product_reference_font_size' => 12,
            'product_box_colors' => [],
            'product_text_colors' => [],
        ];
    }

    /**
     * Fills in fields added after a catalog was first saved. Catalogs written before
     * generation moved to the background have no status but do have a file, so they count
     * as ready.
     */
    protected function normalize(array $catalog): array
    {
        $catalog['settings'] = array_merge($this->defaultSettings(), $catalog['settings'] ?? []);
        $catalog['category_ids'] = $catalog['category_ids'] ?? [];
        $catalog['product_ids'] = $catalog['product_ids'] ?? [];
        $catalog['updated_at'] = $catalog['updated_at'] ?? null;
        $catalog['status'] = $catalog['status'] ?? (empty($catalog['file_path']) ? self::STATUS_FAILED : self::STATUS_READY);
        $catalog['status_message'] = $catalog['status_message'] ?? null;
        $catalog['generated_at'] = $catalog['generated_at'] ?? null;
        $catalog['products_count'] = (int) ($catalog['products_count'] ?? count($catalog['product_ids']));

        return $catalog;
    }

    /**
     * Runs $mutator against the catalog list while holding an exclusive lock on the file.
     */
    protected function mutate(callable $mutator, ?string $returnId = null): ?array
    {
        $path = $this->indexPath();
        $directory = dirname($path);

        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $handle = fopen($path, 'c+');

        if ($handle === false) {
            throw new \RuntimeException('No se pudo abrir el archivo de catalogos: ' . $path);
        }

        try {
            flock($handle, LOCK_EX);

            $raw = stream_get_contents($handle);
            $catalogs = json_decode((string) $raw, true);
            $catalogs = is_array($catalogs) ? $catalogs : [];

            $catalogs = $mutator($catalogs);

            rewind($handle);
            ftruncate($handle, 0);
            fwrite($handle, json_encode($catalogs, JSON_PRETTY_PRINT));
            fflush($handle);
        } finally {
            flock($handle, LOCK_UN);
            fclose($handle);
        }

        if ($returnId === null) {
            return null;
        }

        foreach ($catalogs as $catalog) {
            if (($catalog['id'] ?? null) === $returnId) {
                return $this->normalize($catalog);
            }
        }

        return null;
    }

    protected function read(string $path): array
    {
        if (! file_exists($path)) {
            return [];
        }

        $content = json_decode((string) file_get_contents($path), true);

        return is_array($content) ? $content : [];
    }

    protected function write(string $path, array $content): void
    {
        $directory = dirname($path);

        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        file_put_contents($path, json_encode($content, JSON_PRETTY_PRINT));
    }

    public function indexPath(): string
    {
        return storage_path('app/product_catalogs/catalogs.json');
    }

    public function defaultsPath(): string
    {
        return storage_path('app/product_catalogs/defaults.json');
    }

    public function sharedBlocksPath(): string
    {
        return storage_path('app/product_catalogs/shared_blocks.json');
    }
}
