<div style="margin-left:auto;margin-right:auto;">
    <style>
        @import url('https://fonts.googleapis.com/css?family=Dejavu+Sans:400,700');
        * {
            margin: 0;
            padding: 0;
            line-height: 1.5;
            font-family: 'Dejavu Sans', sans-serif;
            color: #333542;
        }
        body {
            font-size: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 9px 10px;
            border-bottom: 1px solid #eceff4;
        }
        th {
            background: #eceff4;
            text-align: left;
        }
        .text-right {
            text-align: right;
        }
        .muted {
            color: #878f9c;
        }
        .title {
            font-size: 22px;
            font-weight: bold;
        }
        .category-title {
            margin-top: 18px;
            padding: 8px 10px;
            background: #dfe7f3;
            font-size: 15px;
            font-weight: bold;
        }
        .letter-row td {
            background: #f3f5f9;
            font-weight: bold;
        }
    </style>

    <div style="background: #eceff4; padding: 20px;">
        <table>
            <tr>
                <td>
                    <div class="title">{{ $catalogName }}</div>
                    <div class="muted">
                        {{ translate('Categories') }}:
                        {{ $categories->map(function ($category) { return $category->getTranslation('name'); })->join(', ') }}
                    </div>
                </td>
                <td class="text-right">
                    {{ now()->format('Y-m-d') }}
                </td>
            </tr>
        </table>
    </div>

    <div style="padding: 20px;">
        @foreach ($productsByCategory as $categoryGroup)
            <div class="category-title">{{ $categoryGroup['category']->getTranslation('name') }}</div>
            <table>
                <thead>
                    <tr>
                        <th width="10%">#</th>
                        <th width="65%">{{ translate('Product Name') }}</th>
                        <th width="25%" class="text-right">{{ translate('Price') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @php $productNumber = 1; @endphp
                    @foreach ($categoryGroup['letter_groups'] as $letter => $letterProducts)
                        <tr class="letter-row">
                            <td colspan="3">{{ $letter }}</td>
                        </tr>
                        @foreach ($letterProducts as $product)
                            <tr>
                                <td>{{ $productNumber }}</td>
                                <td>{{ $product->getTranslation('name') }}</td>
                                <td class="text-right">{{ format_price($product->lowest_price) }}</td>
                            </tr>
                            @php $productNumber++; @endphp
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        @endforeach
    </div>
</div>
