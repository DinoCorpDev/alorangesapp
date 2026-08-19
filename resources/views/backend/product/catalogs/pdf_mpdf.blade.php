@php
    $settings = array_merge([
        'show_prices'                    => true,
        'show_payment_page'              => true,
        'show_info_page'                 => true,
        'show_page_four'                 => false,
        'description_limit'              => 90,
        'products_per_page'              => 12,
        'cover_image'                    => null,
        'cover_title_position'           => 'middle',
        'advisor_name'                   => '',
        'advisor_phone'                  => '',
        'advisor_email_1'                => '',
        'advisor_email_2'                => '',
        'advertising_items'              => [],
        'payment_page_image'             => null,
        'payment_bank_icon'              => null,
        'payment_debit_icon'             => null,
        'payment_credit_icon'            => null,
        'payment_cash_icon'              => null,
        'info_page_image'                => null,
        'page_four_image'                => null,
        'final_page_image'               => null,
        'final_page_blank'               => false,
        'payment_page_position'          => 'start',
        'info_page_position'             => 'start',
        'extra_pages_position'           => 'start',
        'additional_pages'               => [],
        'payment_title'                  => 'MEDIOS DE PAGO',
        'payment_delivery_title'         => 'EFECTIVO CONTRA ENTREGA, DEPOSITO O TRANSFERENCIA DIRECTA',
        'payment_bank_info'              => '',
        'payment_debit_title'            => 'TARJETAS DEBITO',
        'payment_debit_info'             => '',
        'payment_credit_title'           => 'TARJETAS CREDITO',
        'payment_credit_info'            => '',
        'payment_cash_title'             => 'PAGUE EN EFECTIVO EN MAS DE 14.000 PUNTOS',
        'payment_cash_info'              => '',
        'info_page_title'                => 'INFORMACION',
        'info_page_content'              => '',
        'info_table_rows'                => [],
        'product_title_font_family'      => 'DejaVu Sans',
        'product_title_font_size'        => 12,
        'product_description_font_family'=> 'DejaVu Sans',
        'product_description_font_size'  => 10,
        'product_price_font_family'      => 'DejaVu Sans',
        'product_price_font_size'        => 16,
        'product_reference_font_family'  => 'DejaVu Sans',
        'product_reference_font_size'    => 12,
        'standalone_letter_position'     => 'right',
        'product_box_colors'             => [],
        'product_text_colors'            => [],
    ], $settings ?? []);

    // id => file_name for every Upload referenced by this catalog, resolved in a single
    // query by ProductCatalogPdfRenderer. Looking each id up here instead meant two
    // queries per product card, which does not scale with the product count.
    $uploadMap = $uploadMap ?? [];

    // Returns a local file:/// URL that mPDF can read, or null if the file doesn't exist
    $localFileUrl = function ($path) {
        if (! $path || ! file_exists($path)) {
            return null;
        }
        return 'file:///' . str_replace('\\', '/', $path);
    };

    $localPublicAsset = function ($path) use ($localFileUrl) {
        return $localFileUrl(public_path(ltrim((string) $path, '/')));
    };

    // Resolves an image value (upload ID, public path, or URL) to a file:/// URL for mPDF
    $pageImage = function ($value) use ($localFileUrl, $uploadMap) {
        if (! $value) {
            return null;
        }
        $value = trim((string) $value);

        if (filter_var($value, FILTER_VALIDATE_URL)) {
            return $value;
        }

        if (strpos($value, 'public/') === 0) {
            $localPath = base_path($value);
            if (file_exists($localPath)) {
                return $localFileUrl($localPath);
            }
        }

        if (strpos($value, 'uploads/') === 0 || strpos($value, 'assets/') === 0) {
            $localPath = public_path($value);
            if (file_exists($localPath)) {
                return $localFileUrl($localPath);
            }
        }

        if (! ctype_digit($value)) {
            return null;
        }

        $fileName = $uploadMap[(int) $value] ?? null;

        if (! $fileName) {
            return null;
        }

        if (env('FILESYSTEM_DRIVER') !== 's3') {
            $localPath = public_path($fileName);
            if (file_exists($localPath)) {
                return $localFileUrl($localPath);
            }
        }

        return uploaded_asset($value);
    };

    // Product photos are shot with generous white/transparent padding around the item;
    // trimming that margin is what lets the bigger image box actually show a bigger
    // product instead of a bigger empty area. Only applies to local files — S3-backed
    // images are skipped rather than downloaded just to be cropped.
    $imageTrimmer = new \App\Http\Services\ProductImageTrimmer();
    $productImage = function ($value) use ($pageImage, $imageTrimmer) {
        $resolved = $pageImage($value);

        if (! $resolved || strpos($resolved, 'file:///') !== 0) {
            return $resolved;
        }

        $trimmedPath = $imageTrimmer->trim(urldecode(substr($resolved, 8)));

        return $trimmedPath ? ('file:///' . str_replace('\\', '/', $trimmedPath)) : $resolved;
    };

    $coverImage       = $pageImage($settings['cover_image']);
    $paymentImage     = $pageImage($settings['payment_page_image']);
    $paymentBankIcon  = $pageImage($settings['payment_bank_icon']);
    $paymentDebitIcon = $pageImage($settings['payment_debit_icon']);
    $paymentCreditIcon= $pageImage($settings['payment_credit_icon']);
    $paymentCashIcon  = $pageImage($settings['payment_cash_icon']);
    $infoImage        = $pageImage($settings['info_page_image']);
    $pageFourImage    = $pageImage($settings['page_four_image']);
    $extraPageImages  = collect($settings['extra_page_images'] ?? [])->map($pageImage)->filter()->values();
    $finalPageImage   = $pageImage($settings['final_page_image'] ?? null);

    if ($extraPageImages->isEmpty() && $pageFourImage) {
        $extraPageImages = collect([$pageFourImage]);
    }

    // Cada uno de estos 3 tipos de pagina se ubica al inicio o al final del catalogo, de forma independiente
    $paymentPagePosition = ($settings['payment_page_position'] ?? 'start') === 'end' ? 'end' : 'start';
    $infoPagePosition    = ($settings['info_page_position'] ?? 'start') === 'end' ? 'end' : 'start';
    $extraPagesPosition  = ($settings['extra_pages_position'] ?? 'start') === 'end' ? 'end' : 'start';

    // Paginas adicionales configurables: cada una elige su propia posicion (inicio / final)
    $additionalPagesByPosition = collect($settings['additional_pages'] ?? [])->map(function ($item) use ($pageImage) {
        $position = $item['position'] ?? 'start';

        return [
            'image' => $pageImage($item['image'] ?? null),
            'position' => in_array($position, ['start', 'end'], true) ? $position : 'start',
        ];
    })->filter(fn($item) => $item['image'])->groupBy('position');

    $fallbackImage = $localPublicAsset('assets/img/item-placeholder.png') ?: ($fallbackImage ?? null);

    $advertisingItems = $settings['advertising_items'] ?? [];
    if (empty($advertisingItems) && ! empty($settings['advertising_image']) && ($settings['advertising_position'] ?? '') === 'after_each_letter') {
        $advertisingItems = [['image' => $settings['advertising_image'], 'letter' => 'A']];
    }
    $advertisingByLetter = collect($advertisingItems)->map(function ($item) use ($pageImage) {
        return [
            'letter' => \Illuminate\Support\Str::upper($item['letter'] ?? ''),
            'image'  => $pageImage($item['image'] ?? null),
        ];
    })->filter(fn($i) => $i['letter'] && $i['image'])->groupBy('letter');
    $letterIntroAdsByKey = collect($settings['letter_intro_ads'] ?? [])->map(function ($item, $index) use ($pageImage) {
        $categoryId = (int) ($item['category_id'] ?? 0);
        $letter = \Illuminate\Support\Str::upper($item['letter'] ?? '');
        $order = (int) ($item['order'] ?? ($index + 1));

        return [
            'key' => $categoryId . '|' . $letter,
            'image' => $pageImage($item['image'] ?? null),
            'order' => in_array($order, [1, 2], true) ? $order : 1,
        ];
    })->filter(fn($i) => $i['key'] !== '0|' && $i['image'])
      ->groupBy('key')
      ->map(fn($items) => $items->sortBy('order')->take(2)->values());

    $productsPerPage = in_array((int) ($settings['products_per_page'] ?? 12), [12, 20], true)
        ? (int) $settings['products_per_page']
        : 12;
    $compactProducts = $productsPerPage === 20;
    $productColumns = $compactProducts ? 4 : 3;

    $diagnosticFillerByKey = collect($settings['diagnostic_filler_blocks'] ?? [])->map(function ($item, $index) use ($pageImage, $productColumns) {
        $categoryId = (int) ($item['category_id'] ?? 0);
        $letter = \Illuminate\Support\Str::upper($item['letter'] ?? '');
        return [
            'key' => $categoryId . '|' . $letter,
            'image' => $pageImage($item['image'] ?? null),
            'block_index' => (int) ($item['block_index'] ?? $index),
            'x' => max(0, min($productColumns - 1, (int) ($item['x'] ?? 0))),
            'y' => max(0, min(4, (int) ($item['y'] ?? 0))),
            'width' => max(1, min($productColumns, (int) ($item['width'] ?? 1))),
            'height' => max(1, min(5, (int) ($item['height'] ?? 1))),
        ];
    })->filter(fn ($item) => $item['key'] !== '0|' && $item['image'])
      ->groupBy('key')
      ->map(fn ($items) => $items->sortBy('block_index')->values());

    $descriptionLimit = (int) ($settings['description_limit'] ?: 90);

    $productBoxColors = $settings['product_box_colors'] ?? [];
    if (count($productBoxColors) > 1 && count(array_unique($productBoxColors)) === 1 && reset($productBoxColors) === '#f36f21') {
        $productBoxColors = [];
    }

    // mPDF supports the same fonts; falls back to DejaVu Sans if not found
    $cssFont = fn($f) => str_replace("'", '', $f ?: 'DejaVu Sans');

    $infoRows = $settings['info_table_rows'] ?? [];
    if (empty($infoRows) && ! empty($settings['info_page_content'])) {
        $infoRows = collect(preg_split('/\r\n|\r|\n/', $settings['info_page_content']))
            ->filter()
            ->map(function ($line) {
                $parts = explode(':', $line, 2);
                return ['label' => trim($parts[0] ?? ''), 'value' => trim($parts[1] ?? $line)];
            })->values()->all();
    }

    $coverTitlePosition = in_array($settings['cover_title_position'] ?? 'middle', ['top', 'middle', 'bottom'], true)
        ? $settings['cover_title_position']
        : 'middle';

    $advisorEmails = collect([$settings['advisor_email_1'] ?? '', $settings['advisor_email_2'] ?? ''])->filter()->values();
    $hasAdvisor    = $settings['advisor_name'] || $settings['advisor_phone'] || $advisorEmails->isNotEmpty();

    // Cover title row vertical alignment based on position setting
    // mPDF does not respect vertical-align:middle on fixed-height cells — use padding-top instead
    $coverFooterHeight = $hasAdvisor ? 38 : 0;
    $coverTitleHeight = 52;
    $coverTitleTop = match ($coverTitlePosition) {
        'top'    => 34,
        'bottom' => ($hasAdvisor ? 166 : 198),
        default  => ($hasAdvisor ? 92 : 112),
    };
    $coverTitleBottomSpace = max(0, 276 - $coverTitleTop - $coverTitleHeight - $coverFooterHeight);

    $productTableColspan = ($productColumns * 2) + 1;
    $productGap = $compactProducts ? 3 : 5;
    $productCardWidth = $compactProducts ? 45 : 58;

    // La retícula debe sumar exactamente los 216 mm de la página. Antes los anchos
    // declarados sumaban menos y mPDF repartía el sobrante entre las columnas. Las
    // tarjetas quedaban pegadas al lado izquierdo de una celda ensanchada, mientras
    // las gráficas se centraban: por eso ambos bloques parecían corridos.
    $productGridWidth = ($productCardWidth * $productColumns) + ($productGap * ($productColumns - 1));
    $productSideSpace = (216 - $productGridWidth) / 2;
    $productCardHeight = $compactProducts ? 45 : 54;
    $productHeadHeight = $compactProducts ? 8 : 10;
    $productMediaHeight = $compactProducts ? 27 : 32;
    $productInfoHeight = $productCardHeight - $productHeadHeight - $productMediaHeight;
    $productImageWidth = $compactProducts ? 42 : 54;
    $productImageHeight = $compactProducts ? 24 : 28;
    $productRowHeight = $compactProducts ? 48 : 62;
    $productCellPaddingTop = $compactProducts ? 1.5 : 4;
    $productCellPaddingBottom = $compactProducts ? 1 : 3;
    $showAlphabeticNavigator = (bool) ($settings['show_alphabetic_navigator'] ?? true);
    $standaloneLetterPosition = in_array(($settings['standalone_letter_position'] ?? 'right'), ['left', 'right', 'alternate_outer'], true)
        ? $settings['standalone_letter_position']
        : 'right';
    $catalogAlphabet = ['A','B','C','D','E','F','G','H','I','J','K','L','M','N','Ñ','O','P','Q','R','S','T','U','V','W','X','Y','Z'];
    $availableCatalogLetters = $productsByCategory
        ->flatMap(function ($categoryGroup) {
            return collect($categoryGroup['letter_groups'])->keys();
        })
        ->filter(function ($letter) use ($catalogAlphabet) {
            return in_array($letter, $catalogAlphabet, true);
        })
        ->unique()
        ->values()
        ->all();
    $catalogLetterAnchor = function ($letter) {
        return 'catalog-letter-' . ($letter === 'Ñ' ? 'N-TILDE' : $letter);
    };
    $anchoredCatalogLetters = [];
    $productHeaderHeight = $showAlphabeticNavigator ? 18 : 22;
    $productBannerLimit = $compactProducts ? 34 : 42;
    $advertisingWidth = ($productCardWidth * 2) + $productGap;
    $advertisingHeight = ($productRowHeight * 2) - $productCellPaddingTop - $productCellPaddingBottom;

    $safeTitleFontSize = $compactProducts
        ? min(8, max(6, (int) ($settings['product_title_font_size'] ?: 8)))
        : min(10, max(7, (int) ($settings['product_title_font_size'] ?: 10)));
    $safeDescriptionFontSize = min(9, max(7, (int) ($settings['product_description_font_size'] ?: 8)));
    $safePriceFontSize = $compactProducts
        ? min(11, max(8, (int) ($settings['product_price_font_size'] ?: 10)))
        : min(13, max(9, (int) ($settings['product_price_font_size'] ?: 13)));
    $safeReferenceFontSize = $compactProducts
        ? min(8, max(6, (int) ($settings['product_reference_font_size'] ?: 7)))
        : min(9, max(7, (int) ($settings['product_reference_font_size'] ?: 9)));

    $pdfPageRendered = false;
    $currentPdfPageNumber = 0;
    // $showPageNumber = true shows the page number (used only on product pages);
    // every other page explicitly turns it off so numbering never leaks onto other pages.
    // Every page also opens with a marker so the renderer can split this document into
    // fragments small enough for mPDF's pcre.backtrack_limit guard, always cutting between
    // pages and never inside a table.
    $marker = \App\Http\Services\ProductCatalogPdfRenderer::PAGE_MARKER;
    $pageBreak = function ($showPageNumber = false) use (&$pdfPageRendered, &$currentPdfPageNumber, $marker) {
        $currentPdfPageNumber++;
        $margins = $showPageNumber ? ' margin-bottom="3" margin-footer="3"' : ' margin-bottom="0" margin-footer="0"';
        $toggle = '<sethtmlpagefooter name="pageFooter" page="ALL" value="' . ($showPageNumber ? 'ON' : 'OFF') . '" />';

        if (! $pdfPageRendered) {
            $pdfPageRendered = true;
            return $marker . $toggle;
        }

        return $marker . '<pagebreak' . $margins . ' />' . $toggle;
    };
