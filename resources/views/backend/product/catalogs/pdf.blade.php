@php
    $settings = array_merge([
        'show_prices' => true,
        'show_payment_page' => true,
        'show_info_page' => true,
        'show_page_four' => false,
        'description_limit' => 90,
        'cover_image' => null,
        'cover_title_position' => 'middle',
        'advisor_name' => '',
        'advisor_phone' => '',
        'advisor_email_1' => '',
        'advisor_email_2' => '',
        'advertising_image' => null,
        'advertising_position' => 'before_products',
        'advertising_items' => [],
        'payment_page_image' => null,
        'payment_bank_icon' => null,
        'payment_debit_icon' => null,
        'payment_credit_icon' => null,
        'payment_cash_icon' => null,
        'info_page_image' => null,
        'page_four_image' => null,
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
    ], $settings ?? []);

    $localFileUrl = function ($path) {
        if (! $path || ! file_exists($path)) {
            return null;
        }

        return 'file:///' . str_replace('\\', '/', $path);
    };
    $localPublicAsset = function ($path) use ($localFileUrl) {
        return $localFileUrl(public_path(ltrim((string) $path, '/')));
    };
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

            return static_asset($value);
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
    $coverImage = $pageImage($settings['cover_image']);
    $paymentImage = $pageImage($settings['payment_page_image']);
    $paymentBankIcon = $pageImage($settings['payment_bank_icon']);
    $paymentDebitIcon = $pageImage($settings['payment_debit_icon']);
    $paymentCreditIcon = $pageImage($settings['payment_credit_icon']);
    $paymentCashIcon = $pageImage($settings['payment_cash_icon']);
    $infoImage = $pageImage($settings['info_page_image']);
    $pageFourImage = $pageImage($settings['page_four_image']);
    $fallbackImage = $localPublicAsset('assets/img/item-placeholder.png') ?: ($fallbackImage ?? null);
    $advertisingItems = $settings['advertising_items'] ?? [];
    if (empty($advertisingItems) && ! empty($settings['advertising_image']) && ($settings['advertising_position'] ?? '') === 'after_each_letter') {
        $advertisingItems = [['image' => $settings['advertising_image'], 'letter' => 'A']];
    }
    $advertisingByLetter = collect($advertisingItems)->map(function ($item) use ($pageImage) {
        return [
            'letter' => \Illuminate\Support\Str::upper($item['letter'] ?? ''),
            'image' => $pageImage($item['image'] ?? null),
        ];
    })->filter(function ($item) {
        return $item['letter'] && $item['image'];
    })->groupBy('letter');
    $descriptionLimit = (int) ($settings['description_limit'] ?: 90);
    $productBoxColors = $settings['product_box_colors'] ?? [];
    if (count($productBoxColors) > 1 && count(array_unique($productBoxColors)) === 1 && reset($productBoxColors) === '#f36f21') {
        $productBoxColors = [];
    }
    $cssFont = function ($fontFamily) {
        return str_replace("'", '', $fontFamily ?: 'DejaVu Sans');
    };
    $infoRows = $settings['info_table_rows'] ?? [];
    if (empty($infoRows) && ! empty($settings['info_page_content'])) {
        $infoRows = collect(preg_split('/\r\n|\r|\n/', $settings['info_page_content']))
            ->filter()
            ->map(function ($line) {
                $parts = explode(':', $line, 2);
                return [
                    'label' => trim($parts[0] ?? ''),
                    'value' => trim($parts[1] ?? $line),
                ];
            })->values()->all();
    }
    $coverTitlePosition = in_array($settings['cover_title_position'] ?? 'middle', ['top', 'middle', 'bottom'], true)
        ? $settings['cover_title_position']
        : 'middle';
    $advisorEmails = collect([$settings['advisor_email_1'] ?? '', $settings['advisor_email_2'] ?? ''])->filter()->values();
