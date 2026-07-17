<?php

namespace App\Jobs;

use App\Http\Services\AlegraServices;
use App\Models\Category;
use App\Models\Product;
use App\Models\Upload;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;
use Throwable;

class SyncAlegraProductsJob
{
    public const CACHE_KEY = 'alegra_products_import_status';
    public const ACTIVE_STATUSES = ['starting', 'running'];

    private const LAST_TOTAL_CACHE_KEY = 'alegra_products_last_total';

    private const PRICE_LIST = 'PUNTO DE VENTA';
    private const IMAGE_FIELD_INDEX = [
        'thumbnail_img' => 0,
        'imagen1' => 1,
        'imagen2' => 2,
        'imagen3' => 3,
        'imagen4' => 4,
    ];

    private array $categoryIds = [];

    /**
     * Starts the import as a detached OS process (no queue worker required)
     * and returns the current status. No-ops if an import is already running.
     */
    public static function trigger(): array
    {
        $status = Cache::get(self::CACHE_KEY, []);

        if (in_array($status['status'] ?? null, self::ACTIVE_STATUSES)) {
            return $status;
        }

        $phpBinary = self::resolvePhpBinary();
        $artisan = base_path('artisan');

        if (PHP_OS_FAMILY === 'Windows') {
            // On Windows, a plain proc_open child is tied to the spawning
            // process and dies with it. "start /B" fully detaches it.
            $cmd = 'start "" /B '.escapeshellarg($phpBinary).' '.escapeshellarg($artisan).' alegra:sync-products';
            $process = Process::fromShellCommandline($cmd);
        } else {
            $process = new Process([$phpBinary, $artisan, 'alegra:sync-products']);
        }

        $process->setTimeout(null);
        $process->setIdleTimeout(null);
        $process->disableOutput();
        $process->start();

        $status = ['status' => 'starting'];
        Cache::put(self::CACHE_KEY, $status, now()->addHours(6));

        return $status;
    }

    /**
     * PhpExecutableFinder relies on PHP_BINARY / PATH, which are unreliable when
     * PHP runs in-process inside the web server (e.g. Apache + mod_php): PHP_BINARY
     * then points at httpd.exe, not a usable CLI binary, so find() returns empty.
     */
    private static function resolvePhpBinary(): string
    {
        $isPhpCli = fn ($path) => $path && stripos(basename((string) $path), 'php') === 0;

        $found = (new PhpExecutableFinder)->find();
        if ($isPhpCli($found) && File::exists($found)) {
            return $found;
        }

        if ($isPhpCli(PHP_BINARY) && File::exists(PHP_BINARY)) {
            return PHP_BINARY;
        }

        if (PHP_OS_FAMILY === 'Windows') {
            $candidate = 'C:/laragon/bin/php/php-'.PHP_VERSION.'/php.exe';
            if (File::exists($candidate)) {
                return $candidate;
            }

            $matches = glob('C:/laragon/bin/php/php-*/php.exe');
            if (!empty($matches)) {
                return $matches[0];
            }
        }

        return 'php';
    }

    public function handle(): void
    {
        $estimatedTotal = Cache::get(self::LAST_TOTAL_CACHE_KEY);

        // Kept in memory and written whole on every update so a single
        // Cache::put() call can never lose fields from a prior read.
        $state = [
            'status' => 'running',
            'imported' => 0,
            'total' => $estimatedTotal,
            'total_is_estimate' => $estimatedTotal !== null,
            'errors' => 0,
            'started_at' => now()->toIso8601String(),
            'finished_at' => null,
        ];

        try {
            $this->writeStatus($state);

            $this->categoryIds = Category::pluck('id')->flip()->all();

            $alegra = new AlegraServices;
            $processed = 0;

            $alegra->eachProduct(function ($product) use (&$state, &$processed) {
                try {
                    if ($this->syncAlegraProduct($product)) {
                        $state['imported']++;
                    }
                } catch (Exception $e) {
                    $state['errors']++;
                    report($e);
                }

                // Throttle disk writes: update every few products instead of every single one.
                if (++$processed % 5 === 0) {
                    $this->writeStatus($state);
                }
            });

            Cache::put(self::LAST_TOTAL_CACHE_KEY, $state['imported'], now()->addDays(30));

            $state['status'] = 'completed';
            $state['total'] = $state['imported'];
            $state['total_is_estimate'] = false;
            $state['finished_at'] = now()->toIso8601String();
            $this->writeStatus($state);
        } catch (Throwable $e) {
            report($e);
            $state['status'] = 'failed';
            $state['finished_at'] = now()->toIso8601String();
            $this->writeStatus($state);
        }
    }

    private function writeStatus(array $data): void
    {
        Cache::put(self::CACHE_KEY, $data, now()->addHours(6));
    }

