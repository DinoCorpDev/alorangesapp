@php
    $settings = array_merge([
        'show_prices'                    => true,
        'show_payment_page'              => true,
        'show_info_page'                 => true,
        'show_page_four'                 => false,
        'description_limit'              => 90,
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
    $coverPaddingTop = match ($coverTitlePosition) {
        'top'    => '18mm',
        'bottom' => ($hasAdvisor ? '158mm' : '208mm'),
        default  => ($hasAdvisor ? '82mm'  : '110mm'),
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

        /* Each .pdf-page triggers a page break after itself */
        .pdf-page { page-break-after: always; }

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
    </style>
</head>
<body>

{{-- ═══════════════════════════════════════════════════════ --}}
{{-- PORTADA                                                  --}}
{{-- ═══════════════════════════════════════════════════════ --}}
@if ($coverImage)
    {{-- Portada con imagen de fondo usando background-image en tabla (mPDF soportado) --}}
    <table class="pdf-page" style="width:216mm; height:279mm; border-collapse:collapse;
           background-image:url('{{ $coverImage }}'); background-size:cover; background-position:center;">
        <tr>
            <td style="text-align:center; vertical-align:top;
                padding-top:{{ $coverPaddingTop }}; padding-left:16mm; padding-right:16mm;
                height:{{ $hasAdvisor ? '220mm' : '279mm' }};">
                <div class="cover-overlay-title">{{ $catalogName }}</div>
            </td>
        </tr>
        @if ($hasAdvisor)
            <tr>
                <td style="text-align:center; vertical-align:bottom; padding:0 12mm 10mm; height:59mm;">
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
@else
    {{-- Portada sin imagen: fondo plano --}}
    <table class="pdf-page cover-bg" style="width:216mm; height:279mm; border-collapse:collapse;">
        <tr>
            <td style="text-align:center; vertical-align:top;
                padding-top:{{ $coverPaddingTop }}; padding-left:20mm; padding-right:20mm;
                height:{{ $hasAdvisor ? '229mm' : '279mm' }};">
                <div class="cover-title">{{ $catalogName }}</div>
                <div class="cover-meta">{{ $categories->map(fn($c) => $c->getTranslation('name'))->join(' - ') }}</div>
            </td>
        </tr>
        @if ($hasAdvisor)
            <tr>
                <td style="text-align:center; vertical-align:bottom; padding:0 12mm 10mm; height:50mm;">
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
    <div class="pdf-page" style="padding:34mm 20mm 18mm; position:relative;">
        @if ($paymentImage)
            {{-- Imagen de fondo absoluta — en mPDF position:absolute es relativo a la página --}}
            <img src="{{ $paymentImage }}" style="position:absolute; top:0mm; left:0mm; width:216mm; height:279mm;">
        @endif

        <div class="payment-title">{{ $settings['payment_title'] ?: 'MEDIOS DE PAGO' }}</div>

        <div class="payment-pill">{{ $settings['payment_delivery_title'] }}</div>

        <div class="payment-box">
            <table style="width:100%; border-collapse:collapse;">
                <tr>
                    <td style="vertical-align:middle;">{!! nl2br(e($settings['payment_bank_info'])) !!}</td>
                    @if ($paymentBankIcon)
                        <td style="width:34mm; text-align:center; vertical-align:middle; padding-left:4mm;">
                            <img class="payment-icon" src="{{ $paymentBankIcon }}" alt="">
                        </td>
                    @endif
                </tr>
            </table>
        </div>

        <table style="width:100%; border-collapse:separate; border-spacing:4mm 0; margin-bottom:7mm;">
            <tr>
                <td style="width:50%; border:1.2mm solid #008847; vertical-align:top; padding:0;">
                    <div class="payment-column-title">{{ $settings['payment_debit_title'] }}</div>
                    <div class="payment-column-body">
                        @if ($paymentDebitIcon)
                            <img class="payment-column-icon" src="{{ $paymentDebitIcon }}" alt=""><br>
                        @endif
                        {!! nl2br(e($settings['payment_debit_info'])) !!}
                    </div>
                </td>
                <td style="width:50%; border:1.2mm solid #008847; vertical-align:top; padding:0;">
                    <div class="payment-column-title">{{ $settings['payment_credit_title'] }}</div>
                    <div class="payment-column-body">
                        @if ($paymentCreditIcon)
                            <img class="payment-column-icon" src="{{ $paymentCreditIcon }}" alt=""><br>
                        @endif
                        {!! nl2br(e($settings['payment_credit_info'])) !!}
                    </div>
                </td>
            </tr>
        </table>

        <div class="payment-pill">{{ $settings['payment_cash_title'] }}</div>
        <div class="payment-box" style="text-align:center;">
            @if ($paymentCashIcon)
                <img class="payment-column-icon" src="{{ $paymentCashIcon }}" alt=""><br>
            @endif
            {!! nl2br(e($settings['payment_cash_info'])) !!}
        </div>
    </div>
@endif

{{-- ═══════════════════════════════════════════════════════ --}}
{{-- PÁGINA DE INFORMACIÓN                                    --}}
{{-- ═══════════════════════════════════════════════════════ --}}
@if ($settings['show_info_page'])
    <div class="pdf-page" style="padding:38mm 20mm 22mm; position:relative;">
        @if ($infoImage)
            <img src="{{ $infoImage }}" style="position:absolute; top:0mm; left:0mm; width:216mm; height:279mm;">
        @endif

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
@endif

{{-- ═══════════════════════════════════════════════════════ --}}
{{-- PÁGINA 4 (imagen opcional)                              --}}
{{-- ═══════════════════════════════════════════════════════ --}}
@if ($settings['show_page_four'] && $pageFourImage)
    <div class="pdf-page">
        <img src="{{ $pageFourImage }}" style="display:block; width:216mm; height:279mm;" alt="">
    </div>
@endif

{{-- ═══════════════════════════════════════════════════════ --}}
{{-- PÁGINAS DE PRODUCTOS                                     --}}
{{-- ═══════════════════════════════════════════════════════ --}}
@foreach ($productsByCategory as $categoryGroup)
    @foreach ($categoryGroup['letter_groups'] as $letter => $letterProducts)
        @foreach ($letterProducts->chunk(6) as $chunkIdx => $chunk)
            @php
                $boxColor  = $productBoxColors[$letter] ?? $letterPalette[$letter] ?? '#f36f21';
                $textColor = $settings['product_text_colors'][$letter] ?? '#ffffff';
            @endphp

            {{-- Una tabla por página de productos --}}
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
                    <div class="pdf-page">
                        <img src="{{ $advertisingItem['image'] }}" style="display:block; width:216mm; height:279mm;" alt="">
                    </div>
                @endforeach
            @endif

        @endforeach
    @endforeach
@endforeach

</body>
</html>
