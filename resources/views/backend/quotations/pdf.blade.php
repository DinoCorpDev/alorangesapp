{{-- Plantilla del PDF de cotizacion.
     Replica el formato que ya se usa (ver Cotizacion-5819): cabecera del
     emisor a la izquierda y datos de contacto a la derecha, bloque de cliente
     frente a numero y fechas, tabla de items, y pie con observaciones y
     totales con el IVA desglosado por tarifa. --}}
@php
    $moneda = fn($v) => '$' . number_format((float) $v, 0, ',', '.');
@endphp
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: dejavusans, sans-serif;
            font-size: 8.5pt;
            color: #222;
        }

        .cab { width: 100%; }
        .cab td { vertical-align: top; }
        .cab-empresa { font-size: 10pt; font-weight: bold; }
        .cab-nit { font-size: 8.5pt; }
        .cab-contacto { text-align: right; font-size: 8pt; line-height: 1.5; }

        h1.titulo {
            font-size: 15pt;
            font-weight: normal;
            margin: 16px 0 10px;
        }

        .datos { width: 100%; margin-bottom: 12px; }
        .datos td { vertical-align: top; }
        .cliente-nombre { font-weight: bold; text-transform: uppercase; }
        .cliente-linea { line-height: 1.45; }

        .meta { width: 100%; font-size: 8.5pt; }
        .meta td { padding: 2px 0; }
        .meta .etiqueta { color: #444; }
        .meta .valor { text-align: right; font-weight: bold; }

        table.items {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
        }
        table.items th {
            background: #f0f0f0;
            border-bottom: 1px solid #cfcfcf;
            padding: 6px 5px;
            font-size: 8pt;
            text-align: left;
        }
        table.items td {
            border-bottom: 1px solid #ebebeb;
            padding: 5px;
            font-size: 8pt;
        }
        .der { text-align: right; }
        .cen { text-align: center; }

        .pie { width: 100%; margin-top: 14px; }
        .pie td { vertical-align: top; }
        .obs-titulo { font-weight: bold; margin-bottom: 4px; }
        .obs-texto { font-size: 8pt; line-height: 1.5; }

        table.totales { width: 100%; font-size: 9pt; }
        table.totales td { padding: 3px 0; }
        table.totales .et { text-align: right; padding-right: 14px; color: #444; }
        table.totales .vl { text-align: right; font-weight: bold; width: 95px; }
        table.totales tr.final td {
            border-top: 1px solid #cfcfcf;
            padding-top: 7px;
            font-size: 11pt;
        }

        .elaborado {
            margin-top: 34px;
            font-size: 8pt;
            color: #444;
        }
        .firma {
            margin-top: 26px;
            border-top: 1px solid #999;
            width: 210px;
            padding-top: 3px;
        }
    </style>
</head>

<body>
    <table class="cab">
        <tr>
            <td style="width: 55%">
                @if ($empresa['logo'] && is_file($empresa['logo']))
                    <img src="{{ $empresa['logo'] }}" style="height: 30px; margin-bottom: 6px">
                    <br>
                @endif
                <span class="cab-empresa">{{ $empresa['nombre'] }}</span><br>
                <span class="cab-nit">{{ $empresa['nit'] }}</span>
            </td>
            <td class="cab-contacto">
                {{ $empresa['telefono'] }}<br>
                {{ $empresa['web'] }}<br>
                {{ $empresa['email'] }}<br>
                {{ $empresa['direccion'] }}
            </td>
        </tr>
    </table>

    <h1 class="titulo">Cotización</h1>

    <table class="datos">
        <tr>
            <td style="width: 58%">
                <div class="cliente-nombre">{{ $cotizacion->customer_name }}</div>
                @if ($cotizacion->customer_document)
                    <div class="cliente-linea">NIT {{ $cotizacion->customer_document }}</div>
                @endif
                @if ($cotizacion->customer_address)
                    <div class="cliente-linea">{!! nl2br(e($cotizacion->customer_address)) !!}</div>
                @endif
                @if ($cotizacion->customer_phone)
                    <div class="cliente-linea">TEL {{ $cotizacion->customer_phone }}</div>
                @endif
                @if ($cotizacion->customer_email)
                    <div class="cliente-linea">{{ $cotizacion->customer_email }}</div>
                @endif
            </td>
            <td style="width: 42%">
                <table class="meta">
                    <tr>
                        <td class="etiqueta">Cotización No.</td>
                        <td class="valor">{{ $cotizacion->number }}</td>
                    </tr>
                    <tr>
                        <td class="etiqueta">Fecha de expedición</td>
                        <td class="valor">{{ optional($cotizacion->issue_date)->format('d/m/Y') }}</td>
                    </tr>
                    @if ($cotizacion->expiry_date)
                        <tr>
                            <td class="etiqueta">Fecha de vencimiento</td>
                            <td class="valor">{{ $cotizacion->expiry_date->format('d/m/Y') }}</td>
                        </tr>
                    @endif
                </table>
            </td>
        </tr>
    </table>

    <table class="items">
        <thead>
            <tr>
                <th style="width: 42px"></th>
                <th style="width: 62px">Referencia</th>
                <th>Ítem</th>
                <th class="der" style="width: 72px">Precio</th>
                <th class="cen" style="width: 52px">Cantidad</th>
                <th class="cen" style="width: 60px">Descuento</th>
                <th class="cen" style="width: 52px">Impuesto</th>
                <th class="der" style="width: 78px">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($cotizacion->items as $linea)
                <tr>
                    <td class="cen">
                        @php
                            // mPDF necesita una ruta local: la URL guardada se
                            // traduce a public_path. Si el fichero no existe
                            // (hay productos sin foto) se deja la celda vacia.
                            $rutaImagen = null;
                            if ($linea->image_path) {
                                $relativa = ltrim(parse_url($linea->image_path, PHP_URL_PATH) ?: '', '/');
                                $relativa = preg_replace('#^public/#', '', $relativa);
                                $candidata = public_path($relativa);
                                if (is_file($candidata)) {
                                    $rutaImagen = $candidata;
                                }
                            }
                        @endphp
                        @if ($rutaImagen)
                            {{-- mPDF ignora object-fit y las clases CSS sobre <img>: sin el
                                 atributo width la foto salia a tamano natural (500px). --}}
                            <img src="{{ $rutaImagen }}" width="26">
                        @endif
                    </td>
                    <td>{{ $linea->reference ?: '—' }}</td>
                    <td>{{ $linea->name }}</td>
                    <td class="der">{{ $moneda($linea->unit_price) }}</td>
                    <td class="cen">{{ rtrim(rtrim(number_format($linea->quantity, 2, '.', ''), '0'), '.') }}</td>
                    <td class="cen">{{ number_format($linea->discount_percent, 2) }}%</td>
                    <td class="cen">{{ rtrim(rtrim(number_format($linea->tax_percent, 2, '.', ''), '0'), '.') }}%</td>
                    <td class="der">{{ $moneda($linea->line_total) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="pie">
        <tr>
            <td style="width: 55%">
                @if ($cotizacion->observations)
                    <div class="obs-titulo">Observaciones</div>
                    <div class="obs-texto">{!! nl2br(e($cotizacion->observations)) !!}</div>
                @endif
            </td>
            <td style="width: 45%">
                <table class="totales">
                    <tr>
                        <td class="et">Subtotal</td>
                        <td class="vl">{{ $moneda($cotizacion->subtotal) }}</td>
                    </tr>
                    @if ($cotizacion->discount_total > 0)
                        <tr>
                            <td class="et">Descuentos</td>
                            <td class="vl">-{{ $moneda($cotizacion->discount_total) }}</td>
                        </tr>
                    @endif
                    @foreach (($cotizacion->tax_breakdown ?? []) as $tarifa => $importe)
                        <tr>
                            <td class="et">IVA ({{ number_format((float) $tarifa, 2) }}%)</td>
                            <td class="vl">{{ $moneda($importe) }}</td>
                        </tr>
                    @endforeach
                    <tr class="final">
                        <td class="et">Total</td>
                        <td class="vl">{{ $moneda($cotizacion->total) }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <div class="elaborado">
        ELABORADO POR
        <div class="firma">{{ $cotizacion->created_by_name ?: '' }}</div>
    </div>
</body>

</html>
