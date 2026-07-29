<?php

namespace App\Http\Services;

use App\Models\Category;
use App\Models\Product;
use App\Models\Upload;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Mpdf\HTMLParserMode;
use Mpdf\Mpdf;
use Mpdf\Output\Destination;

/**
 * Renders a product catalog to PDF with mPDF.
 *
 * mPDF refuses any HTML string longer than pcre.backtrack_limit (1.000.000 bytes by
 * default) — see Mpdf::AdjustHTML(). The catalog view produces about 1,8 KB of HTML per
 * product, so a single WriteHTML() call used to die above ~545 products. Everything here
 * is built so the product count is not a limit: the view is split on page boundaries and
 * fed to mPDF in bounded fragments, and the data it needs is loaded in a fixed number of
 * queries instead of one per product.
 */
class ProductCatalogPdfRenderer
{
    /**
     * Emitted by the PDF view at the start of every page. The rendered HTML is split on
     * this marker so every WriteHTML() fragment begins on a page boundary and never cuts
     * a table in half.
     */
    public const PAGE_MARKER = '<!--CATALOG-PAGE-->';

    /** Target size of each WriteHTML() fragment. A page boundary is never split. */
    private const CHUNK_BYTES = 400000;

    /**
     * Safety net for the fragments themselves: a single page holding an unusually long
     * name or description can exceed CHUNK_BYTES on its own, and mPDF also runs regexes
     * over the fragment internally. Raised only while rendering, then restored.
     */
    private const BACKTRACK_LIMIT = 50000000;

    /** Page size in millimetres (US letter). */
    private const PAGE_FORMAT = [216, 279];

    /** Progress phases reported through the render() callback. */
    public const PHASE_LOADING = 'loading';

    public const PHASE_RENDERING = 'rendering';

    public const PHASE_PAGINATING = 'paginating';

    public const PHASE_WRITING = 'writing';

    public function letterPalette(): array
    {
        return [
            'A' => '#f36f21', 'B' => '#00a86b', 'C' => '#0f75bc', 'D' => '#ec1c24',
            'E' => '#8dc63f', 'F' => '#662d91', 'G' => '#f7941d', 'H' => '#00a99d',
            'I' => '#2e3192', 'J' => '#ed145b', 'K' => '#39b54a', 'L' => '#f15a24',
            'M' => '#0072bc', 'N' => '#92278f', 'O' => '#d4145a', 'P' => '#009245',
            'Q' => '#fbb03b', 'R' => '#1b75bb', 'S' => '#c1272d', 'T' => '#006837',
            'U' => '#9e005d', 'V' => '#29abe2', 'W' => '#f7931e', 'X' => '#7ac943',
            'Y' => '#3fa9f5', 'Z' => '#ff5a5f', '#' => '#4d4d4d',
        ];
    }

    /**
     * Builds the PDF and returns its path relative to public/.
     *
     * $onProgress, when given, is called with a phase name and — once mPDF starts laying
     * pages out — how many of the expected pages are done, so a caller can show progress
     * for a run that takes minutes.
     */
    public function render(string $catalogName, array $settings, array $categoryIds, array $productIds, ?callable $onProgress = null): string
    {
        $report = $this->progressReporter($onProgress);

        $report(['phase' => self::PHASE_LOADING]);

        $categories = $this->categories($categoryIds);
        $productsByCategory = $this->productsByCategory($categories, $productIds);

        $directory = public_path('uploads/catalogs');

        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $slug = Str::slug($catalogName) ?: 'catalogo';
        $relativePath = 'uploads/catalogs/' . $slug . '-' . now()->format('YmdHis') . '.pdf';

        $report(['phase' => self::PHASE_RENDERING]);

        $html = view('backend.product.catalogs.pdf_mpdf', [
            'catalogName' => $catalogName,
            'productsByCategory' => $productsByCategory,
            'settings' => $settings,
            'letterPalette' => $this->letterPalette(),
            'uploadMap' => $this->uploadMap($productsByCategory, $settings),
            'fallbackImage' => uploaded_asset(get_setting('header_logo')) ?: static_asset('assets/img/logo.png'),
        ])->render();

        // The view holds every product; free it before mPDF starts allocating pages.
        unset($productsByCategory, $categories);

        $this->writeWithMpdf($catalogName, $html, public_path($relativePath), $report);

        return $relativePath;
    }