@endphp
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        /* Base — no flexbox, no grid, no object-fit, no transforms: all unsupported in mPDF */
        * { box-sizing: border-box; }
        body { margin: 0; padding: 0; font-family: DejaVu Sans, sans-serif; color: #2f3138; font-size: 11px; }

        .pdf-page { width: 216mm; overflow: hidden; }

        /* ─── COVER ─────────────────────────────── */
        .cover-bg     { background: #f4f5f7; }
        .cover-title  { font-size: 34px; font-weight: 700; color: #f36f21; line-height: 1.15; margin: 0; text-align: center; }
        .cover-meta   { font-size: 14px; color: #555; margin: 8mm 0 0; text-align: center; }
        .cover-advisor { text-align: center; color: #007a3d; font-size: 20px; font-weight: 700; line-height: 1.25; }
        .cover-advisor-name { display: block; font-size: 22px; text-transform: uppercase; margin-bottom: 2mm; }
        .cover-advisor-contact { display: block; font-size: 17px; }

        /* ─── PAYMENT ────────────────────────────── */
        .payment-title  { color: #ff5a00; font-size: 29px; font-weight: 700; line-height: 1; margin-bottom: 9mm; }
        .payment-pill   { background-color: #27c83a; color: #fff; border-radius: 20px; padding: 3mm 5mm; text-align: center; font-weight: 700; font-size: 12px; margin-bottom: 4mm; }
        .payment-box    { border: 1.5mm solid #008847; padding: 4mm 5mm; margin-bottom: 7mm; font-size: 10px; line-height: 1.35; }
        .payment-icon   { max-width: 28mm; max-height: 18mm; }
        .payment-column-title { background-color: #27c83a; color: #fff; padding: 2mm; text-align: center; font-weight: 700; font-size: 11px; }
        .payment-column-body  { padding: 5mm; font-size: 10px; line-height: 1.35; text-align: center; }
        .payment-column-icon  { max-width: 28mm; max-height: 16mm; margin-bottom: 3mm; }

        /* ─── INFO ───────────────────────────────── */
        .info-title { color: #ff5a00; font-size: 26px; font-weight: 700; margin-bottom: 8mm; }
        .info-table { width: 100%; border-collapse: collapse; border: 1.2mm solid #008847; font-size: 11px; }
        .info-table td { border-bottom: 0.3mm solid #a7d9b8; padding: 4mm; vertical-align: top; }
        .info-table tr:last-child td { border-bottom: 0; }
        .info-table-label { width: 34%; background-color: #f0fff3; color: #008847; font-weight: 700; }

        /* Executive report refresh - mPDF safe overrides */
        body { color: #1f2937; font-size: 10px; background: #ffffff; }
        .muted { color: #64748b; }
        .report-page { width: 216mm; height: 276mm; background: #f5f7fb; overflow: hidden; }
        .report-hero { height: 48mm; background: #e9eef5; overflow: hidden; }
        .report-hero img { display: block; width: 216mm; height: 48mm; }
        .report-shell { margin: 0 14mm; padding-top: 10mm; }
        .report-eyebrow { color: #64748b; font-size: 8px; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; margin: 0 0 2mm; }
        .report-title { color: #111827; font-size: 25px; font-weight: 700; line-height: 1.1; margin: 0; }
        .report-accent { width: 24mm; height: 1.2mm; background: #f36f21; margin: 3mm 0 7mm; }
        .report-card { background: #ffffff; border: 0.35mm solid #d8e0ea; padding: 4mm; }
        .report-card-title { color: #0f766e; font-size: 10px; font-weight: 700; text-transform: uppercase; margin-bottom: 2mm; }
        .report-card-body { color: #334155; font-size: 9.5px; line-height: 1.45; }
        .full-page-bg { display: block; width: 216mm; height: 276mm; }
        .cover-page { width: 216mm; height: 276mm; overflow: hidden; background: #f4f5f7; }
        .report-overlay { margin-left: 14mm; width: 188mm; }
        .payment-icon { max-width: 24mm; max-height: 14mm; }
        .payment-method-table { width: 100%; border-collapse: separate; border-spacing: 4mm 0; margin-top: 5mm; }
        .payment-method-card { background: #ffffff; border: 0.35mm solid #d8e0ea; padding: 4mm; height: 34mm; vertical-align: top; }
        .payment-method-title { color: #0f172a; font-size: 10px; font-weight: 700; text-transform: uppercase; border-bottom: 0.35mm solid #e5e7eb; padding-bottom: 2mm; margin-bottom: 3mm; }
        .payment-cash-card { margin-top: 5mm; background: #ffffff; border: 0.35mm solid #d8e0ea; padding: 4mm; min-height: 24mm; }
        .info-table { width: 100%; border-collapse: collapse; background: #ffffff; border: 0.35mm solid #d8e0ea; }
        .info-table td { border-bottom: 0.25mm solid #e5e7eb; padding: 4mm; vertical-align: top; font-size: 10px; line-height: 1.45; }
        .info-table tr:last-child td { border-bottom: 0; }
        .info-table-label { width: 34%; color: #0f766e; font-weight: 700; background: #f0fdfa; text-transform: uppercase; }
        .product-sheet { width: 216mm; height: 276mm; margin: 0; border-collapse: collapse; table-layout: fixed; background: #f5f7fb; }
        .product-sheet-header { height: {{ $productHeaderHeight }}mm; padding: 0; vertical-align: middle; background: #f7f9f7; border-bottom: 0.35mm solid #d3d9d4; }
        .product-category { color: #64748b; font-size: 8px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; }
        .product-letter { color: #538442; font-size: 29px; font-weight: 700; line-height: 1; text-align: center; }
        .alphabet-nav-table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        .alphabet-nav-cell { height: 12mm; padding: 3.0mm 0.1mm 0.8mm; text-align: center; vertical-align: middle; border-bottom: 0.25mm solid #d3d9d4; }
        .alphabet-nav-link, .alphabet-nav-disabled { display: block; width: 100%; font-size: 14px; font-weight: bold; line-height: 1; text-decoration: none; }
        .alphabet-nav-link { color: #538442; font-weight: bold; }
        .alphabet-nav-current { color: #f58634; font-weight: bold; }
        .alphabet-nav-disabled { color: #9a9a9a; font-weight: bold; }
        .alphabet-current-cell { width: 18mm; height: 12mm; padding: 0.7mm 2mm 0 2mm; text-align: center; vertical-align: middle; }
        .product-row { height: {{ $productRowHeight }}mm; }
        .product-cell { width: {{ $productCardWidth }}mm; padding-left: 0; padding-right: 0; text-align: left; vertical-align: top; }
        .product-card { width: {{ $productCardWidth }}mm; height: {{ $productCardHeight }}mm; border-collapse: collapse; table-layout: fixed; background: #ffffff; border: 0.3mm solid #dfe5e8; }
        .product-head { width: {{ $productCardWidth }}mm; height: {{ $productHeadHeight }}mm; padding: {{ $compactProducts ? 1.2 : 1.8 }}mm {{ $compactProducts ? 1.6 : 2.2 }}mm; font-weight: 700; line-height: 1.13; vertical-align: middle; text-transform: uppercase; }
        .product-media { width: {{ $productCardWidth }}mm; height: {{ $productMediaHeight }}mm; padding: {{ $compactProducts ? 1 : 1.5 }}mm; text-align: center; vertical-align: middle; background: #ffffff; }
        .product-img { max-width: {{ $productImageWidth }}mm; max-height: {{ $productImageHeight }}mm; }
        .product-info { width: {{ $productCardWidth }}mm; height: {{ $productInfoHeight }}mm; padding: 0 {{ $compactProducts ? 1 : 1.5 }}mm {{ $compactProducts ? 0.6 : 1 }}mm; text-align: center; vertical-align: bottom; background: #ffffff; }
        .product-name { color: #111827; font-weight: 700; line-height: 1.22; margin: 0; }
        .product-price { color: #d9832e; font-weight: 500; line-height: 1.1; margin: 0; text-align: center; }
        .product-ref { color: #475569; font-weight: 400; line-height: 1.1; margin: 0; text-align: center; }
        .product-detail-table { width: 100%; border-collapse: collapse; background: #ffffff; }
        .product-detail-table td { padding: 0; vertical-align: bottom; background: #ffffff; }
        .product-price-gap { height: {{ $compactProducts ? 0.8 : 1.2 }}mm; line-height: {{ $compactProducts ? 0.8 : 1.2 }}mm; font-size: 1px; }
        .product-advertising-cell { width: {{ $advertisingWidth }}mm; height: {{ $advertisingHeight }}mm; padding: {{ $productCellPaddingTop }}mm 0 {{ $productCellPaddingBottom }}mm; text-align: center; vertical-align: middle; }
        .product-advertising-frame { width: {{ $advertisingWidth }}mm; height: {{ $advertisingHeight - $productCellPaddingTop - $productCellPaddingBottom }}mm; border: 0.3mm solid #dfe5e8; background: #ffffff; text-align: center; vertical-align: middle; overflow: hidden; }
        .product-advertising-image { max-width: {{ $advertisingWidth - 1 }}mm; max-height: {{ $advertisingHeight - $productCellPaddingTop - $productCellPaddingBottom - 1 }}mm; }
        .product-desc { color: #64748b; line-height: 1.35; margin: 0; }
    </style>
</head>
<body>

<htmlpagefooter name="pageFooter">
    <div style="text-align:right; font-size:12px; line-height:1; font-weight:700; color:#64748b; padding-right:{{ $compactProducts ? 8 : 10 }}mm;">{PAGENO}</div>
</htmlpagefooter>

{{-- ═══════════════════════════════════════════════════════ --}}
{{-- PORTADA                                                  --}}
{{-- ═══════════════════════════════════════════════════════ --}}
@if ($coverImage)
    {!! $pageBreak() !!}
    <div class="cover-page">
        <img class="full-page-bg" src="{{ $coverImage }}" alt="">
    </div>
@else
    {!! $pageBreak() !!}
    <table class="pdf-page cover-bg" style="width:216mm; height:276mm; border-collapse:collapse;">
        <tr><td style="height:{{ $coverTitleTop }}mm;">&nbsp;</td></tr>
        <tr>
            <td style="text-align:center; vertical-align:middle; padding-left:20mm; padding-right:20mm; height:{{ $coverTitleHeight }}mm;">
                <div class="cover-title">{{ $catalogName }}</div>
            </td>
        </tr>
        <tr><td style="height:{{ $coverTitleBottomSpace }}mm;">&nbsp;</td></tr>
        @if ($hasAdvisor)
            <tr>
                <td style="text-align:center; vertical-align:bottom; padding:0 12mm 8mm; height:{{ $coverFooterHeight }}mm;">
                    <div class="cover-advisor">
                        @if ($settings['advisor_name'])
                            <div style="font-size:22px; text-transform:uppercase; margin-bottom:2mm; font-weight:700;">{{ $settings['advisor_name'] }}</div>
                        @endif
                        @if ($settings['advisor_phone'] || $advisorEmails->isNotEmpty())
                            <div style="font-size:17px;">
                                {{ $settings['advisor_phone'] }}
                                @if ($settings['advisor_phone'] && $advisorEmails->isNotEmpty()) | @endif
                                {{ $advisorEmails->join(' - ') }}
                            </div>
                        @endif
                    </div>
                </td>
            </tr>
        @endif
    </table>
@endif

{{-- ═══════════════════════════════════════════════════════ --}}
{{-- PÁGINA DE MEDIOS DE PAGO, INFORMACIÓN Y EXTRA - INICIO  --}}
{{-- ═══════════════════════════════════════════════════════ --}}
@if ($settings['show_payment_page'] && $paymentImage && $paymentPagePosition === 'start')
    {!! $pageBreak() !!}
    <div class="pdf-page">
        <img class="full-page-bg" src="{{ $paymentImage }}" alt="">
    </div>
@endif

@if ($settings['show_info_page'] && $infoImage && $infoPagePosition === 'start')
    {!! $pageBreak() !!}
    <div class="pdf-page">
        <img class="full-page-bg" src="{{ $infoImage }}" alt="">
    </div>
@endif

@if ($extraPagesPosition === 'start')
    @foreach ($extraPageImages as $extraPageImage)
        {!! $pageBreak() !!}
        <div class="pdf-page">
            <img class="full-page-bg" src="{{ $extraPageImage }}" alt="">
        </div>
    @endforeach
@endif

{{-- ═══════════════════════════════════════════════════════ --}}
{{-- PAGINAS ADICIONALES - DESPUES DE PAGO/INFORMACION/EXTRA --}}
{{-- ═══════════════════════════════════════════════════════ --}}
@foreach ($additionalPagesByPosition->get('start', collect()) as $additionalPage)
    {!! $pageBreak() !!}
    <div class="pdf-page">
        <img class="full-page-bg" src="{{ $additionalPage['image'] }}" alt="">
    </div>
@endforeach

{{-- Product pages --}}
@foreach ($productsByCategory as $executiveCategoryGroup)
    @php
        $executiveCategoryName = $executiveCategoryGroup['category']
            ? $executiveCategoryGroup['category']->getTranslation('name')
            : '';
        $executiveCategoryId = $executiveCategoryGroup['category']
            ? $executiveCategoryGroup['category']->id
            : null;
    @endphp

    @foreach ($executiveCategoryGroup['letter_groups'] as $letter => $letterProducts)
        @php
            $boxColor  = $productBoxColors[$letter] ?? $letterPalette[$letter] ?? '#f36f21';
            $textColor = $settings['product_text_colors'][$letter] ?? '#ffffff';
            $letterIntroAds = $letterIntroAdsByKey->get($executiveCategoryId . '|' . $letter, collect());
            $remainingLetterProducts = $letterProducts->values();
            $letterAdvertising = $advertisingByLetter->get($letter, collect())->values();
            $letterPages = collect();
            $advertisingIndex = 0;

            while ($remainingLetterProducts->isNotEmpty()) {
                $advertisingItem = $letterAdvertising->get($advertisingIndex);
                $pageCapacity = $productsPerPage - ($advertisingItem ? 4 : 0);
                $pageProducts = $remainingLetterProducts->take($pageCapacity)->values();
                $remainingLetterProducts = $remainingLetterProducts->slice($pageCapacity)->values();

                $letterPages->push([
                    'products' => $pageProducts,
                    'advertising' => $advertisingItem,
                ]);

                if ($advertisingItem) {
                    $advertisingIndex++;
                }
            }
        @endphp

        @foreach ($letterIntroAds as $letterIntroAd)
            {!! $pageBreak() !!}
            <div class="pdf-page">
                <img class="full-page-bg" src="{{ $letterIntroAd['image'] }}" alt="">
            </div>
        @endforeach

        @foreach ($letterPages as $letterPage)
            @php
                $chunk = $letterPage['products']->values();
                $advertisingItem = $letterPage['advertising'];
                $rowsTotal = (int) ($productsPerPage / $productColumns);
                $pageGrid = array_fill(0, $rowsTotal, array_fill(0, $productColumns, null));

                $placeGridItem = function ($x, $y, $width, $height, $type, $value) use (&$pageGrid, $productColumns, $rowsTotal) {
                    $x = (int) $x; $y = (int) $y; $width = (int) $width; $height = (int) $height;
                    if ($x < 0 || $y < 0 || $width < 1 || $height < 1 || $x + $width > $productColumns || $y + $height > $rowsTotal) return false;
                    for ($rr = $y; $rr < $y + $height; $rr++) {
                        for ($cc = $x; $cc < $x + $width; $cc++) {
                            if ($pageGrid[$rr][$cc] !== null) return false;
                        }
                    }
                    $anchor = ['x'=>$x,'y'=>$y,'width'=>$width,'height'=>$height,'type'=>$type,'value'=>$value];
                    for ($rr = $y; $rr < $y + $height; $rr++) {
                        for ($cc = $x; $cc < $x + $width; $cc++) $pageGrid[$rr][$cc] = $anchor;
                    }
                    return true;
                };

                if ($advertisingItem) {
                    $featuredColumns = max(0, $productColumns - 2);
                    $featuredCapacity = $featuredColumns * min(2, $rowsTotal);
                    $featuredProducts = $chunk->take($featuredCapacity)->values();
                    foreach ($featuredProducts as $index => $product) {
                        $placeGridItem($index % max(1, $featuredColumns), intdiv($index, max(1, $featuredColumns)), 1, 1, 'product', $product);
                    }
                    $placeGridItem(max(0, $productColumns - 2), 0, 2, min(2, $rowsTotal), 'advertising', $advertisingItem);
                    $regularProducts = $chunk->slice($featuredCapacity)->values();
                    foreach ($regularProducts as $index => $product) {
                        $placeGridItem($index % $productColumns, 2 + intdiv($index, $productColumns), 1, 1, 'product', $product);
                    }
                } else {
                    foreach ($chunk as $index => $product) {
                        $placeGridItem($index % $productColumns, intdiv($index, $productColumns), 1, 1, 'product', $product);
                    }
                }

                if ($loop->last) {
                    foreach ($diagnosticFillerByKey->get($executiveCategoryId . '|' . $letter, collect()) as $diagnosticFiller) {
                        $placeGridItem($diagnosticFiller['x'], $diagnosticFiller['y'], $diagnosticFiller['width'], $diagnosticFiller['height'], 'filler', $diagnosticFiller);
                    }
                }
            @endphp

            {!! $pageBreak(true) !!}
            @php
                $standaloneLetterAlign = $standaloneLetterPosition === 'alternate_outer'
                    ? (($currentPdfPageNumber % 2) === 1 ? 'right' : 'left')
                    : $standaloneLetterPosition;
            @endphp
            <table class="product-sheet">
                <colgroup>
                    <col style="width:{{ $productSideSpace }}mm;">
                    @for ($columnIndex = 0; $columnIndex < $productColumns; $columnIndex++)
                        <col style="width:{{ $productCardWidth }}mm;">
                        @if ($columnIndex < $productColumns - 1)
                            <col style="width:{{ $productGap }}mm;">
                        @endif
                    @endfor
                    <col style="width:{{ $productSideSpace }}mm;">
                </colgroup>
                <tr>
                    <td colspan="{{ $productTableColspan }}" class="product-sheet-header">
                        @php
                            $isFirstCatalogPageForLetter = ! in_array($letter, $anchoredCatalogLetters, true);
                            if ($isFirstCatalogPageForLetter) {
                                $anchoredCatalogLetters[] = $letter;
                            }
                        @endphp
                        @if ($isFirstCatalogPageForLetter && in_array($letter, $catalogAlphabet, true))
                            <a name="{{ $catalogLetterAnchor($letter) }}"></a>
                        @endif

                        @if ($showAlphabeticNavigator)
                            <table class="alphabet-nav-table">
                                <tr>
                                    <td style="width:6mm;">&nbsp;</td>
                                    @foreach ($catalogAlphabet as $navigatorLetter)
                                        <td class="alphabet-nav-cell">
                                            @if (in_array($navigatorLetter, $availableCatalogLetters, true))
                                                <a href="#{{ $catalogLetterAnchor($navigatorLetter) }}"
                                                   class="alphabet-nav-link{{ $navigatorLetter === $letter ? ' alphabet-nav-current' : '' }}"
                                                   style="color:{{ $navigatorLetter === $letter ? '#f58634' : '#538442' }};">{{ $navigatorLetter }}</a>
                                            @else
                                                <span class="alphabet-nav-disabled">{{ $navigatorLetter }}</span>
                                            @endif
                                        </td>
                                    @endforeach
                                    <td style="width:4mm;">&nbsp;</td>
                                    <td class="alphabet-current-cell">
                                        <div class="product-letter">{{ $letter }}</div>
                                    </td>
                                </tr>
                            </table>
                        @else
                            <table style="width:100%; border-collapse:collapse; table-layout:fixed;">
                                <tr>
                                    @if ($standaloneLetterAlign === 'left')
                                        <td style="width:50%; padding:5mm 0 3mm {{ $compactProducts ? 8 : 10 }}mm; text-align:left; vertical-align:middle;">
                                            <span class="product-letter" style="color:{{ $boxColor }};">{{ $letter }}</span>
                                        </td>
                                        <td style="width:50%;">&nbsp;</td>
                                    @else
                                        <td style="width:50%;">&nbsp;</td>
                                        <td style="width:50%; padding:5mm {{ $compactProducts ? 8 : 10 }}mm 3mm 0; text-align:right; vertical-align:middle;">
                                            <span class="product-letter" style="color:{{ $boxColor }};">{{ $letter }}</span>
                                        </td>
                                    @endif
                                </tr>
                            </table>
                        @endif
                    </td>
                </tr>

                @for ($gridRow = 0; $gridRow < $rowsTotal; $gridRow++)
                    <tr class="product-row">
                        <td style="width:{{ $productSideSpace }}mm;">&nbsp;</td>
                        @php $gridColumn = 0; @endphp
                        @while ($gridColumn < $productColumns)
                            @php
                                $gridItem = $pageGrid[$gridRow][$gridColumn] ?? null;
                            @endphp

                            @if ($gridItem && $gridItem['x'] === $gridColumn && $gridItem['y'] < $gridRow)
                                @php $gridColumn += $gridItem['width']; @endphp
                                @if ($gridColumn < $productColumns)<td style="width:{{ $productGap }}mm;">&nbsp;</td>@endif
                                @continue
                            @endif

                            @php
                                $itemWidth = $gridItem && $gridItem['x'] === $gridColumn && $gridItem['y'] === $gridRow ? $gridItem['width'] : 1;
                                $itemHeight = $gridItem && $gridItem['x'] === $gridColumn && $gridItem['y'] === $gridRow ? $gridItem['height'] : 1;
                                $itemColspan = ($itemWidth * 2) - 1;
                                $itemWidthMm = ($productCardWidth * $itemWidth) + ($productGap * ($itemWidth - 1));
                                $itemHeightMm = ($productRowHeight * $itemHeight) - $productCellPaddingTop - $productCellPaddingBottom;
                            @endphp

                            @if (! $gridItem)
                                <td class="product-cell" style="width:{{ $productCardWidth }}mm;">&nbsp;</td>
                            @elseif ($gridItem['type'] === 'product')
                                <td class="product-cell" style="width:{{ $productCardWidth }}mm; padding:{{ $productCellPaddingTop }}mm 0 {{ $productCellPaddingBottom }}mm 0; text-align:left; vertical-align:top;">
                                    @include('backend.product.catalogs._pdf_product_card', ['product' => $gridItem['value']])
                                </td>
                            @elseif ($gridItem['type'] === 'advertising')
                                <td colspan="{{ $itemColspan }}" rowspan="{{ $itemHeight }}" style="padding:{{ $productCellPaddingTop }}mm 0 {{ $productCellPaddingBottom }}mm 0; text-align:left; vertical-align:top; line-height:0; font-size:0;">
                                    <table style="width:{{ $itemWidthMm }}mm; height:{{ $itemHeightMm }}mm; border-collapse:collapse; table-layout:fixed; border:0.3mm solid #dfe5e8; background:#fff; margin:0;">
                                        <tr>
                                            <td style="width:{{ $itemWidthMm }}mm; height:{{ $itemHeightMm }}mm; padding:0; text-align:center; vertical-align:middle; line-height:0; font-size:0;">
                                                <img src="{{ $gridItem['value']['image'] }}" style="max-width:{{ $itemWidthMm - 1 }}mm; max-height:{{ $itemHeightMm - 1 }}mm; width:auto; height:auto; margin:0;">
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            @else
                                <td colspan="{{ $itemColspan }}" rowspan="{{ $itemHeight }}" style="padding:{{ $productCellPaddingTop }}mm 0 {{ $productCellPaddingBottom }}mm 0; text-align:left; vertical-align:top; overflow:hidden; line-height:0; font-size:0;">
                                    <table style="width:{{ $itemWidthMm }}mm; height:{{ $itemHeightMm }}mm; border-collapse:collapse; table-layout:fixed; margin:0;">
                                        <tr>
                                            <td style="width:{{ $itemWidthMm }}mm; height:{{ $itemHeightMm }}mm; padding:0; text-align:center; vertical-align:middle; line-height:0; font-size:0; overflow:hidden;">
                                                <img src="{{ $gridItem['value']['image'] }}" style="max-width:{{ $itemWidthMm }}mm; max-height:{{ $itemHeightMm }}mm; width:auto; height:auto; margin:0;">
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            @endif

                            @php $gridColumn += $itemWidth; @endphp
                            @if ($gridColumn < $productColumns)<td style="width:{{ $productGap }}mm;">&nbsp;</td>@endif
                        @endwhile
                        <td style="width:{{ $productSideSpace }}mm;">&nbsp;</td>
                    </tr>
                @endfor
            </table>
        @endforeach
    @endforeach
@endforeach

{{-- ═══════════════════════════════════════════════════════ --}}
{{-- PÁGINA DE MEDIOS DE PAGO, INFORMACIÓN Y EXTRA - FINAL   --}}
{{-- ═══════════════════════════════════════════════════════ --}}
@if ($settings['show_payment_page'] && $paymentImage && $paymentPagePosition === 'end')
    {!! $pageBreak() !!}
    <div class="pdf-page">
        <img class="full-page-bg" src="{{ $paymentImage }}" alt="">
    </div>
@endif

@if ($settings['show_info_page'] && $infoImage && $infoPagePosition === 'end')
    {!! $pageBreak() !!}
    <div class="pdf-page">
        <img class="full-page-bg" src="{{ $infoImage }}" alt="">
    </div>
@endif

@if ($extraPagesPosition === 'end')
    @foreach ($extraPageImages as $extraPageImage)
        {!! $pageBreak() !!}
        <div class="pdf-page">
            <img class="full-page-bg" src="{{ $extraPageImage }}" alt="">
        </div>
    @endforeach
@endif

{{-- ═══════════════════════════════════════════════════════ --}}
{{-- PAGINAS ADICIONALES - FINAL                             --}}
{{-- ═══════════════════════════════════════════════════════ --}}
@foreach ($additionalPagesByPosition->get('end', collect()) as $additionalPage)
    {!! $pageBreak() !!}
    <div class="pdf-page">
        <img class="full-page-bg" src="{{ $additionalPage['image'] }}" alt="">
    </div>
@endforeach

@if (! empty($settings['final_page_blank']) && $finalPageImage)
    {!! $pageBreak() !!}
    <div class="pdf-page">
        <img class="full-page-bg" src="{{ $finalPageImage }}" alt="">
    </div>
@endif

</body>
</html>