    private function syncAlegraProduct(array $product): bool
    {
        if (!isset($product['id'])) {
            return false;
        }

        $categoryId = $this->resolveAlegraCategoryId($product);
        $productStorage = Product::find($product['id']) ?: new Product;
        $productStorage->id = $product['id'];
        $productStorage->name = $product['name'] ?? '';
        $productStorage->reference = $product['reference'] ?? '';
        $productStorage->description = $product['description'] ?? '';
        $productStorage->tax = $this->getAlegraTaxPercentage($product);
        $price = $this->calculateAlegraPrice($product, self::PRICE_LIST);
        $productStorage->lowest_price = $price;
        $productStorage->highest_price = $price;
        $productStorage->shop_id = 1;
        $productStorage->slug = $productStorage->slug ?: Str::slug($productStorage->name, '-') . '-' . strtolower(Str::random(5));
        $productStorage->published = ($product['status'] ?? '') == 'active' ? 1 : 0;

        $this->syncAlegraProductImages($productStorage, $product);

        $productStorage->save();

        if ($categoryId && isset($this->categoryIds[$categoryId])) {
            $productStorage->categories()->sync([$categoryId]);
        }

        return true;
    }

    private function resolveAlegraCategoryId(array $product)
    {
        return $product['itemCategory']['id']
            ?? $product['category']['id']
            ?? $product['idItemCategory']
            ?? null;
    }

    private function getAlegraTaxPercentage(array $product)
    {
        return isset($product['tax'][0]['percentage']) ? (float) $product['tax'][0]['percentage'] : 0;
    }

    private function calculateAlegraPrice(array $product, string $priceListName)
    {
        $percentage = $this->getAlegraTaxPercentage($product);
        $prices = $product['price'] ?? [];
        $basePrice = 0;

        foreach ($prices as $listPrice) {
            if (($listPrice['name'] ?? '') === $priceListName) {
                $basePrice = (float) ($listPrice['price'] ?? 0);
                break;
            }
        }

        if ($basePrice == 0 && isset($prices[0]['price'])) {
            $basePrice = (float) $prices[0]['price'];
        }

        return floor($basePrice * (($percentage / 100) + 1));
    }

    private function syncAlegraProductImages(Product $productStorage, array $product): void
    {
        $images = array_values($product['images'] ?? []);

        $slots = [];
        foreach (self::IMAGE_FIELD_INDEX as $field => $index) {
            $slots[$field] = $images[$index === 0 ? 0 : $index - 1]['url'] ?? null;
        }

        $pending = [];
        foreach ($slots as $field => $url) {
            if (!$url) {
                if (!$productStorage->{$field}) {
                    $productStorage->{$field} = null;
                }
                continue;
            }

            if (!filter_var($url, FILTER_VALIDATE_URL) || $this->hasLocalUpload($productStorage->{$field})) {
                continue;
            }

            $pending[$url][] = $field;
        }

        if (empty($pending)) {
            return;
        }

        $urls = array_keys($pending);
        $responses = Http::pool(fn ($pool) => array_map(
            fn ($url) => $pool->as($url)
                ->timeout(45)
                ->withOptions(['verify' => false])
                ->retry(2, 300)
                ->get($url),
            $urls
        ));

        foreach ($pending as $url => $fields) {
            $response = $responses[$url] ?? null;

            if (!$response || $response instanceof Throwable || !$response->successful() || empty($response->body())) {
                continue;
            }

            foreach ($fields as $field) {
                $productStorage->{$field} = $this->persistAlegraImage(
                    $response->body(),
                    $response->header('Content-Type'),
                    $url,
                    $product['id'],
                    self::IMAGE_FIELD_INDEX[$field]
                );
            }
        }
    }

    private function persistAlegraImage(string $body, ?string $contentType, string $url, $productId, int $index): ?int
    {
        try {
            $path = parse_url($url, PHP_URL_PATH) ?: '';
            $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
            $extension = in_array($extension, ['jpg', 'jpeg', 'png', 'webp', 'gif']) ? $extension : 'jpg';
            $hash = substr(sha1($path ?: $url), 0, 16);
            $fileName = 'alegra-'.$productId.'-'.$index.'-'.$hash.'.'.$extension;
            $relativePath = 'uploads/all/'.$fileName;
            $absolutePath = public_path($relativePath);

            File::ensureDirectoryExists(dirname($absolutePath));
            File::put($absolutePath, $body);

            $upload = Upload::where('file_name', $relativePath)->first() ?: new Upload;
            $upload->file_original_name = $fileName;
            $upload->file_name = $relativePath;
            $upload->user_id = 1;
            $upload->extension = $extension;
            $upload->type = str_contains((string) $contentType, 'image') ? 'image' : 'others';
            $upload->file_size = File::size($absolutePath);
            $upload->save();

            return $upload->id;
        } catch (Exception $e) {
            report($e);
            return null;
        }
    }

    private function hasLocalUpload($value): bool
    {
        if (!$value || !ctype_digit((string) $value)) {
            return false;
        }

        $upload = Upload::find($value);

        return $upload && File::exists(public_path($upload->file_name));
    }
}
