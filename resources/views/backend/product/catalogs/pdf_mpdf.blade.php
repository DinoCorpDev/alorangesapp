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

    $productsPerPage = in_array((int) ($settings['products_per_page'] ?? 12), [6, 12], true)
        ? (int) $settings['products_per_page']
        : 12;
    $productColumns = $productsPerPage === 6 ? 2 : 3;
    $productTableColspan = ($productColumns * 2) + 1;
    $productSideSpace = $productsPerPage === 6 ? 12 : 8;
    $productGap = $productsPerPage === 6 ? 10 : 5;
    $productCardWidth = $productsPerPage === 6 ? 91 : 58;
    $productCardHeight = $productsPerPage === 6 ? 74 : 54;
    $productMediaWidth = $productsPerPage === 6 ? 34 : 20;
    $productMediaHeight = $productCardHeight;
    $productImageWidth = $productsPerPage === 6 ? 30 : 17;
    $productImageHeight = $productsPerPage === 6 ? 68 : 49;
    $productContentWidth = $productCardWidth - $productMediaWidth;
    $productHeadHeight = $productsPerPage === 6 ? 13 : 10;
    $productInfoHeight = $productCardHeight - $productHeadHeight;
    $productRowHeight = $productsPerPage === 6 ? 82 : 62;
    $productHeaderHeight = $productsPerPage === 6 ? 25 : 22;
    $productNameLimit = $productsPerPage === 6 ? 82 : 48;
    $productBannerLimit = $productsPerPage === 6 ? 48 : 28;

    $safeTitleFontSize = $productsPerPage === 6
        ? min(14, max(8, (int) ($settings['product_title_font_size'] ?: 12)))
        : min(10, max(7, (int) ($settings['product_title_font_size'] ?: 10)));
    $safeDescriptionFontSize = min(9, max(7, (int) ($settings['product_description_font_size'] ?: 8)));
    $safePriceFontSize = $productsPerPage === 6
        ? min(18, max(11, (int) ($settings['product_price_font_size'] ?: 16)))
        : min(13, max(9, (int) ($settings['product_price_font_size'] ?: 13)));
    $safeReferenceFontSize = $productsPerPage === 6
        ? min(12, max(8, (int) ($settings['product_reference_font_size'] ?: 11)))
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
        .product-sheet-header { height: {{ $productHeaderHeight }}mm; padding: {{ $productsPerPage === 6 ? 7 : 6 }}mm {{ $productsPerPage === 6 ? 12 : 10 }}mm {{ $productsPerPage === 6 ? 4 : 3 }}mm; vertical-align: top; background: #ffffff; border-bottom: 0.35mm solid #dde5ef; }
        .product-category { color: #64748b; font-size: 8px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; }
        .product-letter { color: #111827; font-size: 30px; font-weight: 700; line-height: 1; text-align: right; }
        .product-row { height: {{ $productRowHeight }}mm; }
        .product-cell { width: {{ 100 / $productColumns }}%; vertical-align: top; }
        .product-card { width: {{ $productCardWidth }}mm; height: {{ $productCardHeight }}mm; border-collapse: collapse; table-layout: fixed; background: #ffffff; border: 0.35mm solid #d8e0ea; }
        .product-media { width: {{ $productMediaWidth }}mm; height: {{ $productMediaHeight }}mm; padding: {{ $productsPerPage === 6 ? 2 : 1.5 }}mm; text-align: center; vertical-align: middle; background: #f8fafc; border-right: 0.35mm solid #e5e7eb; }
        .product-img { max-width: {{ $productImageWidth }}mm; max-height: {{ $productImageHeight }}mm; }
        .product-head { width: {{ $productContentWidth }}mm; height: {{ $productHeadHeight }}mm; padding: {{ $productsPerPage === 6 ? 2 : 1.5 }}mm {{ $productsPerPage === 6 ? 3 : 2 }}mm; font-weight: 700; line-height: 1.12; vertical-align: middle; }
        .product-info { width: {{ $productContentWidth }}mm; height: {{ $productInfoHeight }}mm; padding: {{ $productsPerPage === 6 ? 5 : 4 }}mm {{ $productsPerPage === 6 ? 3 : 2.5 }}mm {{ $productsPerPage === 6 ? 3 : 2.5 }}mm; vertical-align: top; }
        .product-name { color: #111827; font-weight: 700; line-height: 1.22; margin: 0; }
        .product-price { color: #f36f21; font-weight: 700; line-height: 1.08; margin: 0; }
        .product-ref { color: #475569; font-weight: 700; line-height: 1.1; margin: 0; }
        .product-detail-table { width: 100%; border-collapse: collapse; }
        .product-detail-table td { padding: 0; vertical-align: top; }
        .product-text-gap { height: 6mm; line-height: 6mm; font-size: 1px; }
        .product-price-gap { height: 6mm; line-height: 6mm; font-size: 1px; }
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
@if ($settings['show_payment_page'])
    {!! $pageBreak() !!}
    <div class="report-page">
        @if ($paymentImage)
            <img class="full-page-bg" src="{{ $paymentImage }}" alt="">
        @endif
        <div class="report-overlay" style="@if ($paymentImage) margin-top:-218mm; @else margin-top:58mm; @endif">
            <div class="report-title">{{ $settings['payment_title'] ?: 'MEDIOS DE PAGO' }}</div>
            <div class="report-accent"></div>

            <div class="report-card">
                <table style="width:100%; border-collapse:collapse;">
                    <tr>
                        <td style="vertical-align:top;">
                            <div class="report-card-title">{{ $settings['payment_delivery_title'] }}</div>
                            <div class="report-card-body">{!! nl2br(e($settings['payment_bank_info'])) !!}</div>
                        </td>
                        @if ($paymentBankIcon)
                            <td style="width:28mm; text-align:center; vertical-align:middle;">
                                <img class="payment-icon" src="{{ $paymentBankIcon }}" alt="">
                            </td>
                        @endif
                    </tr>
                </table>
            </div>

            <table class="payment-method-table">
                <tr>
                    <td class="payment-method-card" style="width:50%;">
                        <div class="payment-method-title">{{ $settings['payment_debit_title'] }}</div>
                        @if ($paymentDebitIcon)
                            <div style="text-align:center; margin-bottom:2mm;"><img class="payment-icon" src="{{ $paymentDebitIcon }}" alt=""></div>
                        @endif
                        <div class="report-card-body">{!! nl2br(e($settings['payment_debit_info'])) !!}</div>
                    </td>
                    <td class="payment-method-card" style="width:50%;">
                        <div class="payment-method-title">{{ $settings['payment_credit_title'] }}</div>
                        @if ($paymentCreditIcon)
                            <div style="text-align:center; margin-bottom:2mm;"><img class="payment-icon" src="{{ $paymentCreditIcon }}" alt=""></div>
                        @endif
                        <div class="report-card-body">{!! nl2br(e($settings['payment_credit_info'])) !!}</div>
                    </td>
                </tr>
            </table>

            <div class="payment-cash-card">
                <table style="width:100%; border-collapse:collapse;">
                    <tr>
                        <td style="vertical-align:top;">
                            <div class="payment-method-title">{{ $settings['payment_cash_title'] }}</div>
                            <div class="report-card-body">{!! nl2br(e($settings['payment_cash_info'])) !!}</div>
                        </td>
                        @if ($paymentCashIcon)
                            <td style="width:28mm; text-align:center; vertical-align:middle;">
                                <img class="payment-icon" src="{{ $paymentCashIcon }}" alt="">
                            </td>
                        @endif
                    </tr>
                </table>
            </div>
        </div>
    </div>
@endif

{{-- ═══════════════════════════════════════════════════════ --}}
{{-- PÁGINA DE INFORMACIÓN                                    --}}
{{-- ═══════════════════════════════════════════════════════ --}}
@if ($settings['show_info_page'])
    {!! $pageBreak() !!}
    <div class="report-page">
        @if ($infoImage)
            <img class="full-page-bg" src="{{ $infoImage }}" alt="">
        @endif
        <div class="report-overlay" style="@if ($infoImage) margin-top:-218mm; @else margin-top:58mm; @endif">
            <div class="report-title">{{ $settings['info_page_title'] ?: 'INFORMACION' }}</div>
            <div class="report-accent"></div>

            @if (! empty($infoRows))
                <table class="info-table">
                    @foreach ($infoRows as $row)
                        <tr>
                            <td class="info-table-label">{{ $row['label'] ?? '' }}</td>
                            <td>{{ $row['value'] ?? '' }}</td>
                        </tr>
                    @endforeach
                </table>
            @else
                <div class="report-card">
                    <div class="report-card-body">&nbsp;</div>
                </div>
            @endif
        </div>
    </div>
@endif

@if (false && $settings['show_info_page'])
    {!! $pageBreak() !!}
    <div class="pdf-page" style="width:216mm; height:276mm; overflow:hidden;">
        @if ($infoImage)
            <img src="{{ $infoImage }}" style="display:block; width:216mm; height:276mm;" alt="">
        @endif
        <div style="@if ($infoImage) margin-top:-226mm; @else margin-top:50mm; @endif margin-left:20mm; width:176mm;">
        <div class="info-title">{{ $settings['info_page_title'] ?: 'INFORMACIÓN' }}</div>

        @if (! empty($infoRows))
            <table class="info-table">
                @foreach ($infoRows as $row)
                    <tr>
                        <td class="info-table-label">{{ $row['label'] ?? '' }}</td>
                        <td>{{ $row['value'] ?? '' }}</td>
                    </tr>
                @endforeach
            </table>
        @else
            <div style="border:1.2mm solid #008847; padding:6mm; min-height:20mm;"></div>
        @endif
        </div>
    </div>
@endif

{{-- ═══════════════════════════════════════════════════════ --}}
{{-- PÁGINA 4 (imagen opcional)                              --}}
{{-- ═══════════════════════════════════════════════════════ --}}
@if ($settings['show_page_four'] && $pageFourImage)
    {!! $pageBreak() !!}
    <div class="pdf-page">
        <img src="{{ $pageFourImage }}" style="display:block; width:216mm; height:276mm;" alt="">
    </div>
@endif

{{-- ═══════════════════════════════════════════════════════ --}}
{{-- PÁGINAS DE PRODUCTOS                                     --}}
{{-- ═══════════════════════════════════════════════════════ --}}
@foreach ($productsByCategory as $executiveCategoryGroup)
    @php
        $executiveCategoryName = $executiveCategoryGroup['category']
            ? $executiveCategoryGroup['category']->getTranslation('name')
            : '';
    @endphp

    @foreach ($executiveCategoryGroup['letter_groups'] as $letter => $letterProducts)
        @foreach ($letterProducts->chunk($productsPerPage) as $chunk)
            @php
                $boxColor  = $productBoxColors[$letter] ?? $letterPalette[$letter] ?? '#f36f21';
                $textColor = $settings['product_text_colors'][$letter] ?? '#ffffff';
            @endphp

            {!! $pageBreak() !!}
            <table class="product-sheet">
                <tr>
                    <td colspan="{{ $productTableColspan }}" class="product-sheet-header" style="text-align:right;">
                        <div class="product-letter" style="color:{{ $boxColor }};">{{ $letter }}</div>
                    </td>
                </tr>

                @foreach ($chunk->values()->chunk($productColumns) as $row)
                    @php $rowProducts = $row->values(); @endphp
                    <tr class="product-row">
                        <td style="width:{{ $productSideSpace }}mm;">&nbsp;</td>

                        @for ($productSlot = 0; $productSlot < $productColumns; $productSlot++)
                            @php $product = $rowProducts->get($productSlot); @endphp

                            @if ($product)
                                @php
                                    $image = $pageImage($product->thumbnail_img)
                                        ?: $pageImage($product->meta_image)
                                        ?: $fallbackImage;
                                    $rawName = trim($product->getTranslation('name'));
                                    $displayName = \Illuminate\Support\Str::limit($rawName, $productNameLimit);
                                    $bannerName = \Illuminate\Support\Str::limit($rawName, $productBannerLimit);
                                @endphp
                                <td class="product-cell" style="width:{{ $productCardWidth }}mm; padding-top:4mm; padding-bottom:3mm; vertical-align:top;">
                                    <table class="product-card" style="border-color:{{ $boxColor }};">
                                        <tr>
                                            <td class="product-media" rowspan="2">
                                                @if ($image)
                                                    <img class="product-img" src="{{ $image }}" alt="">
                                                @endif
                                            </td>
                                            <td class="product-head" style="background-color:{{ $boxColor }}; color:{{ $textColor }}; font-family:{{ $cssFont($settings['product_title_font_family']) }}, sans-serif; font-size:{{ $safeTitleFontSize }}px;">
                                                {{ $bannerName }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="product-info">
                                                <table class="product-detail-table">
                                                    <tr>
                                                        <td class="product-name" style="font-family:{{ $cssFont($settings['product_title_font_family']) }}, sans-serif; font-size:{{ $safeTitleFontSize }}px;">
                                                            {{ $displayName }}
                                                        </td>
                                                    </tr>

                                                    @if ($settings['show_prices'])
                                                        <tr><td class="product-text-gap">&nbsp;</td></tr>
                                                        <tr>
                                                            <td class="product-price" style="font-family:{{ $cssFont($settings['product_price_font_family']) }}, sans-serif; font-size:{{ $safePriceFontSize }}px;">
                                                                {{ format_price($product->lowest_price) }}
                                                            </td>
                                                        </tr>
                                                    @endif

                                                    @if ($product->reference)
                                                        <tr><td class="product-price-gap">&nbsp;</td></tr>
                                                        <tr>
                                                            <td class="product-ref" style="font-family:{{ $cssFont($settings['product_reference_font_family']) }}, sans-serif; font-size:{{ $safeReferenceFontSize }}px;">
                                                                Ref. {{ $product->reference }}
                                                            </td>
                                                        </tr>
                                                    @endif
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
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
