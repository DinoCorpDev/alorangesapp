@php
    $image = $productImage($product->thumbnail_img)
        ?: $productImage($product->meta_image)
        ?: $fallbackImage;
    $rawName = trim($product->getTranslation('name'));
    $bannerName = \Illuminate\Support\Str::limit($rawName, $productBannerLimit);
@endphp

<table class="product-card" style="border-color:{{ $boxColor }};">
    <tr>
        <td class="product-head" style="background-color:{{ $boxColor }}; color:{{ $textColor }}; font-family:{{ $cssFont($settings['product_title_font_family']) }}, sans-serif; font-size:{{ $safeTitleFontSize }}px;">
            {{ $bannerName }}
        </td>
    </tr>
    <tr>
        <td class="product-media">
            @if ($image)
                <img class="product-img" src="{{ $image }}" alt="">
            @endif
        </td>
    </tr>
    <tr>
        <td class="product-info">
            <table class="product-detail-table">
                @if ($settings['show_prices'])
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
