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
        'product_box_colors'             => [],
        'product_text_colors'            => [],
    ], $settings ?? []);

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
    $pageImage = function ($value) use ($localFileUrl) {
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

        $asset = \App\Models\Upload::find($value);

        if (! $asset) {
            return null;
        }

        if (env('FILESYSTEM_DRIVER') !== 's3') {
            $localPath = public_path($asset->file_name);
            if (file_exists($localPath)) {
                return $localFileUrl($localPath);
            }
        }

        return uploaded_asset($value);
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
    $letterIntroAdsByKey = collect($settings['letter_intro_ads'] ?? [])->map(function ($item) use ($pageImage) {
        $categoryId = (int) ($item['category_id'] ?? 0);
        $letter = \Illuminate\Support\Str::upper($item['letter'] ?? '');

        return [
            'key' => $categoryId . '|' . $letter,
            'image' => $pageImage($item['image'] ?? null),
        ];
    })->filter(fn($i) => $i['key'] !== '0|' && $i['image'])->groupBy('key')->map(fn($items) => $items->first());

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

    $productsPerPage = in_array((int) ($settings['products_per_page'] ?? 12), [12, 20], true)
        ? (int) $settings['products_per_page']
        : 12;
    $compactProducts = $productsPerPage === 20;
    $productColumns = $compactProducts ? 4 : 3;
    $productTableColspan = ($productColumns * 2) + 1;
    $productSideSpace = $compactProducts ? 6 : 8;
    $productGap = $compactProducts ? 3 : 5;
    $productCardWidth = $compactProducts ? 45 : 58;
    $productCardHeight = $compactProducts ? 45 : 54;
    $productHeadHeight = $compactProducts ? 8 : 10;
    $productMediaHeight = $compactProducts ? 23 : 27;
    $productInfoHeight = $productCardHeight - $productHeadHeight - $productMediaHeight;
    $productImageWidth = $compactProducts ? 37 : 48;
    $productImageHeight = $compactProducts ? 20 : 23;
    $productRowHeight = $compactProducts ? 48 : 62;
    $productCellPaddingTop = $compactProducts ? 1.5 : 4;
    $productCellPaddingBottom = $compactProducts ? 1 : 3;
    $productHeaderHeight = $compactProducts ? 22 : 22;
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
    $pageBreak = function () use (&$pdfPageRendered) {
        if ($pdfPageRendered) {
            return '<pagebreak />';
        }

        $pdfPageRendered = true;
        return '';
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
        .cover-overlay-title { color: #ff5a00; font-size: 32px; font-weight: 700; text-transform: uppercase; line-height: 1.08; margin: 0; text-align: center; }
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

        /* ─── PRODUCTS ───────────────────────────── */
        .product-card  { border-collapse: collapse; }
        .product-banner { font-weight: 700; overflow: hidden; white-space: nowrap; padding: 2mm 4mm; height: 10mm; }
        .product-img-cell { width: 38mm; padding: 4mm 2mm 4mm 4mm; vertical-align: middle; text-align: center; height: 62mm; }
        .product-img   { max-width: 30mm; max-height: 50mm; }
        .product-info  { padding: 4mm; vertical-align: top; width: 52mm; }
        .product-name  { font-weight: 700; line-height: 1.25; margin: 0; }
        .product-price { font-weight: 700; color: #f36f21; margin: 2mm 0 0; }
        .product-ref   { color: #8a93a3; margin: 2mm 0 0; }
        .product-desc  { color: #59606b; line-height: 1.35; margin: 3mm 0 0; }
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
        .cover-overlay-table { width: 216mm; height: 276mm; border-collapse: collapse; }
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
        .product-sheet { width: 216mm; height: 276mm; border-collapse: collapse; background: #f5f7fb; }
        .product-sheet-header { height: {{ $productHeaderHeight }}mm; padding: 6mm {{ $compactProducts ? 8 : 10 }}mm 3mm; vertical-align: top; background: #ffffff; border-bottom: 0.35mm solid #dde5ef; }
        .product-category { color: #64748b; font-size: 8px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; }
        .product-letter { color: #111827; font-size: 30px; font-weight: 700; line-height: 1; text-align: right; }
        .product-row { height: {{ $productRowHeight }}mm; }
        .product-cell { width: {{ 100 / $productColumns }}%; vertical-align: top; }
        .product-card { width: {{ $productCardWidth }}mm; height: {{ $productCardHeight }}mm; border-collapse: collapse; table-layout: fixed; background: #ffffff; border: 0.3mm solid #dfe5e8; }
        .product-head { width: {{ $productCardWidth }}mm; height: {{ $productHeadHeight }}mm; padding: {{ $compactProducts ? 1.2 : 1.8 }}mm {{ $compactProducts ? 1.6 : 2.2 }}mm; font-weight: 700; line-height: 1.13; vertical-align: middle; text-transform: uppercase; }
        .product-media { width: {{ $productCardWidth }}mm; height: {{ $productMediaHeight }}mm; padding: {{ $compactProducts ? 1 : 1.5 }}mm; text-align: center; vertical-align: middle; background: #ffffff; }
        .product-img { max-width: {{ $productImageWidth }}mm; max-height: {{ $productImageHeight }}mm; }
        .product-info { width: {{ $productCardWidth }}mm; height: {{ $productInfoHeight }}mm; padding: 0 {{ $compactProducts ? 1.5 : 2 }}mm {{ $compactProducts ? 1.2 : 1.8 }}mm; text-align: center; vertical-align: middle; }
        .product-name { color: #111827; font-weight: 700; line-height: 1.22; margin: 0; }
        .product-price { color: #d9832e; font-weight: 500; line-height: 1.1; margin: 0; text-align: center; }
        .product-ref { color: #475569; font-weight: 400; line-height: 1.1; margin: 0; text-align: center; }
        .product-detail-table { width: 100%; border-collapse: collapse; }
        .product-detail-table td { padding: 0; vertical-align: middle; }
        .product-price-gap { height: {{ $compactProducts ? 1.5 : 2.2 }}mm; line-height: {{ $compactProducts ? 1.5 : 2.2 }}mm; font-size: 1px; }
        .product-advertising-cell { width: {{ $advertisingWidth }}mm; height: {{ $advertisingHeight }}mm; padding: {{ $productCellPaddingTop }}mm 0 {{ $productCellPaddingBottom }}mm; text-align: center; vertical-align: middle; }
        .product-advertising-frame { width: {{ $advertisingWidth }}mm; height: {{ $advertisingHeight - $productCellPaddingTop - $productCellPaddingBottom }}mm; border: 0.3mm solid #dfe5e8; background: #ffffff; text-align: center; vertical-align: middle; overflow: hidden; }
        .product-advertising-image { max-width: {{ $advertisingWidth - 1 }}mm; max-height: {{ $advertisingHeight - $productCellPaddingTop - $productCellPaddingBottom - 1 }}mm; }
        .product-desc { color: #64748b; line-height: 1.35; margin: 0; }
    </style>
</head>
<body>

{{-- ═══════════════════════════════════════════════════════ --}}
{{-- PORTADA                                                  --}}
{{-- ═══════════════════════════════════════════════════════ --}}
@if ($coverImage)
    {!! $pageBreak() !!}
    <div class="cover-page">
        <img class="full-page-bg" src="{{ $coverImage }}" alt="">
        <div style="margin-top:-276mm;">
            <table class="cover-overlay-table">
                <tr><td style="height:{{ $coverTitleTop }}mm;">&nbsp;</td></tr>
                <tr>
                    <td style="text-align:center; vertical-align:middle; padding-left:16mm; padding-right:16mm; height:{{ $coverTitleHeight }}mm;">
                        <div class="cover-overlay-title">{{ $catalogName }}</div>
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
        </div>
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
{{-- PÁGINA DE MEDIOS DE PAGO                                --}}
{{-- ═══════════════════════════════════════════════════════ --}}
@if ($settings['show_payment_page'] && $paymentImage)
    {!! $pageBreak() !!}
    <div class="pdf-page">
        <img class="full-page-bg" src="{{ $paymentImage }}" alt="">
    </div>
@endif

@if ($settings['show_info_page'] && $infoImage)
    {!! $pageBreak() !!}
    <div class="pdf-page">
        <img class="full-page-bg" src="{{ $infoImage }}" alt="">
    </div>
@endif

@foreach ($extraPageImages as $extraPageImage)
    {!! $pageBreak() !!}
    <div class="pdf-page">
        <img class="full-page-bg" src="{{ $extraPageImage }}" alt="">
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
            $letterIntroAd = $letterIntroAdsByKey->get($executiveCategoryId . '|' . $letter);
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

        @if ($letterIntroAd)
            {!! $pageBreak() !!}
            <div class="pdf-page">
                <img class="full-page-bg" src="{{ $letterIntroAd['image'] }}" alt="">
            </div>
        @endif

        @foreach ($letterPages as $letterPage)
            @php
                $chunk = $letterPage['products'];
                $advertisingItem = $letterPage['advertising'];
                $featuredProductColumns = $productColumns - 2;
                $featuredProductCount = $featuredProductColumns * 2;
                $featuredProducts = $advertisingItem
                    ? $chunk->take($featuredProductCount)->values()
                    : collect();
                $regularProducts = $advertisingItem
                    ? $chunk->slice($featuredProductCount)->values()
                    : $chunk;
            @endphp

            {!! $pageBreak() !!}
            <table class="product-sheet">
                <tr>
                    <td colspan="{{ $productTableColspan }}" class="product-sheet-header" style="text-align:right;">
                        <div class="product-letter" style="color:{{ $boxColor }};">{{ $letter }}</div>
                    </td>
                </tr>

                @if ($advertisingItem)
                    @for ($featuredRow = 0; $featuredRow < 2; $featuredRow++)
                        <tr class="product-row">
                            <td style="width:{{ $productSideSpace }}mm;">&nbsp;</td>

                            @for ($featuredColumn = 0; $featuredColumn < $featuredProductColumns; $featuredColumn++)
                                @php
                                    $featuredProduct = $featuredProducts->get(($featuredRow * $featuredProductColumns) + $featuredColumn);
                                @endphp

                                <td class="product-cell" style="width:{{ $productCardWidth }}mm; padding-top:{{ $productCellPaddingTop }}mm; padding-bottom:{{ $productCellPaddingBottom }}mm; vertical-align:top;">
                                    @if ($featuredProduct)
                                        @include('backend.product.catalogs._pdf_product_card', ['product' => $featuredProduct])
                                    @else
                                        &nbsp;
                                    @endif
                                </td>
                                <td style="width:{{ $productGap }}mm;">&nbsp;</td>
                            @endfor

                            @if ($featuredRow === 0)
                                <td class="product-advertising-cell" colspan="3" rowspan="2">
                                    <table class="product-advertising-frame">
                                        <tr>
                                            <td style="text-align:center; vertical-align:middle;">
                                                <img class="product-advertising-image" src="{{ $advertisingItem['image'] }}" alt="">
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            @endif

                            <td style="width:{{ $productSideSpace }}mm;">&nbsp;</td>
                        </tr>
                    @endfor
                @endif

                @foreach ($regularProducts->chunk($productColumns) as $row)
                    @php $rowProducts = $row->values(); @endphp
                    <tr class="product-row">
                        <td style="width:{{ $productSideSpace }}mm;">&nbsp;</td>

                        @for ($productSlot = 0; $productSlot < $productColumns; $productSlot++)
                            @php $product = $rowProducts->get($productSlot); @endphp

                            @if ($product)
                                <td class="product-cell" style="width:{{ $productCardWidth }}mm; padding-top:{{ $productCellPaddingTop }}mm; padding-bottom:{{ $productCellPaddingBottom }}mm; vertical-align:top;">
                                    @include('backend.product.catalogs._pdf_product_card', ['product' => $product])
                                </td>
                            @else
                                <td class="product-cell" style="width:{{ $productCardWidth }}mm;">&nbsp;</td>
                            @endif

                            @if ($productSlot < ($productColumns - 1))
                                <td style="width:{{ $productGap }}mm;">&nbsp;</td>
                            @endif
                        @endfor

                        <td style="width:{{ $productSideSpace }}mm;">&nbsp;</td>
                    </tr>
                @endforeach
            </table>
        @endforeach
    @endforeach
@endforeach

@if ($finalPageImage)
    {!! $pageBreak() !!}
    <div class="pdf-page">
        <img class="full-page-bg" src="{{ $finalPageImage }}" alt="">
    </div>
@endif

@foreach ([] as $categoryGroup)
    @foreach ($categoryGroup['letter_groups'] as $letter => $letterProducts)
        @foreach ($letterProducts->chunk(6) as $chunkIdx => $chunk)
            @php
                $boxColor  = $productBoxColors[$letter] ?? $letterPalette[$letter] ?? '#f36f21';
                $textColor = $settings['product_text_colors'][$letter] ?? '#ffffff';
            @endphp

            {{-- Una tabla por página de productos --}}
            {!! $pageBreak() !!}
            <table class="pdf-page" style="width:216mm; border-collapse:collapse;">

                {{-- Fila encabezado de letra (22mm) --}}
                <tr style="height:22mm;">
                    <td colspan="2" style="text-align:right; vertical-align:bottom;
                        padding:0 10mm 0 10mm; font-size:34px; font-weight:700; color:{{ $boxColor }};">
                        {{ $letter }}
                    </td>
                </tr>

                {{-- Filas de productos (máx 3 × 2 = 6 productos) --}}
                @foreach ($chunk->values()->chunk(2) as $row)
                    <tr style="height:80mm;">
                        @foreach ($row as $colIdx => $product)
                            @php
                                $image = $pageImage($product->thumbnail_img)
                                    ?: $pageImage($product->meta_image)
                                    ?: $fallbackImage;
                                $description = trim(strip_tags(
                                    $product->getTranslation('description')
                                    ?: $product->meta_description
                                    ?: ''
                                ));
                                $description = \Illuminate\Support\Str::limit($description, $descriptionLimit);
                                $leftPad  = $colIdx === 0 ? '10mm' : '2mm';
                                $rightPad = $colIdx === 0 ? '2mm'  : '10mm';
                            @endphp
                            <td style="width:50%; padding:2mm {{ $rightPad }} 2mm {{ $leftPad }}; vertical-align:top;">

                                {{-- Tarjeta de producto (tabla anidada) --}}
                                <table class="product-card" style="width:94mm; height:72mm;
                                    border:1px solid {{ $boxColor }};">

                                    {{-- Banner de color con nombre --}}
                                    <tr>
                                        <td colspan="2" class="product-banner"
                                            style="background-color:{{ $boxColor }}; color:{{ $textColor }};
                                                   font-size:{{ (int) $settings['product_title_font_size'] }}px;">
                                            {{ $product->getTranslation('name') }}
                                        </td>
                                    </tr>

                                    {{-- Imagen + info --}}
                                    <tr>
                                        <td class="product-img-cell">
                                            @if ($image)
                                                <img class="product-img" src="{{ $image }}" alt="">
                                            @endif
                                        </td>
                                        <td class="product-info">
                                            <div class="product-name"
                                                 style="font-family:{{ $cssFont($settings['product_title_font_family']) }}, sans-serif;
                                                        font-size:{{ (int) $settings['product_title_font_size'] }}px;">
                                                {{ $product->getTranslation('name') }}
                                            </div>

                                            @if ($settings['show_prices'])
                                                <div class="product-price"
                                                     style="font-family:{{ $cssFont($settings['product_price_font_family']) }}, sans-serif;
                                                            font-size:{{ (int) $settings['product_price_font_size'] }}px;">
                                                    {{ format_price($product->lowest_price) }}
                                                </div>
                                            @endif

                                            @if ($product->reference)
                                                <div class="product-ref"
                                                     style="font-family:{{ $cssFont($settings['product_reference_font_family']) }}, sans-serif;
                                                            font-size:{{ (int) $settings['product_reference_font_size'] }}px;">
                                                    {{ $product->reference }}
                                                </div>
                                            @endif

                                            @if ($description)
                                                <div class="product-desc"
                                                     style="font-family:{{ $cssFont($settings['product_description_font_family']) }}, sans-serif;
                                                            font-size:{{ (int) $settings['product_description_font_size'] }}px;">
                                                    {{ $description }}
                                                </div>
                                            @endif
                                        </td>
                                    </tr>

                                </table>
                            </td>
                        @endforeach

                        {{-- Celda vacía si la fila tiene solo 1 producto --}}
                        @if ($row->count() === 1)
                            <td style="width:50%;"></td>
                        @endif
                    </tr>
                @endforeach

            </table>

            {{-- Página de publicidad después de esta letra --}}
            @if ($advertisingByLetter->has($letter))
                @foreach ($advertisingByLetter->get($letter) as $advertisingItem)
                    {!! $pageBreak() !!}
                    <div class="pdf-page">
                        <img src="{{ $advertisingItem['image'] }}" style="display:block; width:216mm; height:276mm;" alt="">
                    </div>
                @endforeach
            @endif

        @endforeach
    @endforeach
@endforeach

</body>
</html>
