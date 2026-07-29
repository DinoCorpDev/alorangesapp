<?php

namespace App\Jobs;

use App\Http\Services\ProductCatalogPdfRenderer;
use App\Http\Services\ProductCatalogStore;
use App\Jobs\Concerns\RunsDetachedArtisanCommand;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Builds a catalog PDF outside the request that asked for it.
 *
 * A large catalog takes minutes and hundreds of megabytes (measured: ~190s and 128 MB for
 * 3.000 products), which no request-response cycle survives on a normal PHP configuration.
 * The catalog record carries the progress so the listing can show it, and a failure is
 * recorded on the record instead of surfacing as a 500 that loses the whole selection.
 */
class GenerateProductCatalogJob
{
    use RunsDetachedArtisanCommand;

    /**
     * Progress lives in the cache, not in catalogs.json: it changes many times per run and
     * is worthless once the run ends, so it should not mean rewriting (and locking) the
     * catalog index over and over. The file cache driver is shared between the web process
     * and the detached worker.
     */
    private const PROGRESS_TTL_HOURS = 6;

    /** Minimum seconds between progress writes, so a small catalog is not chatty. */
    private const PROGRESS_THROTTLE_SECONDS = 1.0;

    protected ProductCatalogStore $store;

    protected ProductCatalogPdfRenderer $renderer;

    private ?float $lastProgressAt = null;

    public function __construct(?ProductCatalogStore $store = null, ?ProductCatalogPdfRenderer $renderer = null)
    {
        $this->store = $store ?: new ProductCatalogStore;
        $this->renderer = $renderer ?: new ProductCatalogPdfRenderer;
    }

    /**
     * Queues generation for a catalog that is already stored, and returns its new status.
     */
    public static function trigger(string $catalogId): string
    {
        $store = new ProductCatalogStore;
        $store->update($catalogId, [
            'status' => ProductCatalogStore::STATUS_QUEUED,
            'status_message' => null,
        ]);

        Cache::forget(self::progressKey($catalogId));

        self::spawnDetachedArtisan('catalogs:generate ' . escapeshellarg($catalogId));

        return ProductCatalogStore::STATUS_QUEUED;
    }

    public static function progressKey(string $catalogId): string
    {
        return 'catalog_pdf_progress_' . $catalogId;
    }

    /**
     * Last reported progress for a catalog, or null when nothing is running.
     */
    public static function progress(string $catalogId): ?array
    {
        $progress = Cache::get(self::progressKey($catalogId));

        return is_array($progress) ? $progress : null;
    }

    public function handle(string $catalogId): void
    {
        // The renderer holds every page in memory until Output(), and a big catalog runs for
        // minutes. Both limits are lifted here rather than in php.ini so the setting travels
        // with the code and applies even when the job runs inline (sync queue).
        @set_time_limit(0);
        @ini_set('memory_limit', '-1');

        $catalog = $this->store->find($catalogId);

        if (! $catalog) {
            Log::warning('Catalog PDF generation skipped: catalog not found', ['catalog_id' => $catalogId]);
            return;
        }

        $startedAt = microtime(true);

        $this->store->update($catalogId, [
            'status' => ProductCatalogStore::STATUS_PROCESSING,
            'status_message' => null,
        ]);

        try {
            $filePath = $this->renderer->render(
                $catalog['name'],
                $catalog['settings'] ?? [],
                $catalog['category_ids'] ?? [],
                $catalog['product_ids'] ?? [],
                function (array $progress) use ($catalogId) {
                    $this->reportProgress($catalogId, $progress);
                },
            );

            // Only now is the previous file replaceable: keeping it until the new one exists
            // means a failed regeneration still leaves a downloadable catalog behind.
            $this->deletePreviousFile($catalog, $filePath);

            $this->store->update($catalogId, [
                'status' => ProductCatalogStore::STATUS_READY,
                'status_message' => null,
                'file_path' => $filePath,
                'generated_at' => now()->format('Y-m-d H:i:s'),
            ]);

            Log::info('Catalog PDF generated', [
                'catalog_id' => $catalogId,
                'products' => count($catalog['product_ids'] ?? []),
                'seconds' => round(microtime(true) - $startedAt, 1),
                'peak_memory_mb' => round(memory_get_peak_usage(true) / 1048576),
                'file_path' => $filePath,
            ]);
        } catch (Throwable $e) {
            Log::error('Catalog PDF generation failed', [
                'catalog_id' => $catalogId,
                'products' => count($catalog['product_ids'] ?? []),
                'seconds' => round(microtime(true) - $startedAt, 1),
                'peak_memory_mb' => round(memory_get_peak_usage(true) / 1048576),
                'exception' => $e,
            ]);

            $this->store->update($catalogId, [
                'status' => ProductCatalogStore::STATUS_FAILED,
                'status_message' => $this->readableError($e),
            ]);
        } finally {
            // The status on the record is the durable answer from here on; a stale progress
            // entry would otherwise keep the bar on screen after the run ended.
            Cache::forget(self::progressKey($catalogId));
        }
    }

    /**
     * Publishes progress for the listing to poll, skipping updates that arrive too close
     * together. A phase change always gets through, so the last step is never swallowed.
     */
    protected function reportProgress(string $catalogId, array $progress): void
    {
        $now = microtime(true);
        $isPageUpdate = ($progress['phase'] ?? null) === ProductCatalogPdfRenderer::PHASE_PAGINATING
            && ($progress['done_pages'] ?? 0) > 0;

        if ($isPageUpdate && $this->lastProgressAt !== null && ($now - $this->lastProgressAt) < self::PROGRESS_THROTTLE_SECONDS) {
            return;
        }

        $this->lastProgressAt = $now;

        Cache::put(
            self::progressKey($catalogId),
            array_merge($progress, ['updated_at' => now()->toIso8601String()]),
            now()->addHours(self::PROGRESS_TTL_HOURS),
        );
    }

    protected function deletePreviousFile(array $catalog, string $newPath): void
    {
        $previous = $catalog['file_path'] ?? '';

        if (! $previous || $previous === $newPath) {
            return;
        }

        $absolute = public_path($previous);

        if (is_file($absolute)) {
            @unlink($absolute);
        }
    }

    protected function readableError(Throwable $e): string
    {
        $message = trim($e->getMessage());

        if ($e instanceof \Mpdf\MpdfException) {
            $message = 'mPDF: ' . $message;
        }

        return \Illuminate\Support\Str::limit($message ?: get_class($e), 400);
    }
}
