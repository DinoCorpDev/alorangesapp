@extends('backend.layouts.app')

@section('content')
    @php
        $coverImageRows = old('cover_category_ids')
            ? collect(old('cover_category_ids'))->map(function ($categoryId, $index) {
                return [
                    'category_id' => $categoryId,
                    'image' => old('cover_category_images.' . $index),
                ];
            })->values()->all()
            : ($settings['cover_category_images'] ?? []);

        if (empty($coverImageRows) && ! empty($settings['cover_image'])) {
            $coverImageRows = [[
                'category_id' => '',
                'image' => $settings['cover_image'],
            ]];
        }

        if (empty($coverImageRows)) {
            $coverImageRows = [['category_id' => '', 'image' => '']];
        }

        // Migracion: las "Paginas extra" (imagen unica con posicion global) se fusionaron
        // dentro de "Paginas adicionales" (tabla con posicion por fila). Estas imagenes legacy
        // se precargan como filas de la tabla la primera vez que se abre esta pantalla.
        $legacyExtraPageImages = $settings['extra_page_images'] ?? [];

        if (empty($legacyExtraPageImages) && ! empty($settings['page_four_image'])) {
            $legacyExtraPageImages = [$settings['page_four_image']];
        }

        $additionalPageRows = old('additional_page_images')
            ? collect(old('additional_page_images'))->map(function ($image, $index) {
                return [
                    'image' => $image,
                    'position' => old('additional_page_positions.' . $index, 'start'),
                ];
            })->values()->all()
            : collect($settings['additional_pages'] ?? [])->values()->all();

        if (! old('additional_page_images') && ! empty($legacyExtraPageImages)) {
            $legacyPosition = ($settings['extra_pages_position'] ?? 'start') === 'end' ? 'end' : 'start';
            $legacyRows = collect($legacyExtraPageImages)->filter()->map(function ($image) use ($legacyPosition) {
                return ['image' => $image, 'position' => $legacyPosition];
            })->values()->all();

            $additionalPageRows = array_merge($additionalPageRows, $legacyRows);
        }

        if (empty($additionalPageRows)) {
            $additionalPageRows = [['image' => '', 'position' => 'start']];
        }

        $additionalPagePositionLabels = [
            'start' => 'Despues de pago e informacion',
            'end' => 'Final (antes de la ultima pagina)',
        ];

        $fullPageImageHint = 'Tamano recomendado: 2550 x 3300 px - carta vertical';

        // Fuentes con archivo TTF/OTF realmente incluido en mPDF (vendor/mpdf/mpdf/ttfonts).
        // No se listan "Arial", "Georgia", etc. porque mPDF no trae esos archivos: los sustituye
        // en silencio por una de estas mismas fuentes, dando la falsa impresion de que no cambian.
        $fontFamilies = ['DejaVu Sans', 'DejaVu Sans Condensed', 'DejaVu Serif', 'DejaVu Serif Condensed', 'DejaVu Sans Mono', 'FreeSans', 'FreeSerif', 'FreeMono'];
        $typographyFields = [
            ['label' => 'Titulo del producto', 'family' => 'product_title_font_family', 'size' => 'product_title_font_size', 'default_size' => 12],
            ['label' => 'Descripcion', 'family' => 'product_description_font_family', 'size' => 'product_description_font_size', 'default_size' => 10],
            ['label' => 'Precio', 'family' => 'product_price_font_family', 'size' => 'product_price_font_size', 'default_size' => 16],
            ['label' => 'Referencia', 'family' => 'product_reference_font_family', 'size' => 'product_reference_font_size', 'default_size' => 12],
        ];
    @endphp

    <style>
        .catalog-config-shell .card { border: 1px solid #e5e7eb; box-shadow: 0 6px 18px rgba(15, 23, 42, 0.04); }
        .catalog-config-shell .config-tabs { border: 0; gap: 6px; }
        .catalog-config-shell .config-tabs .nav-link {
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            color: #475569;
            font-weight: 600;
            padding: 10px 14px;
            background: #fff;
        }
        .catalog-config-shell .config-tabs .nav-link.active {
            color: #fff;
            background: #f36f21;
            border-color: #f36f21;
        }
        .config-section-title { font-size: 16px; font-weight: 700; color: #1f2937; margin: 0; }
        .config-section-subtitle { color: #64748b; margin: 4px 0 0; }
        .config-panel {
            background: #f8fafc;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 16px;
            margin-bottom: 16px;
        }
        .config-panel-white {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 16px;
            margin-bottom: 16px;
        }
        .config-toggle-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 14px;
        }
        .config-toggle-row h6 { margin: 0; font-weight: 700; color: #1f2937; }
        .config-toggle-row p { margin: 2px 0 0; color: #64748b; }
        .config-sticky-actions {
            position: sticky;
            bottom: 0;
            z-index: 3;
            background: rgba(255, 255, 255, 0.94);
            border-top: 1px solid #e5e7eb;
            padding: 12px 0;
        }
        .letter-color-cell {
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 10px;
            background: #fff;
            margin-bottom: 10px;
        }
        .letter-color-cell label { font-weight: 700; color: #1f2937; }
        .letter-color-cell input[type="color"] { height: 34px; padding: 3px; }
        .typography-table td, .typography-table th { vertical-align: middle; }
        .compact-uploader .file-preview { margin-bottom: 0; }
        @media (max-width: 767.98px) {
            .catalog-config-shell .config-tabs .nav-item { width: 100%; }
            .catalog-config-shell .config-tabs .nav-link { width: 100%; }
            .config-toggle-row { display: block; }
            .config-toggle-row .aiz-checkbox { margin-top: 10px; }
        }
    </style>

    <div class="catalog-config-shell">
        <div class="aiz-titlebar text-left mt-2 mb-3">
            <div class="row align-items-center">
                <div class="col-md-7">
                    <h1 class="h3">Configuracion del catalogo</h1>
                    <p class="mb-0 text-muted">
                        {{ $catalog ? $catalog['name'] : 'Configuracion base para nuevos catalogos' }}
                    </p>
                </div>
                <div class="col-md-5 text-md-right mt-3 mt-md-0">
                    <a href="{{ route('product_catalogs.index') }}" class="btn btn-soft-secondary">
                        <i class="las la-arrow-left"></i>
                        Volver a catalogos PDF
                    </a>
                </div>
            </div>
        </div>

        <form action="{{ $action }}" method="POST" id="catalog-configuration-form">
            @csrf
            @if ($method === 'PUT')
                @method('PUT')
            @endif

            <div class="card">
                <div class="card-body">
                    <ul class="nav nav-tabs config-tabs mb-4" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-toggle="tab" href="#config-pages" role="tab">
                                <i class="las la-file-alt"></i> Paginas
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#config-cover-images" role="tab">
                                <i class="las la-image"></i> Portadas
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#config-products" role="tab">
                                <i class="las la-box"></i> Productos
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#config-style" role="tab">
                                <i class="las la-palette"></i> Estilo
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="config-pages" role="tabpanel">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="config-panel">
                                        <div class="config-toggle-row">
                                            <div>
                                                <h6>Pagina de medios de pago</h6>
                                                <p>Se mostrara una sola imagen a pagina completa.</p>
                                            </div>
                                            <label class="aiz-checkbox mb-0">
                                                <input type="checkbox" name="show_payment_page" value="1" @if (old('show_payment_page', $settings['show_payment_page'] ?? true)) checked @endif>
                                                <span class="aiz-square-check"></span>
                                                <span>Mostrar</span>
                                            </label>
                                        </div>
                                        <div class="form-group mb-2 compact-uploader">
                                            <label>Imagen de medios de pago</label>
                                            <div class="input-group" data-toggle="aizuploader" data-type="image">
                                                <div class="input-group-prepend">
                                                    <div class="input-group-text bg-soft-secondary font-weight-medium">Buscar</div>
                                                </div>
                                                <div class="form-control file-amount">Elegir archivo</div>
                                                <input type="hidden" name="payment_page_image" class="selected-files" value="{{ old('payment_page_image', $settings['payment_page_image'] ?? '') }}">
                                            </div>
                                            <div class="file-preview box sm"></div>
                                            <small class="text-muted d-block mt-1">{{ $fullPageImageHint }}</small>
                                        </div>
                                        <div class="form-group mb-0">
                                            <label>Posicion en el catalogo</label>
                                            <select class="form-control aiz-selectpicker" name="payment_page_position">
                                                <option value="start" @if (old('payment_page_position', $settings['payment_page_position'] ?? 'start') === 'start') selected @endif>Inicio (despues de la portada)</option>
                                                <option value="end" @if (old('payment_page_position', $settings['payment_page_position'] ?? 'start') === 'end') selected @endif>Final (antes de la ultima pagina)</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="config-panel">
                                        <div class="config-toggle-row">
                                            <div>
                                                <h6>Pagina de informacion</h6>
                                                <p>Se mostrara una sola imagen a pagina completa.</p>
                                            </div>
                                            <label class="aiz-checkbox mb-0">
                                                <input type="checkbox" name="show_info_page" value="1" @if (old('show_info_page', $settings['show_info_page'] ?? true)) checked @endif>
                                                <span class="aiz-square-check"></span>
                                                <span>Mostrar</span>
                                            </label>
                                        </div>
                                        <div class="form-group mb-2 compact-uploader">
                                            <label>Imagen de informacion</label>
                                            <div class="input-group" data-toggle="aizuploader" data-type="image">
                                                <div class="input-group-prepend">
                                                    <div class="input-group-text bg-soft-secondary font-weight-medium">Buscar</div>
                                                </div>
                                                <div class="form-control file-amount">Elegir archivo</div>
                                                <input type="hidden" name="info_page_image" class="selected-files" value="{{ old('info_page_image', $settings['info_page_image'] ?? '') }}">
                                            </div>
                                            <div class="file-preview box sm"></div>
                                            <small class="text-muted d-block mt-1">{{ $fullPageImageHint }}</small>
                                        </div>
                                        <div class="form-group mb-0">
                                            <label>Posicion en el catalogo</label>
                                            <select class="form-control aiz-selectpicker" name="info_page_position">
                                                <option value="start" @if (old('info_page_position', $settings['info_page_position'] ?? 'start') === 'start') selected @endif>Inicio (despues de la portada)</option>
                                                <option value="end" @if (old('info_page_position', $settings['info_page_position'] ?? 'start') === 'end') selected @endif>Final (antes de la ultima pagina)</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-12">
                                    <div class="config-panel">
                                        <div class="d-flex align-items-center justify-content-between mb-3">
                                            <div>
                                                <h6 class="mb-1">Paginas adicionales</h6>
                                                <p class="mb-0 text-muted">Agrega paginas extra y elige donde se ubica cada una dentro del catalogo.</p>
                                            </div>
                                            <button type="button" class="btn btn-soft-primary btn-sm" id="add-additional-page-row">
                                                <i class="las la-plus"></i>
                                                Agregar pagina
                                            </button>
                                        </div>
                                        <div class="table-responsive">
                                            <table class="table table-bordered mb-0" id="additional-pages-table">
                                                <thead>
                                                    <tr>
                                                        <th>Imagen a pagina completa</th>
                                                        <th width="260">Ubicacion</th>
                                                        <th width="80" class="text-center">Opciones</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($additionalPageRows as $additionalPageRow)
                                                        <tr class="additional-page-row">
                                                            <td>
                                                                <div class="input-group" data-toggle="aizuploader" data-type="image">
                                                                    <div class="input-group-prepend">
                                                                        <div class="input-group-text bg-soft-secondary font-weight-medium">Buscar</div>
                                                                    </div>
                                                                    <div class="form-control file-amount">Elegir archivo</div>
                                                                    <input type="hidden" name="additional_page_images[]" class="selected-files" value="{{ $additionalPageRow['image'] ?? '' }}">
                                                                </div>
                                                                <div class="file-preview box sm"></div>
                                                                <small class="text-muted d-block mt-1">{{ $fullPageImageHint }}</small>
                                                            </td>
                                                            <td>
                                                                <select class="form-control aiz-selectpicker" name="additional_page_positions[]">
                                                                    @foreach ($additionalPagePositionLabels as $positionValue => $positionLabel)
                                                                        <option value="{{ $positionValue }}" @if (($additionalPageRow['position'] ?? 'start') === $positionValue) selected @endif>{{ $positionLabel }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </td>
                                                            <td class="text-center">
                                                                <button type="button" class="btn btn-soft-danger btn-icon btn-circle btn-sm remove-additional-page-row" title="Eliminar">
                                                                    <i class="las la-trash"></i>
                                                                </button>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-12">
                                    <div class="config-panel mb-0">
                                        <div class="config-toggle-row mb-0">
                                            <div>
                                                <h6>Precios de productos</h6>
                                                <p>Activa o desactiva los precios en las tarjetas.</p>
                                            </div>
                                            <label class="aiz-checkbox mb-0">
                                                <input type="checkbox" name="show_prices" value="1" @if (old('show_prices', $settings['show_prices'] ?? true)) checked @endif>
                                                <span class="aiz-square-check"></span>
                                                <span>Mostrar</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="config-cover-images" role="tabpanel">
                            <div class="row">
                                <div class="col-lg-8">
                                    <div class="config-panel-white mb-lg-0">
                                        <div class="d-flex align-items-center justify-content-between mb-3">
                                            <div>
                                                <h5 class="config-section-title">Portadas por categoria</h5>
                                                <p class="config-section-subtitle">Estas imagenes estaran disponibles al crear o editar un catalogo PDF.</p>
                                            </div>
                                            <button type="button" class="btn btn-soft-primary btn-sm" id="add-cover-image-row">
                                                <i class="las la-plus"></i>
                                                Agregar portada
                                            </button>
                                        </div>
                                        <div class="table-responsive">
                                            <table class="table table-bordered mb-0" id="cover-images-table">
                                                <thead>
                                                    <tr>
                                                        <th width="35%">Categoria</th>
                                                        <th>Primera imagen del catalogo</th>
                                                        <th width="80" class="text-center">Opciones</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($coverImageRows as $coverImageRow)
                                                        <tr class="cover-image-row">
                                                            <td>
                                                                <select class="form-control aiz-selectpicker" name="cover_category_ids[]" data-live-search="true">
                                                                    <option value="">Selecciona una categoria</option>
                                                                    @foreach ($categories as $category)
                                                                        <option value="{{ $category->id }}" @if ((string) ($coverImageRow['category_id'] ?? '') === (string) $category->id) selected @endif>{{ $category->getTranslation('name') }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <div class="input-group" data-toggle="aizuploader" data-type="image">
                                                                    <div class="input-group-prepend">
                                                                        <div class="input-group-text bg-soft-secondary font-weight-medium">Buscar</div>
                                                                    </div>
                                                                    <div class="form-control file-amount">Elegir archivo</div>
                                                                    <input type="hidden" name="cover_category_images[]" class="selected-files" value="{{ $coverImageRow['image'] ?? '' }}">
                                                                </div>
                                                                <div class="file-preview box sm"></div>
                                                                <small class="text-muted d-block mt-1">{{ $fullPageImageHint }}</small>
                                                            </td>
                                                            <td class="text-center">
                                                                <button type="button" class="btn btn-soft-danger btn-icon btn-circle btn-sm remove-cover-image-row" title="Eliminar">
                                                                    <i class="las la-trash"></i>
                                                                </button>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-4">
                                    <div class="config-panel-white mb-0">
                                        <h5 class="config-section-title">Ultima pagina del catalogo</h5>
                                        <p class="config-section-subtitle">Imagen opcional a pagina completa al final del PDF.</p>
                                        <div class="form-group mb-0 compact-uploader">
                                            <label>Imagen final</label>
                                            <div class="input-group" data-toggle="aizuploader" data-type="image">
                                                <div class="input-group-prepend">
                                                    <div class="input-group-text bg-soft-secondary font-weight-medium">Buscar</div>
                                                </div>
                                                <div class="form-control file-amount">Elegir archivo</div>
                                                <input type="hidden" name="final_page_image" class="selected-files" value="{{ old('final_page_image', $settings['final_page_image'] ?? '') }}">
                                            </div>
                                            <div class="file-preview box sm"></div>
                                            <small class="text-muted d-block mt-1">{{ $fullPageImageHint }}</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="config-products" role="tabpanel">
                            <div class="config-panel-white">
                                <h5 class="config-section-title">Distribucion de productos</h5>
                                <div class="row gutters-10 mt-3">
                                    <div class="col-lg-4">
                                        <div class="form-group mb-lg-0">
                                            <label>Limite de caracteres de descripcion</label>
                                            <input type="number" class="form-control" name="description_limit" min="40" max="220" value="{{ old('description_limit', $settings['description_limit'] ?? 90) }}">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="config-panel-white mb-0">
                                <h5 class="config-section-title">Tipografia de productos</h5>
                                <div class="table-responsive mt-3">
                                    <table class="table table-bordered typography-table mb-0">
                                        <thead>
                                            <tr>
                                                <th>Elemento</th>
                                                <th width="260">Tipo de letra</th>
                                                <th width="160">Tamano</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($typographyFields as $field)
                                                <tr>
                                                    <td class="fw-600">{{ $field['label'] }}</td>
                                                    <td>
                                                        <select class="form-control aiz-selectpicker" name="{{ $field['family'] }}" data-live-search="true">
                                                            @foreach ($fontFamilies as $fontFamily)
                                                                <option value="{{ $fontFamily }}" @if (old($field['family'], $settings[$field['family']] ?? 'DejaVu Sans') === $fontFamily) selected @endif>{{ $fontFamily }}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <input type="number" class="form-control" name="{{ $field['size'] }}" min="7" max="30" value="{{ old($field['size'], $settings[$field['size']] ?? $field['default_size']) }}">
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="config-style" role="tabpanel">
                            <div class="config-panel">
                                <div class="config-toggle-row mb-0">
                                    <div>
                                        <h6>Navegador alfabético interactivo</h6>
                                        <p>Agrega el abecedario navegable en el encabezado de cada página de productos.</p>
                                    </div>
                                    <label class="aiz-checkbox mb-0">
                                        <input type="checkbox" name="show_alphabetic_navigator" value="1" @if (old('show_alphabetic_navigator', $settings['show_alphabetic_navigator'] ?? true)) checked @endif>
                                        <span class="aiz-square-check"></span>
                                        <span>Incluir</span>
                                    </label>
                                </div>
                                <small class="text-muted d-block mt-2">Letras disponibles en verde, sección actual en naranja y letras sin productos en gris. Los enlaces llevan a la primera página de cada letra.</small>
                            </div>

                            <div class="config-panel">
                                <div class="form-group mb-0">
                                    <label class="fw-700">Posición de la letra grande sin navegador</label>
                                    <p class="text-muted mb-2">Esta opción se usa cuando desactivas el navegador alfabético. Para PDF en dispositivos se recomienda <strong>Derecha</strong>. Para impresión a doble cara selecciona <strong>Impresión alternada</strong>: páginas impares a la derecha y páginas pares a la izquierda, siempre hacia el borde exterior.</p>
                                    <select name="standalone_letter_position" class="form-control aiz-selectpicker">
                                        <option value="right" @if (old('standalone_letter_position', $settings['standalone_letter_position'] ?? 'right') === 'right') selected @endif>Derecha — PDF para dispositivos</option>
                                        <option value="alternate_outer" @if (old('standalone_letter_position', $settings['standalone_letter_position'] ?? 'right') === 'alternate_outer') selected @endif>Impresión alternada — impares derecha / pares izquierda</option>
                                        <option value="left" @if (old('standalone_letter_position', $settings['standalone_letter_position'] ?? 'right') === 'left') selected @endif>Izquierda fija</option>
                                    </select>
                                </div>
                            </div>

                            <div class="config-panel-white mb-0">
                                <h5 class="config-section-title">Colores por letra</h5>
                                <div class="row gutters-10 mt-3">
                                    @foreach ($letters as $letter)
                                        <div class="col-xl-2 col-md-3 col-6">
                                            <div class="letter-color-cell">
                                                <label class="mb-2">{{ $letter }}</label>
                                                <div class="d-flex">
                                                    <input type="color" class="form-control mr-1" name="product_box_colors[{{ $letter }}]" value="{{ old('product_box_colors.' . $letter, $settings['product_box_colors'][$letter] ?? $letterPalette[$letter] ?? '#f36f21') }}" title="Color del recuadro">
                                                    <input type="color" class="form-control" name="product_text_colors[{{ $letter }}]" value="{{ old('product_text_colors.' . $letter, $settings['product_text_colors'][$letter] ?? '#ffffff') }}" title="Color del texto">
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="config-sticky-actions text-right">
                <button type="submit" class="btn btn-primary">
                    <i class="las la-save"></i>
                    Guardar configuracion
                </button>
            </div>
        </form>
    </div>
@endsection

@section('script')
    <script type="text/javascript">
        var coverCategoryOptions = @json($categories->map(function ($category) {
            return [
                'id' => $category->id,
                'name' => $category->getTranslation('name'),
            ];
        })->values());

        function escapeHtml(value) {
            return $('<div>').text(value || '').html();
        }

        function syncConfigToggles() {
            $('[data-config-toggle]').each(function() {
                var target = $($(this).data('config-toggle'));
                target.toggleClass('d-none', !$(this).is(':checked'));
            });
        }

        $('[data-config-toggle]').on('change', syncConfigToggles);
        syncConfigToggles();

        function coverImageRowTemplate() {
            var options = '<option value="">Selecciona una categoria</option>' + coverCategoryOptions.map(function(category) {
                return '<option value="' + escapeHtml(category.id) + '">' + escapeHtml(category.name) + '</option>';
            }).join('');

            return '<tr class="cover-image-row">' +
                '<td><select class="form-control aiz-selectpicker" name="cover_category_ids[]" data-live-search="true">' + options + '</select></td>' +
                '<td><div class="input-group" data-toggle="aizuploader" data-type="image"><div class="input-group-prepend"><div class="input-group-text bg-soft-secondary font-weight-medium">Buscar</div></div><div class="form-control file-amount">Elegir archivo</div><input type="hidden" name="cover_category_images[]" class="selected-files" value=""></div><div class="file-preview box sm"></div><small class="text-muted d-block mt-1">{{ $fullPageImageHint }}</small></td>' +
                '<td class="text-center"><button type="button" class="btn btn-soft-danger btn-icon btn-circle btn-sm remove-cover-image-row" title="Eliminar"><i class="las la-trash"></i></button></td>' +
            '</tr>';
        }

        $('#add-cover-image-row').on('click', function() {
            $('#cover-images-table tbody').append(coverImageRowTemplate());
            if ($.fn.selectpicker) { $('.aiz-selectpicker').selectpicker('refresh'); }
        });

        $(document).on('click', '.remove-cover-image-row', function() {
            if ($('.cover-image-row').length === 1) {
                var row = $(this).closest('.cover-image-row');
                row.find('select').val('');
                row.find('.selected-files').val('');
                row.find('.file-amount').text('Elegir archivo');
                row.find('.file-preview').empty();
                if ($.fn.selectpicker) { $('.aiz-selectpicker').selectpicker('refresh'); }
                return;
            }

            $(this).closest('.cover-image-row').remove();
        });

        var additionalPagePositionOptions = @json($additionalPagePositionLabels);

        function additionalPageRowTemplate() {
            var options = Object.keys(additionalPagePositionOptions).map(function(value) {
                var selected = value === 'start' ? ' selected' : '';
                return '<option value="' + escapeHtml(value) + '"' + selected + '>' + escapeHtml(additionalPagePositionOptions[value]) + '</option>';
            }).join('');

            return '<tr class="additional-page-row">' +
                '<td><div class="input-group" data-toggle="aizuploader" data-type="image"><div class="input-group-prepend"><div class="input-group-text bg-soft-secondary font-weight-medium">Buscar</div></div><div class="form-control file-amount">Elegir archivo</div><input type="hidden" name="additional_page_images[]" class="selected-files" value=""></div><div class="file-preview box sm"></div><small class="text-muted d-block mt-1">{{ $fullPageImageHint }}</small></td>' +
                '<td><select class="form-control aiz-selectpicker" name="additional_page_positions[]">' + options + '</select></td>' +
                '<td class="text-center"><button type="button" class="btn btn-soft-danger btn-icon btn-circle btn-sm remove-additional-page-row" title="Eliminar"><i class="las la-trash"></i></button></td>' +
            '</tr>';
        }

        $('#add-additional-page-row').on('click', function() {
            $('#additional-pages-table tbody').append(additionalPageRowTemplate());
            if ($.fn.selectpicker) { $('.aiz-selectpicker').selectpicker('refresh'); }
        });

        $(document).on('click', '.remove-additional-page-row', function() {
            if ($('.additional-page-row').length === 1) {
                var row = $(this).closest('.additional-page-row');
                row.find('.selected-files').val('');
                row.find('.file-amount').text('Elegir archivo');
                row.find('.file-preview').empty();
                row.find('select').val('start');
                if ($.fn.selectpicker) { $('.aiz-selectpicker').selectpicker('refresh'); }
                return;
            }

            $(this).closest('.additional-page-row').remove();
        });

        $('.config-tabs a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
            window.location.hash = $(e.target).attr('href');
            if ($.fn.selectpicker) {
                $('.aiz-selectpicker').selectpicker('refresh');
            }
        });

        if (window.location.hash && $('.config-tabs a[href="' + window.location.hash + '"]').length) {
            $('.config-tabs a[href="' + window.location.hash + '"]').tab('show');
        }
    </script>
@endsection