@endphp
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        @page { margin: 0; size: letter; }
        * { box-sizing: border-box; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
        body { margin: 0; font-family: DejaVu Sans, sans-serif; color: #2f3138; font-size: 11px; }
        .page { width: 216mm; height: 279mm; page-break-after: always; position: relative; overflow: hidden; }
        .page:last-child { page-break-after: auto; }
        .full-page-image { width: 216mm; height: 279mm; object-fit: cover; }
        .page-background { position: absolute; inset: 0; width: 216mm; height: 279mm; object-fit: cover; z-index: 0; }
        .payment-page > :not(.page-background), .info-page > :not(.page-background) { position: relative; z-index: 1; }
        .cover { background: #f4f5f7; padding: 28mm 20mm; }
        .cover-title { position: absolute; left: 20mm; right: 20mm; text-align: center; font-size: 34px; font-weight: 700; color: #f36f21; line-height: 1.15; margin: 0; }
        .cover-meta { position: absolute; left: 20mm; right: 20mm; text-align: center; font-size: 14px; color: #555; margin: 0; }
        .cover-overlay-title { position: absolute; left: 16mm; right: 16mm; text-align: center; color: #ff5a00; font-size: 32px; line-height: 1.08; font-weight: 700; text-transform: uppercase; }
        .cover-title-position-top .cover-title, .cover-title-position-top .cover-overlay-title { top: 58mm; transform: none; }
        .cover-title-position-top .cover-meta { top: 89mm; }
        .cover-title-position-middle .cover-title, .cover-title-position-middle .cover-overlay-title { top: 50%; transform: translateY(-50%); }
        .cover-title-position-middle .cover-meta { top: calc(50% + 24mm); }
        .cover-title-position-bottom .cover-title, .cover-title-position-bottom .cover-overlay-title { bottom: 38mm; transform: none; }
        .cover-title-position-bottom .cover-meta { bottom: 28mm; }
        .cover-advisor { position: absolute; left: 12mm; right: 12mm; bottom: 10mm; text-align: center; color: #007a3d; font-size: 25px; font-weight: 700; line-height: 1.25; }
        .cover-advisor-name { display: block; margin-bottom: 2.5mm; text-transform: uppercase; text-align: center; }
        .cover-advisor-contact { display: block; max-width: 185mm; margin: 0 auto; white-space: normal; overflow-wrap: anywhere; word-break: break-word; font-size: 20px; line-height: 1.2; }
        .category-heading { position: absolute; top: 7mm; right: 10mm; width: 18mm; height: 18mm; padding: 0; text-align: right; font-size: 34px; line-height: 18mm; font-weight: 700; }
        .product-table { width: 196mm; margin: 22mm 10mm 0; border-collapse: separate; border-spacing: 4mm 4mm; }
        .product-card { width: 94mm; height: 72mm; border: 1px solid #e5e7eb; background: #fff; vertical-align: top; padding: 0; }
        .product-banner { height: 10mm; padding: 2mm 4mm; font-size: 12px; font-weight: 700; overflow: hidden; white-space: nowrap; }
        .product-body { padding: 0; height: 62mm; }
        .product-layout { width: 100%; height: 62mm; border-collapse: collapse; table-layout: fixed; }
        .product-image-cell { width: 38mm; padding: 4mm 2mm 4mm 4mm; vertical-align: middle; text-align: center; }
        .product-image { width: 32mm; height: 54mm; object-fit: contain; }
        .product-info-cell { padding: 4mm 4mm 4mm 2mm; vertical-align: top; }
        .product-name { font-family: '{{ $cssFont($settings['product_title_font_family']) }}', sans-serif; font-size: {{ (int) $settings['product_title_font_size'] }}px; font-weight: 700; line-height: 1.25; min-height: 13mm; }
        .product-info-cell.without-description .product-name { min-height: 25mm; }
        .product-price { font-family: '{{ $cssFont($settings['product_price_font_family']) }}', sans-serif; font-size: {{ (int) $settings['product_price_font_size'] }}px; font-weight: 700; color: #f36f21; margin-top: 2mm; }
        .product-description { font-family: '{{ $cssFont($settings['product_description_font_family']) }}', sans-serif; padding-top: 3mm; font-size: {{ (int) $settings['product_description_font_size'] }}px; color: #59606b; line-height: 1.35; }
        .product-reference { font-family: '{{ $cssFont($settings['product_reference_font_family']) }}', sans-serif; font-size: {{ (int) $settings['product_reference_font_size'] }}px; color: #8a93a3; margin-top: 2mm; }
        .empty-special-page { padding: 32mm 20mm; text-align: center; background: #f4f5f7; }
        .empty-special-page h1 { color: #f36f21; font-size: 28px; margin-bottom: 8mm; }
        .payment-page { padding: 34mm 20mm 18mm; background: #fff; }
        .payment-title { color: #ff5a00; font-size: 29px; font-weight: 700; line-height: 1; }
        .payment-pill { background: #27c83a; color: #fff; border-radius: 20px; padding: 3mm 5mm; text-align: center; font-weight: 700; margin: 9mm 0 4mm; font-size: 12px; }
        .payment-box { border: 1.5mm solid #008847; padding: 4mm 5mm; margin-bottom: 7mm; min-height: 22mm; font-size: 10px; line-height: 1.35; }
        .payment-box-content { width: 100%; border-collapse: collapse; }
        .payment-box-text { vertical-align: middle; }
        .payment-icon-cell { width: 34mm; vertical-align: middle; text-align: center; }
        .payment-icon { max-width: 28mm; max-height: 18mm; object-fit: contain; }
        .payment-columns { width: 100%; border-collapse: separate; border-spacing: 4mm 0; margin-bottom: 7mm; }
        .payment-column { width: 50%; border: 1.2mm solid #008847; vertical-align: top; min-height: 32mm; }
        .payment-column-title { background: #27c83a; color: #fff; padding: 2mm; text-align: center; font-weight: 700; font-size: 11px; }
        .payment-column-body { padding: 5mm; font-size: 10px; line-height: 1.35; text-align: center; }
        .payment-column-icon { max-width: 28mm; max-height: 16mm; object-fit: contain; margin-bottom: 3mm; }
        .info-page { padding: 38mm 20mm 22mm; background: #fff; }
        .info-title { color: #ff5a00; font-size: 26px; font-weight: 700; margin-bottom: 8mm; }
        .info-content { border: 1.2mm solid #008847; padding: 6mm; font-size: 11px; line-height: 1.45; }
        .info-table { width: 100%; border-collapse: collapse; border: 1.2mm solid #008847; font-size: 11px; }
        .info-table td { border-bottom: 0.3mm solid #a7d9b8; padding: 4mm; vertical-align: top; }
        .info-table tr:last-child td { border-bottom: 0; }
        .info-table-label { width: 34%; background: #f0fff3; color: #008847; font-weight: 700; }
    </style>
</head>
<body>
    @if ($coverImage)
        <div class="page cover-title-position-{{ $coverTitlePosition }}">
            <img class="full-page-image" src="{{ $coverImage }}">
            <div class="cover-overlay-title">{{ $catalogName }}</div>
            @if ($settings['advisor_name'] || $settings['advisor_phone'] || $settings['advisor_email_1'] || $settings['advisor_email_2'])
                <div class="cover-advisor">
                    @if ($settings['advisor_name'])
                        <span class="cover-advisor-name">{{ $settings['advisor_name'] }}</span>
                    @endif
                    @if ($settings['advisor_phone'] || $advisorEmails->isNotEmpty())
                        <span class="cover-advisor-contact">
                            @if ($settings['advisor_phone'])
                                {{ $settings['advisor_phone'] }}
                            @endif
                            @if ($settings['advisor_phone'] && $advisorEmails->isNotEmpty())
                                |
                            @endif
                            @if ($advisorEmails->isNotEmpty())
                                {{ $advisorEmails->join(' - ') }}
                            @endif
                        </span>
                    @endif
                </div>
            @endif
        </div>
    @else
        <div class="page cover cover-title-position-{{ $coverTitlePosition }}">
            <div class="cover-title">{{ $catalogName }}</div>
            <div class="cover-meta">{{ $categories->map(function ($category) { return $category->getTranslation('name'); })->join(' - ') }}</div>
            @if ($settings['advisor_name'] || $settings['advisor_phone'] || $settings['advisor_email_1'] || $settings['advisor_email_2'])
                <div class="cover-advisor">
                    @if ($settings['advisor_name'])
                        <span class="cover-advisor-name">{{ $settings['advisor_name'] }}</span>
                    @endif
                    @if ($settings['advisor_phone'] || $advisorEmails->isNotEmpty())
                        <span class="cover-advisor-contact">
                            @if ($settings['advisor_phone'])
                                {{ $settings['advisor_phone'] }}
                            @endif
                            @if ($settings['advisor_phone'] && $advisorEmails->isNotEmpty())
                                |
                            @endif
                            @if ($advisorEmails->isNotEmpty())
                                {{ $advisorEmails->join(' - ') }}
                            @endif
                        </span>
                    @endif
                </div>
            @endif
        </div>
    @endif

    @if ($settings['show_payment_page'])
        <div class="page payment-page">
            @if ($paymentImage)
                <img class="page-background" src="{{ $paymentImage }}">
            @endif
            <div class="payment-title">{{ $settings['payment_title'] ?: 'MEDIOS DE PAGO' }}</div>
            <div class="payment-pill">{{ $settings['payment_delivery_title'] }}</div>
            <div class="payment-box">
                <table class="payment-box-content">
                    <tr>
                        <td class="payment-box-text">{!! nl2br(e($settings['payment_bank_info'])) !!}</td>
                        @if ($paymentBankIcon)
                            <td class="payment-icon-cell"><img class="payment-icon" src="{{ $paymentBankIcon }}"></td>
                        @endif
                    </tr>
                </table>
            </div>
            <table class="payment-columns">
                <tr>
                    <td class="payment-column">
                        <div class="payment-column-title">{{ $settings['payment_debit_title'] }}</div>
                        <div class="payment-column-body">
                            @if ($paymentDebitIcon)
                                <img class="payment-column-icon" src="{{ $paymentDebitIcon }}">
                            @endif
                            <div>{!! nl2br(e($settings['payment_debit_info'])) !!}</div>
                        </div>
                    </td>
                    <td class="payment-column">
                        <div class="payment-column-title">{{ $settings['payment_credit_title'] }}</div>
                        <div class="payment-column-body">
                            @if ($paymentCreditIcon)
                                <img class="payment-column-icon" src="{{ $paymentCreditIcon }}">
                            @endif
                            <div>{!! nl2br(e($settings['payment_credit_info'])) !!}</div>
                        </div>
                    </td>
                </tr>
            </table>
            <div class="payment-pill">{{ $settings['payment_cash_title'] }}</div>
            <div class="payment-box" style="text-align: center;">
                @if ($paymentCashIcon)
                    <div><img class="payment-column-icon" src="{{ $paymentCashIcon }}"></div>
                @endif
                <div>{!! nl2br(e($settings['payment_cash_info'])) !!}</div>
            </div>
        </div>
    @endif

    @if ($settings['show_info_page'])
        <div class="page info-page">
            @if ($infoImage)
                <img class="page-background" src="{{ $infoImage }}">
            @endif
            <div class="info-title">{{ $settings['info_page_title'] ?: translate('Information') }}</div>
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
                <div class="info-content"></div>
            @endif
        </div>
    @endif

    @if ($settings['show_page_four'] && $pageFourImage)
        <div class="page"><img class="full-page-image" src="{{ $pageFourImage }}"></div>
    @endif

    @foreach ($productsByCategory as $categoryGroup)
        @foreach ($categoryGroup['letter_groups'] as $letter => $letterProducts)
            @foreach ($letterProducts->chunk(6) as $chunk)
                @php
                    $boxColor = $productBoxColors[$letter] ?? $letterPalette[$letter] ?? '#f36f21';
                    $textColor = $settings['product_text_colors'][$letter] ?? '#ffffff';
                @endphp
                <div class="page">
                    <div class="category-heading" style="color: {{ $boxColor }};">
                        {{ $letter }}
                    </div>
                    <table class="product-table">
                        @foreach ($chunk->values()->chunk(2) as $row)
                            <tr>
                                @foreach ($row as $product)
                                    @php
                                        $image = $pageImage($product->thumbnail_img) ?: $pageImage($product->meta_image) ?: $fallbackImage;
                                        $description = trim(strip_tags($product->getTranslation('description') ?: $product->meta_description ?: ''));
                                        $description = \Illuminate\Support\Str::limit($description, $descriptionLimit);
                                    @endphp
                                    <td class="product-card" style="border-color: {{ $boxColor }};">
                                        <div class="product-banner" style="background: {{ $boxColor }}; color: {{ $textColor }};">{{ $product->getTranslation('name') }}</div>
                                        <div class="product-body">
                                            <table class="product-layout">
                                                <tr>
                                                    <td class="product-image-cell">
                                                        <img class="product-image" src="{{ $image }}">
                                                    </td>
                                                    <td class="product-info-cell {{ $description ? 'with-description' : 'without-description' }}">
                                                        <div class="product-name">{{ $product->getTranslation('name') }}</div>
                                                        @if ($settings['show_prices'])
                                                            <div class="product-price">{{ format_price($product->lowest_price) }}</div>
                                                        @endif
                                                        @if ($product->reference)
                                                            <div class="product-reference">{{ $product->reference }}</div>
                                                        @endif
                                                        @if ($description)
                                                            <div class="product-description">{{ $description }}</div>
                                                        @endif
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </td>
                                @endforeach
                                @if ($row->count() === 1)
                                    <td class="product-card"></td>
                                @endif
                            </tr>
                        @endforeach
                    </table>
                </div>
            @endforeach
            @if ($advertisingByLetter->has($letter))
                @foreach ($advertisingByLetter->get($letter) as $advertisingItem)
                    <div class="page"><img class="full-page-image" src="{{ $advertisingItem['image'] }}"></div>
                @endforeach
            @endif
        @endforeach
    @endforeach
</body>
</html>
