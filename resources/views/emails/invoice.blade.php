<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laravel</title>
    <meta http-equiv="Content-Type" content="text/html;" />
    <meta charset="UTF-8">
    <style media="all">
        @font-face {
            font-family: 'Roboto';
            src: url("{{ static_asset('fonts/Roboto-Regular.ttf') }}") format("truetype");
            font-weight: normal;
            font-style: normal;
        }

        body {
            font-size: 0.75rem;
            font-family: 'Roboto';
            font-weight: normal;
            direction: ltr;
            text-align: left;
            padding: 0;
            margin: 0;
            color: #232323;
        }

        table {
            width: 100%;
        }

        table th {
            font-weight: normal;
        }

        table.padding th {
            padding: 0 .8rem;
        }

        table.padding td {
            padding: .8rem;
        }

        table.sm-padding td {
            padding: .5rem .7rem;
        }

        table.lg-padding td {
            padding: 1rem 1.2rem;
        }

        .border-bottom td,
        .border-bottom th {
            border-bottom: 1px solid #eceff4;
        }

        .text-left {
            text-align: left;
        }

        .text-right {
            text-align: right;
        }

        .bold {
            font-weight: bold
        }

    </style>
</head>

<body>
    <div style="max-width: 650px;padding:20px;margin:0 auto">
        <div style="padding:0px 19px;">
            <table>
                <thead>
                    <tr>
                        <th width="50%"></th>
                        <th width="50%"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <table>
                            <tbody>
                                <tr>
                                    <td class="img m-center"
                                        style="font-size:0pt; line-height:0pt; text-align:left; padding-bottom: 20px;">
                                            <a href="{{ env('APP_URL') }}">
                                                <img style="max-width: 150px; height: auto;" src="{{url('https://dinolabs.dev/wp-content/uploads/2024/10/Alorangeslogo.png')}}" />
                                            </a>
                                    </td>        
                                </tr>
                                    <tr>
                                        <td class="">Email:
                                            info@aloranges.com</td>
                                    </tr>
                                    <tr>
                                        <td class="">Telefono:
                                            +57 3244364040</td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                        <td>
                            <table class="text-right">
                            <tbody>
                                    <tr>
                                        <td class="">
                                            <span class=" ">Factura Código:</span>
                                            <span class="bold">{{ $combined_order->code }}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="">
                                            <span class=" ">Día de Orden:</span>
                                            <span
                                                class="bold">{{ $combined_order->created_at->format('d.m.Y') }}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <span class=" ">Tipo de entrega:</span>
                                            <span class="bold"
                                                style="text-transform: capitalize">{{ translate($combined_order->orders->first()->delivery_type) }}</span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div style="margin:8px 8px 15px 8px; clear:both">
            <div style="padding:10px 14px; width:45%;float:right">
                <table class="text-right">
                    <tbody>
                        @php
                            $shipping_address = json_decode($combined_order->shipping_address);
                        @endphp
                        <tr>
                            <td class="bold">Dirección de envío:</td>
                        </tr>
                        <tr>
                            <td class="">{{ $shipping_address->address }},
                                {{ $shipping_address->postal_code }}</td>
                        </tr>
                        <tr>
                            <td class="">{{ $shipping_address->city }},
                                {{ $shipping_address->state }}, {{ $shipping_address->country }}</td>
                        </tr>
                        <tr>
                            <td class="">{{ translate('Phone') }}: {{ $shipping_address->phone }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div style="margin:0 8px;padding:0 7px">
            <table class="padding">
                <thead>
                    <tr style="background-color: #F58634;">
                        <td width="5%" class="text-left bold">{{ translate('S/L') }}</td>
                        <td width="45%" class="text-left bold">Nombre del producto</td>
                        <td width="13%" class="text-left bold">Cantidad</td>
                        <td width="15%" class="text-left bold">Precio Unitario</td>
                        <td width="10%" class="text-left bold">Impuesto Unitario</td>
                        <td width="12%" class="text-right bold">Total</td>
                    </tr>
                </thead>
            </table>
        </div>
        <div style="margin:8px;">
            <table class="lg-padding" style="border-collapse: collapse">
                <tr>
                    <th width="50%" class="text-left"></th>
                    <th width="13%" class="text-left"></th>
                    <th width="15%" class="text-left"></th>
                    <th width="9%" class="text-left"></th>
                    <th width="14%" class="text-right"></th>
                </tr>
                <tbody class="strong">
                    
                    @php
                        $totalTax = 0;
                        $total = 0;
                    @endphp
                    @foreach ($combined_order->orders as $order)
                        @foreach ($order->orderDetails as $key => $orderDetail)
                            @if ($orderDetail->product != null)
                                <tr>
                                    <td style="border-bottom:1px solid #DEDEDE;">
                                        <span style="display: block">{{ $orderDetail->product->name }}</span>
                                        @if ($orderDetail->variation && $orderDetail->variation->combinations->count() > 0)
                                            @foreach ($orderDetail->variation->combinations as $combination)
                                                <span style="margin-right:10px">
                                                    <span
                                                        class="">{{ $combination->attribute->getTranslation('name') }}</span>:
                                                    <span>{{ $combination->attribute_value->getTranslation('name') }}</span>
                                                </span>
                                            @endforeach
                                        @endif
                                    </td>
                                    <td class="" style="border-bottom:1px solid #DEDEDE;">
                                        {{ $orderDetail->quantity }}</td>
                                    <td class="" style="border-bottom:1px solid #DEDEDE;">
                                        {{ format_price($orderDetail->price) }}</td>
                                    <td class="" style="border-bottom:1px solid #DEDEDE;">
                                        {{ format_price($orderDetail->tax) }}</td>
                                    <td class="text-right bold" style="border-bottom:1px solid #DEDEDE;padding-right:20px;">
                                        {{ format_price($orderDetail->total) }}</td>
                                </tr>
                            @endif
                            @php
                                $totalTax = $orderDetail->tax * $orderDetail->quantity;
                                $total = $orderDetail->total;
                            @endphp
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>

        <div style="margin:15px 8px;clear:both">
            <div style="float: left; width:43%;padding:14px 20px;">
            </div>
            <div style="float: right; width:43%;padding:14px 20px; border:1px solid #DEDEDE;border-radius:3px;">
                <table class="text-right sm-padding" style="border-collapse:collapse">
                    <tbody>
                        <tr>
                            <td class="text-left" style="border-bottom:1px dotted #B8B8B8">
                                Subtotal</td>
                            <td class="bold" style="border-bottom:1px dotted #B8B8B8">
                                {{ format_price($combined_order->grand_total) }}</td>
                        </tr>
                        <tr class="">
                            <td class="text-left" style="border-bottom:1px dotted #B8B8B8">
                                Impuesto total</td>
                            <td class="bold" style="border-bottom:1px dotted #B8B8B8">
                                {{ format_price($totalTax) }}</td>
                        </tr>
                        <tr>
                            <td class="text-left" style="border-bottom:1px dotted #B8B8B8">
                                Costo de envío</td>
                            <td class="bold" style="border-bottom:1px dotted #B8B8B8">
                                {{ format_price($combined_order->orders->sum('shipping_cost')) }}</td>
                        </tr>
                        <tr class="">
                            <td class="text-left" style="border-bottom:1px solid #DEDEDE">
                                Cupon de descuento</td>
                            <td class="bold" style="border-bottom:1px solid #DEDEDE">
                                {{ format_price($combined_order->orders->sum('coupon_discount')) }}</td>
                        </tr>
                        <tr style="background-color: #F58634;">
                            <td class="text-left bold">Total de la compra</td>
                            <td class="bold">{{ format_price($combined_order->grand_total) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</body>

</html>