    protected function progressReporter(?callable $onProgress): callable
    {
        if (! $onProgress) {
            return function () {};
        }

        return function (array $progress) use ($onProgress) {
            $onProgress($progress);
        };
    }

    public function categories(array $categoryIds): Collection
    {
        return Category::whereIn('id', $categoryIds)
            ->orderBy('order_level')
            ->orderBy('name')
            ->get();
    }

    /**
     * Products of each category, grouped by their initial letter.
     *
     * product_translations is eager loaded because getTranslation() reads that relation:
     * without it every product name costs a query, and the name is read several times per
     * product (sorting, grouping and the card itself).
     */
    public function productsByCategory(Collection $categories, array $productIds): Collection
    {
        return $categories->map(function ($category) use ($productIds) {
            // whereIntegerInRaw instead of whereIn: a selection of thousands of products
            // would otherwise bind one placeholder per id and hit PDO's placeholder ceiling.
            $products = Product::with('product_translations')
                ->whereIntegerInRaw('id', $productIds)
                ->where('lowest_price', '>', 0)
                ->whereHas('categories', function ($query) use ($category) {
                    $query->where('categories.id', $category->id);
                })->get()->sortBy(function ($product) {
                    return Str::lower($product->getTranslation('name'));
                })->values();

            return [
                'category' => $category,
                'letter_groups' => $products->groupBy(function ($product) {
                    $letter = Str::upper(Str::substr(trim($product->getTranslation('name')), 0, 1));
                    return preg_match('/[A-Z0-9]/', $letter) ? $letter : '#';
                })->sortKeys(),
            ];
        })->filter(function ($group) {
            return $group['letter_groups']->isNotEmpty();
        })->values();
    }

    /**
     * Every Upload referenced by the catalog, resolved in one query.
     *
     * The view turns image values into file:/// paths and most of them are upload ids;
     * looking each one up on its own meant two queries per product card.
     */
    protected function uploadMap(Collection $productsByCategory, array $settings): array
    {
        $ids = [];

        foreach ($productsByCategory as $categoryGroup) {
            foreach ($categoryGroup['letter_groups'] as $letterProducts) {
                foreach ($letterProducts as $product) {
                    $ids[] = $product->thumbnail_img;
                    $ids[] = $product->meta_image;
                }
            }
        }

        // Page images (cover, payment, info, advertising, ...) are ids too.
        array_walk_recursive($settings, function ($value) use (&$ids) {
            $ids[] = $value;
        });

        $ids = array_values(array_unique(array_filter($ids, function ($value) {
            return is_scalar($value) && ctype_digit((string) $value);
        })));

        if (empty($ids)) {
            return [];
        }

        return Upload::whereIntegerInRaw('id', $ids)->pluck('file_name', 'id')->all();
    }

