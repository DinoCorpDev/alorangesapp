@extends('backend.layouts.app')

@section('content')
    @php
        $esEdicion = $modo === 'editar';
        $accion = $esEdicion ? route('quotations.update', $cotizacion->id) : route('quotations.store');

        // Las lineas se entregan al JS ya normalizadas para no repetir
        // logica de formato en la vista.
        $lineasJson = collect(old('lineas', $lineas))->map(function ($l) {
            $l = (array) $l;
            return [
                'product_id' => $l['product_id'] ?? null,
                'reference' => $l['reference'] ?? '',
                'name' => $l['name'] ?? '',
                'image_path' => $l['image_path'] ?? null,
                'unit_price' => (float) ($l['unit_price'] ?? 0),
                'quantity' => (float) ($l['quantity'] ?? 1),
                'discount_percent' => (float) ($l['discount_percent'] ?? 0),
                'tax_percent' => (float) ($l['tax_percent'] ?? 0),
            ];
        })->values();
    @endphp

    {{-- Cabecera con el mismo patron `aiz-titlebar` que el resto del admin --}}
    <div class="aiz-titlebar text-left mt-2 mb-3">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="h3 mb-1">
                    {{ $esEdicion ? translate('Cotización') . ' N.º ' . $cotizacion->number : translate('Nueva cotización') }}
                </h1>
                @if (!$esEdicion)
                    <span class="badge badge-inline badge-soft-info fs-12">
                        {{ translate('Se guardará con el número') }} {{ $cotizacion->number }}
                    </span>
                @else
                    <span class="badge badge-inline badge-soft-success fs-12">
                        <i class="las la-check"></i> {{ translate('Guardada') }}
                    </span>
                @endif
            </div>
            <div class="col-md-6 text-md-right">
                <a href="{{ route('quotations.index') }}" class="btn btn-soft-secondary">
                    <i class="las la-arrow-left"></i> {{ translate('Volver') }}
                </a>
                @if ($esEdicion)
                    <a href="{{ route('quotations.pdf', $cotizacion->id) }}" target="_blank" class="btn btn-soft-danger">
                        <i class="las la-file-pdf"></i> {{ translate('Ver PDF') }}
                    </a>
                @endif
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12 mx-auto">

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0 pl-3">
                        @foreach ($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ $accion }}" method="POST" id="form-cotizacion">
                @csrf
                @if ($esEdicion)
                    @method('PUT')
                @endif

                {{-- ---------- Datos del cliente ---------- --}}
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0 h6">
                            <i class="las la-user-tie text-primary mr-1"></i>
                            {{ translate('Datos del cliente') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="form-row">
                            <div class="form-group col-md-8">
                                <label>{{ translate('Nombre o razón social') }} <span class="text-danger">*</span></label>
                                <input type="text" name="customer_name" class="form-control" required
                                    value="{{ old('customer_name', $cotizacion->customer_name) }}"
                                    placeholder="{{ translate('Ej: G 4 MEDICAL S.A.S.') }}">
                            </div>
                            <div class="form-group col-md-4">
                                <label>{{ translate('NIT / Documento') }}</label>
                                <input type="text" name="customer_document" class="form-control"
                                    value="{{ old('customer_document', $cotizacion->customer_document) }}">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <label>{{ translate('Dirección') }}</label>
                                <textarea name="customer_address" rows="2" class="form-control"
                                    placeholder="{{ translate('Sede, calle, ciudad…') }}">{{ old('customer_address', $cotizacion->customer_address) }}</textarea>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label>{{ translate('Teléfono') }}</label>
                                <input type="text" name="customer_phone" class="form-control"
                                    value="{{ old('customer_phone', $cotizacion->customer_phone) }}">
                            </div>
                            <div class="form-group col-md-4">
                                <label>{{ translate('Correo') }}</label>
                                <input type="email" name="customer_email" class="form-control"
                                    value="{{ old('customer_email', $cotizacion->customer_email) }}">
                            </div>
                            <div class="form-group col-md-2">
                                <label>{{ translate('Expedición') }} <span class="text-danger">*</span></label>
                                <input type="date" name="issue_date" class="form-control" required
                                    value="{{ old('issue_date', optional($cotizacion->issue_date)->format('Y-m-d')) }}">
                            </div>
                            <div class="form-group col-md-2">
                                <label>{{ translate('Vencimiento') }}</label>
                                <input type="date" name="expiry_date" class="form-control"
                                    value="{{ old('expiry_date', optional($cotizacion->expiry_date)->format('Y-m-d')) }}">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ---------- Productos ---------- --}}
                <div class="card">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5 class="mb-0 h6">
                            <i class="las la-boxes text-primary mr-1"></i>
                            {{ translate('Productos') }}
                        </h5>
                        <span class="badge badge-inline badge-soft-primary" id="cot-contador">
                            0 {{ translate('ítems') }}
                        </span>
                    </div>
                    <div class="card-body">
                        {{-- Buscador: es la accion mas repetida, asi que va arriba,
                             grande y con el foco puesto al entrar. --}}
                        <div class="cot-buscador mb-3">
                            <i class="las la-search cot-buscador-icono"></i>
                            <input type="text" id="cot-buscar" class="form-control cot-buscador-campo"
                                autocomplete="off"
                                placeholder="{{ translate('Busca un producto por nombre o referencia y pulsa Enter…') }}">
                            <div id="cot-sugerencias" class="cot-sugerencias" hidden></div>
                        </div>

                        <div class="table-responsive">
                            <table class="table aiz-table cot-tabla mb-0">
                                <thead>
                                    <tr>
                                        <th style="width: 48px"></th>
                                        <th style="width: 110px">{{ translate('Referencia') }}</th>
                                        <th>{{ translate('Ítem') }}</th>
                                        <th style="width: 130px">{{ translate('Precio') }}</th>
                                        <th style="width: 95px">{{ translate('Cantidad') }}</th>
                                        <th style="width: 95px">{{ translate('Dcto %') }}</th>
                                        <th style="width: 95px">{{ translate('IVA %') }}</th>
                                        <th class="text-right" style="width: 130px">{{ translate('Total') }}</th>
                                        <th style="width: 44px"></th>
                                    </tr>
                                </thead>
                                <tbody id="cot-lineas"></tbody>
                            </table>
                        </div>

                        <div id="cot-vacio" class="text-center py-5">
                            <i class="las la-box-open text-secondary" style="font-size: 44px; opacity: .4"></i>
                            <div class="mt-2 text-muted">
                                {{ translate('Busca productos arriba para agregarlos') }}
                            </div>
                        </div>

                        <button type="button" class="btn btn-soft-primary btn-sm mt-3" id="cot-linea-libre">
                            <i class="las la-plus-circle"></i> {{ translate('Agregar línea manual') }}
                        </button>
                    </div>
                </div>

                {{-- ---------- Observaciones y totales ---------- --}}
                <div class="row">
                    <div class="col-md-7">
                        <div class="card h-100">
                            <div class="card-header">
                                <h5 class="mb-0 h6">
                                    <i class="las la-comment-dots text-primary mr-1"></i>
                                    {{ translate('Observaciones') }}
                                </h5>
                            </div>
                            <div class="card-body">
                                <textarea name="observations" rows="5" class="form-control"
                                    placeholder="{{ translate('Condiciones, sede de entrega, notas…') }}">{{ old('observations', $cotizacion->observations) }}</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="card h-100">
                            <div class="card-header">
                                <h5 class="mb-0 h6">
                                    <i class="las la-calculator text-primary mr-1"></i>
                                    {{ translate('Resumen') }}
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="cot-total-fila">
                                    <span>{{ translate('Subtotal') }}</span>
                                    <span id="cot-subtotal">$0</span>
                                </div>
                                <div class="cot-total-fila" id="cot-fila-descuento" hidden>
                                    <span>{{ translate('Descuentos') }}</span>
                                    <span id="cot-descuento" class="text-danger">-$0</span>
                                </div>
                                <div id="cot-ivas"></div>
                                <hr>
                                <div class="cot-total-fila cot-total-final">
                                    <span>{{ translate('Total') }}</span>
                                    <span id="cot-total">$0</span>
                                </div>

                                <button type="submit" class="btn btn-primary btn-block mt-4" id="cot-guardar">
                                    <i class="las la-save"></i>
                                    {{ $esEdicion ? translate('Guardar cambios') : translate('Crear cotización') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('style')
    <style>
        .cot-buscador { position: relative; }
        .cot-buscador-icono {
            position: absolute; left: 14px; top: 50%; transform: translateY(-50%);
            font-size: 20px; color: #9aa0a6; pointer-events: none;
        }
        /* Hereda de .form-control del tema; solo se agranda y se deja sitio
           para el icono. */
        .cot-buscador-campo {
            height: 46px; padding-left: 42px; font-size: 15px;
        }
        .cot-sugerencias {
            position: absolute; top: calc(100% + 6px); left: 0; right: 0; z-index: 40;
            background: #fff; border-radius: 10px; box-shadow: 0 10px 32px rgba(0,0,0,.16);
            max-height: 340px; overflow-y: auto;
        }
        /* Rejilla fija: imagen | datos (flexible) | precio | boton.
           Con flex suelto, los nombres largos empujaban el precio y el boton
           fuera de la fila y la lista se veia desalineada. */
        .cot-sug-item {
            display: grid;
            grid-template-columns: 44px minmax(0, 1fr) auto auto;
            align-items: center;
            gap: 12px;
            padding: 9px 12px;
            cursor: pointer;
            border-bottom: 1px solid #f1f3f5;
        }
        .cot-sug-item:last-child { border-bottom: 0; }
        .cot-sug-item:hover, .cot-sug-item.activo { background: #fff4ea; }
        /* Marcador para productos sin foto. El tamano lo pone la utilidad
           size-*px del propio tema, aqui solo el aspecto. */
        .cot-sin-foto {
            display: inline-flex; align-items: center; justify-content: center;
            background: #f4f6f8; color: #c3c8cd; font-size: 18px; flex-shrink: 0;
        }
        .cot-miniatura { background: #f4f6f8; }
        .cot-sug-datos { min-width: 0; display: flex; flex-direction: column; gap: 3px; }
        .cot-sug-nombre {
            font-size: 13px; font-weight: 600; color: #25292e; line-height: 1.3;
            /* Dos lineas como maximo: los nombres de producto aqui son muy
               largos y en una sola linea se cortaban casi siempre. */
            display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .cot-sug-meta { display: flex; flex-wrap: wrap; gap: 6px; }
        .cot-sug-meta > span {
            font-size: 11px; line-height: 1; padding: 3px 7px; border-radius: 999px;
            background: #eef1f4; color: #5c6369; white-space: nowrap;
        }
        .cot-sug-iva { background: #e8f1fb !important; color: #1f6feb !important; }
        .cot-sug-ya { background: #e6f6ec !important; color: #1c7c3f !important; font-weight: 600; }
        .cot-sug-precio {
            font-size: 14px; font-weight: 700; color: #f58634;
            white-space: nowrap; text-align: right;
        }
        .cot-sug-boton { white-space: nowrap; }
        .cot-sug-estado {
            padding: 14px; color: #6b7176; font-size: 13px;
            display: flex; align-items: center; gap: 8px;
        }

        /* En pantallas estrechas el boton pasa a icono para no romper la fila */
        @media (max-width: 767.98px) {
            .cot-sug-item { grid-template-columns: 40px minmax(0, 1fr) auto; gap: 9px; }
            .cot-sug-precio { grid-column: 2; text-align: left; font-size: 13px; }
            .cot-sug-boton { grid-row: 1 / span 2; grid-column: 3; padding: 0 10px; }
            .cot-sug-boton span { display: none; }
        }

        .cot-linea-img {
            width: 34px; height: 34px; object-fit: contain;
            background: #f7f8f9; border-radius: 4px;
        }

        /* Los inputs de la tabla heredan de .form-control del tema; aqui solo
           se compactan para que quepan en la fila. */
        .cot-tabla td { vertical-align: middle; }
        .cot-tabla .form-control {
            height: 34px; font-size: 13px; padding: 3px 9px;
        }
        .cot-linea-total { font-weight: 700; white-space: nowrap; }

        .cot-total-fila {
            display: flex; justify-content: space-between; align-items: center;
            padding: 5px 0; font-size: 14px;
        }
        .cot-total-final { font-size: 19px; font-weight: 800; color: #25292e; }
    </style>
@endsection

@section('script')
    <script>
        (function () {
            var URL_BUSQUEDA = "{{ route('quotations.search_products') }}";

            var lineas = @json($lineasJson);

            var $cuerpo = document.getElementById('cot-lineas');
            var $vacio = document.getElementById('cot-vacio');
            var $contador = document.getElementById('cot-contador');
            var $buscar = document.getElementById('cot-buscar');
            var $sug = document.getElementById('cot-sugerencias');

            var temporizador = null;
            var resultados = [];
            var indiceActivo = -1;
            var peticion = 0;

            function moneda(v) {
                return '$' + (Math.round(v) || 0).toLocaleString('es-CO');
            }

            // ---------- Tabla de lineas ----------
            function pintar() {
                $cuerpo.innerHTML = '';

                lineas.forEach(function (l, i) {
                    // Miniatura con las utilidades del admin (size-*px / img-fit)
                    var miniatura = l.image_path
                        ? '<img class="size-40px img-fit rounded cot-miniatura" src="' + escapar(l.image_path) + '" alt="" onerror="this.replaceWith(marcador(40))">'
                        : marcadorHtml(40);

                    var tr = document.createElement('tr');
                    tr.innerHTML =
                        '<td class="text-center">' + miniatura + '</td>' +
                        '<td><input type="text" class="form-control" data-campo="reference" data-i="' + i + '" value="' + escapar(l.reference) + '"></td>' +
                        '<td><input type="text" class="form-control" data-campo="name" data-i="' + i + '" value="' + escapar(l.name) + '" required></td>' +
                        '<td><input type="number" class="form-control" step="0.01" min="0" data-campo="unit_price" data-i="' + i + '" value="' + l.unit_price + '"></td>' +
                        '<td><input type="number" class="form-control" step="1" min="0.01" data-campo="quantity" data-i="' + i + '" value="' + l.quantity + '"></td>' +
                        '<td><input type="number" class="form-control" step="0.01" min="0" max="100" data-campo="discount_percent" data-i="' + i + '" value="' + l.discount_percent + '"></td>' +
                        '<td><input type="number" class="form-control" step="0.01" min="0" max="100" data-campo="tax_percent" data-i="' + i + '" value="' + l.tax_percent + '"></td>' +
                        '<td class="text-right cot-linea-total">' + moneda(totalLinea(l)) + '</td>' +
                        '<td class="text-center"><button type="button" class="btn btn-soft-danger btn-icon btn-circle btn-sm" data-quitar="' + i + '" title="Quitar"><i class="las la-trash"></i></button></td>';
                    $cuerpo.appendChild(tr);
                });

                // Campos ocultos que se envian al servidor
                lineas.forEach(function (l, i) {
                    ['product_id', 'reference', 'name', 'image_path', 'unit_price', 'quantity', 'discount_percent', 'tax_percent'].forEach(function (c) {
                        var h = document.createElement('input');
                        h.type = 'hidden';
                        h.name = 'lineas[' + i + '][' + c + ']';
                        h.value = l[c] === null || l[c] === undefined ? '' : l[c];
                        $cuerpo.appendChild(h);
                    });
                });

                $vacio.hidden = lineas.length > 0;
                $contador.textContent = lineas.length + ' ítem' + (lineas.length === 1 ? '' : 's');
                totales();
            }

            function escapar(t) {
                return String(t === null || t === undefined ? '' : t).replace(/"/g, '&quot;');
            }

            function totalLinea(l) {
                var bruto = (l.unit_price || 0) * (l.quantity || 0);
                var base = bruto - bruto * ((l.discount_percent || 0) / 100);
                return base + base * ((l.tax_percent || 0) / 100);
            }

            function totales() {
                var subtotal = 0, descuentos = 0, porTarifa = {};

                lineas.forEach(function (l) {
                    var bruto = (l.unit_price || 0) * (l.quantity || 0);
                    var desc = bruto * ((l.discount_percent || 0) / 100);
                    var base = bruto - desc;
                    var iva = base * ((l.tax_percent || 0) / 100);

                    subtotal += base;
                    descuentos += desc;
                    if (l.tax_percent > 0) {
                        porTarifa[l.tax_percent] = (porTarifa[l.tax_percent] || 0) + iva;
                    }
                });

                document.getElementById('cot-subtotal').textContent = moneda(subtotal);
                document.getElementById('cot-fila-descuento').hidden = descuentos <= 0;
                document.getElementById('cot-descuento').textContent = '-' + moneda(descuentos);

                var html = '';
                var totalIva = 0;
                Object.keys(porTarifa).sort(function (a, b) { return a - b; }).forEach(function (t) {
                    totalIva += porTarifa[t];
                    html += '<div class="cot-total-fila"><span>IVA (' + t + '%)</span><span>' + moneda(porTarifa[t]) + '</span></div>';
                });
                document.getElementById('cot-ivas').innerHTML = html;
                document.getElementById('cot-total').textContent = moneda(subtotal + totalIva);
            }

            $cuerpo.addEventListener('input', function (e) {
                var campo = e.target.getAttribute('data-campo');
                if (!campo) return;
                var i = parseInt(e.target.getAttribute('data-i'), 10);
                var v = e.target.value;
                lineas[i][campo] = (campo === 'reference' || campo === 'name') ? v : parseFloat(v || 0);

                // Solo se refresca el total de esa fila y el resumen: repintar
                // toda la tabla haria perder el foco mientras se teclea.
                var fila = e.target.closest('tr');
                if (fila) fila.querySelector('.cot-linea-total').textContent = moneda(totalLinea(lineas[i]));
                var h = document.querySelector('input[name="lineas[' + i + '][' + campo + ']"]');
                if (h) h.value = v;
                totales();
            });

            $cuerpo.addEventListener('click', function (e) {
                var btn = e.target.closest('[data-quitar]');
                if (!btn) return;
                lineas.splice(parseInt(btn.getAttribute('data-quitar'), 10), 1);
                pintar();
            });

            document.getElementById('cot-linea-libre').addEventListener('click', function () {
                lineas.push({ product_id: null, reference: '', name: '', image_path: null, unit_price: 0, quantity: 1, discount_percent: 0, tax_percent: 19 });
                pintar();
                var ultimos = $cuerpo.querySelectorAll('input[data-campo="name"]');
                if (ultimos.length) ultimos[ultimos.length - 1].focus();
            });

            function agregar(p, mantenerAbierto) {
                if (!p) return;

                // Si ya esta en la cotizacion, se suma cantidad en vez de
                // duplicar la linea.
                var existente = lineas.find(function (l) { return l.product_id === p.id; });
                if (existente) {
                    existente.quantity = (existente.quantity || 0) + 1;
                } else {
                    lineas.push({
                        product_id: p.id,
                        reference: p.referencia,
                        name: p.nombre,
                        unit_price: p.precio,
                        quantity: 1,
                        discount_percent: 0,
                        tax_percent: p.impuesto,
                        image_path: p.imagen || null
                    });
                }
                pintar();

                if (mantenerAbierto) {
                    pintarSug(); // refresca la marca de "Ya agregado"
                } else {
                    cerrarSug();
                    $buscar.value = '';
                }
                $buscar.focus();
            }

            // ---------- Buscador ----------
            function cerrarSug() {
                $sug.hidden = true;
                indiceActivo = -1;
            }

            function pintarSug() {
                if (!resultados.length) {
                    $sug.innerHTML = '<div class="cot-sug-estado"><i class="las la-search"></i> No se encontraron productos</div>';
                    $sug.hidden = false;
                    return;
                }
                $sug.innerHTML = resultados.map(function (p, i) {
                    var yaEsta = lineas.some(function (l) { return l.product_id === p.id; });
                    var img = p.imagen
                        ? '<img class="size-44px img-fit rounded" src="' + p.imagen + '" alt="" onerror="this.replaceWith(marcador(44))">'
                        : marcadorHtml(44);

                    return '<div class="cot-sug-item' + (i === indiceActivo ? ' activo' : '') + '" data-i="' + i + '">' +
                        img +
                        '<span class="cot-sug-datos">' +
                            '<span class="cot-sug-nombre" title="' + escapar(p.nombre) + '">' + escapar(p.nombre) + '</span>' +
                            '<span class="cot-sug-meta">' +
                                (p.referencia ? '<span class="cot-sug-ref">Ref. ' + escapar(p.referencia) + '</span>' : '') +
                                (p.impuesto ? '<span class="cot-sug-iva">IVA ' + p.impuesto + '%</span>' : '') +
                                (yaEsta ? '<span class="cot-sug-ya">Ya agregado</span>' : '') +
                            '</span>' +
                        '</span>' +
                        '<span class="cot-sug-precio">' + moneda(p.precio) + '</span>' +
                        '<button type="button" class="btn btn-primary btn-sm cot-sug-boton" data-agregar="' + i + '" title="Agregar a la cotización">' +
                            '<i class="las la-plus"></i> <span>Agregar</span>' +
                        '</button>' +
                        '</div>';
                }).join('');
                $sug.hidden = false;
            }

            // Marcador cuando el producto no tiene foto (o esta rota), para
            // que la fila no quede descuadrada. Usa las mismas utilidades de
            // tamano que el resto del admin.
            function marcadorHtml(px) {
                return '<span class="size-' + px + 'px rounded cot-sin-foto"><i class="las la-image"></i></span>';
            }

            window.marcador = function (px) {
                var s = document.createElement('span');
                s.className = 'size-' + (px || 44) + 'px rounded cot-sin-foto';
                s.innerHTML = '<i class="las la-image"></i>';
                return s;
            };

            $sug.addEventListener('click', function (e) {
                var btn = e.target.closest('[data-agregar]');
                if (btn) {
                    // El boton agrega sin cerrar la lista: asi se pueden
                    // encadenar varios productos de la misma busqueda.
                    agregar(resultados[parseInt(btn.getAttribute('data-agregar'), 10)], true);
                    return;
                }
                var it = e.target.closest('[data-i]');
                if (it) agregar(resultados[parseInt(it.getAttribute('data-i'), 10)]);
            });

            $buscar.addEventListener('input', function () {
                var t = $buscar.value.trim();
                if (temporizador) clearTimeout(temporizador);
                if (t.length < 2) { cerrarSug(); return; }

                $sug.innerHTML = '<div class="cot-sug-estado"><i class="las la-spinner la-spin"></i> Buscando…</div>';
                $sug.hidden = false;

                temporizador = setTimeout(function () {
                    var id = ++peticion;
                    fetch(URL_BUSQUEDA + '?q=' + encodeURIComponent(t), {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    })
                        .then(function (r) { return r.json(); })
                        .then(function (d) {
                            if (id !== peticion) return; // respuesta obsoleta
                            resultados = d.productos || [];
                            indiceActivo = -1;
                            pintarSug();
                        })
                        .catch(function () {
                            if (id === peticion) { resultados = []; pintarSug(); }
                        });
                }, 280);
            });

            $buscar.addEventListener('keydown', function (e) {
                if ($sug.hidden || !resultados.length) return;
                if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    indiceActivo = (indiceActivo + 1) % resultados.length;
                    pintarSug();
                } else if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    indiceActivo = (indiceActivo - 1 + resultados.length) % resultados.length;
                    pintarSug();
                } else if (e.key === 'Enter') {
                    e.preventDefault();
                    agregar(resultados[indiceActivo >= 0 ? indiceActivo : 0]);
                } else if (e.key === 'Escape') {
                    cerrarSug();
                }
            });

            document.addEventListener('click', function (e) {
                if (!e.target.closest('.cot-buscador')) cerrarSug();
            });

            document.getElementById('form-cotizacion').addEventListener('submit', function (e) {
                if (!lineas.length) {
                    e.preventDefault();
                    alert('Agrega al menos un producto a la cotización.');
                }
            });

            pintar();
            $buscar.focus();
        })();
    </script>
@endsection
