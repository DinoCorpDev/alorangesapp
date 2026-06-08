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
    </style>

    <div style="background: #eceff4; padding: 20px;">
        <table>
            <tr>
                <td>
                    <div class="title">{{ $catalogName }}</div>
                    <div class="muted">{{ translate('Category') }}: {{ $category->getTranslation('name') }}</div>
                </td>
                <td class="text-right">
                    {{ now()->format('Y-m-d') }}
                </td>
            </tr>
        </table>
    </div>

    <div style="padding: 20px;">
        <table>
            <thead>
                <tr>
                    <th width="10%">#</th>
                    <th width="65%">{{ translate('Product Name') }}</th>
                    <th width="25%" class="text-right">{{ translate('Price') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($products as $key => $product)
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>{{ $product->getTranslation('name') }}</td>
                        <td class="text-right">{{ format_price($product->lowest_price) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