    /**
     * Feeds the rendered HTML to mPDF in fragments that always start on a page boundary.
     */
    protected function writeWithMpdf(string $catalogName, string $html, string $absolutePath, ?callable $report = null): void
    {
        $report = $report ?: function () {};

        $tempDir = storage_path('app/product_catalogs/mpdf_temp');

        if (! is_dir($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        $previousBacktrackLimit = ini_get('pcre.backtrack_limit');
        ini_set('pcre.backtrack_limit', (string) self::BACKTRACK_LIMIT);

        try {
            $mpdf = new Mpdf([
                'mode'          => 'utf-8',
                'format'        => self::PAGE_FORMAT,
                'margin_left'   => 0,
                'margin_right'  => 0,
                'margin_top'    => 0,
                'margin_bottom' => 0,
                'margin_header' => 0,
                'margin_footer' => 0,
                'tempDir'       => $tempDir,
            ]);

            $mpdf->SetTitle($catalogName ?: 'Catalog');
            $mpdf->SetDisplayMode('fullpage');
            $mpdf->shrink_tables_to_fit = 0;

            $document = $this->splitDocument($html);
            unset($html);

            if ($document['css'] !== '') {
                $mpdf->WriteHTML($document['css'], HTMLParserMode::HEADER_CSS);
            }

            $totalPages = $document['pages'];
            $report(['phase' => self::PHASE_PAGINATING, 'done_pages' => 0, 'total_pages' => $totalPages]);

            foreach ($document['chunks'] as $chunk) {
                $mpdf->WriteHTML($chunk, HTMLParserMode::HTML_BODY);

                // $mpdf->page is how many pages actually exist so far, which can exceed the
                // planned count when a page overflows; the label caps it when reporting.
                $report([
                    'phase' => self::PHASE_PAGINATING,
                    'done_pages' => (int) $mpdf->page,
                    'total_pages' => $totalPages,
                ]);
            }

            unset($document);

            $report([
                'phase' => self::PHASE_WRITING,
                'done_pages' => (int) $mpdf->page,
                'total_pages' => max($totalPages, (int) $mpdf->page),
            ]);

            $this->removeBlankPages($mpdf);
            $mpdf->Output($absolutePath, Destination::FILE);
        } finally {
            ini_set('pcre.backtrack_limit', (string) $previousBacktrackLimit);
        }
    }

    /**
     * Splits the document into its stylesheet plus body fragments of at most CHUNK_BYTES.
     *
     * Only string functions are used: a lazy regex over a multi-megabyte document would
     * itself blow past pcre.backtrack_limit, which is the very failure being fixed here.
     */
    protected function splitDocument(string $html): array
    {
        $body = $this->extractBody($html);

        return [
            'css' => $this->extractCss($html),
            // One marker per page, so counting them gives the page total up front — which is
            // what turns progress into "page X of Y" instead of a spinner.
            'pages' => substr_count($body, self::PAGE_MARKER),
            'chunks' => $this->chunkBody($body),
        ];
    }

    protected function extractCss(string $html): string
    {
        $open = stripos($html, '<style');

        if ($open === false) {
            return '';
        }

        $openEnd = strpos($html, '>', $open);
        $close = stripos($html, '</style>', $openEnd === false ? $open : $openEnd);

        if ($openEnd === false || $close === false) {
            return '';
        }

        // HEADER_CSS mode wraps the value in <style> itself, so hand it the bare rules.
        return trim(substr($html, $openEnd + 1, $close - $openEnd - 1));
    }

    protected function extractBody(string $html): string
    {
        $open = stripos($html, '<body');

        if ($open === false) {
            return $html;
        }

        $openEnd = strpos($html, '>', $open);

        if ($openEnd === false) {
            return $html;
        }

        $close = strripos($html, '</body>');
        $start = $openEnd + 1;

        return $close === false || $close < $start
            ? substr($html, $start)
            : substr($html, $start, $close - $start);
    }

    /**
     * @return string[]
     */
    protected function chunkBody(string $body): array
    {
        $segments = explode(self::PAGE_MARKER, $body);
        unset($body);

        $chunks = [];
        $current = '';

        foreach ($segments as $segment) {
            if ($segment === '') {
                continue;
            }

            // A page never gets split, so a single oversized page becomes its own chunk.
            if ($current !== '' && strlen($current) + strlen($segment) > self::CHUNK_BYTES) {
                $chunks[] = $current;
                $current = '';
            }

            $current .= $segment;
        }

        if (trim($current) !== '') {
            $chunks[] = $current;
        }

        return $chunks;
    }

    protected function removeBlankPages(Mpdf $mpdf): void
    {
        $visiblePages = [];

        foreach ($mpdf->pages as $content) {
            if (strlen(trim((string) $content)) <= 220) {
                continue;
            }

            $visiblePages[] = $content;
        }

        if (count($visiblePages) === count($mpdf->pages) || empty($visiblePages)) {
            return;
        }

        $mpdf->pages = [];

        foreach ($visiblePages as $index => $content) {
            $mpdf->pages[$index + 1] = $content;
        }

        $mpdf->page = count($visiblePages);
    }
}
