@extends('backend.layouts.app')

@section('content')
    @php
        $isEdit = $mode === 'edit';
        $selectedCategoryIds = collect(old('category_ids', $catalog['category_ids'] ?? []))->map(fn($id) => (string) $id)->all();
        // product_ids llega como lista separada por coma desde el formulario (ver el input
        // oculto mas abajo), pero en el catalogo guardado es un array de enteros.
        $oldProductIds = old('product_ids');
        $selectedProductIds = is_string($oldProductIds)
            ? array_values(array_unique(array_filter(preg_split('/[^0-9]+/', $oldProductIds, -1, PREG_SPLIT_NO_EMPTY) ?: [])))
            : collect($oldProductIds ?? ($catalog['product_ids'] ?? []))->map(fn($id) => (string) $id)->all();
        $catalogStatusMap = $catalogs->mapWithKeys(function ($item) {
            return [$item['id'] => [
                'status' => $item['status'] ?? 'ready',
                'status_message' => $item['status_message'] ?? null,
                'products_count' => $item['products_count'] ?? 0,
                'has_file' => ! empty($item['file_path']) && is_file(public_path($item['file_path'])),
                'progress' => \App\Jobs\GenerateProductCatalogJob::progress($item['id']),
            ]];
        });
        $advertisingLetters = array_merge(range('A', 'Z'), ['#']);
        $advertisingRows = old('advertising_images')
            ? collect(old('advertising_images'))->map(function ($image, $index) {
                return ['image' => $image, 'letter' => old('advertising_letters.' . $index)];
            })->values()->all()
            : ($settings['advertising_items'] ?? []);

        if (empty($advertisingRows) && ! empty($settings['advertising_image'])) {
            $advertisingRows = [['image' => $settings['advertising_image'], 'letter' => 'A']];
        }

        if (empty($advertisingRows)) {
            $advertisingRows = [['image' => '', 'letter' => 'A']];
        }

        $letterIntroAdRows = old('letter_intro_ad_images')
            ? collect(old('letter_intro_ad_images'))->map(function ($image, $index) {
                return [
                    'image' => $image,
                    'category_id' => old('letter_intro_ad_category_ids.' . $index),
                    'letter' => old('letter_intro_ad_letters.' . $index),
                    'order' => old('letter_intro_ad_orders.' . $index, 1),
                ];
            })->values()->all()
            : ($settings['letter_intro_ads'] ?? []);

        if (empty($letterIntroAdRows)) {
            $letterIntroAdRows = [['image' => '', 'category_id' => '', 'letter' => 'A', 'order' => 1]];
        }
        $diagnosticFillerRows = collect();
        if (old('diagnostic_filler_category_ids') !== null) {
            $oldDiagnosticCategoryIds = old('diagnostic_filler_category_ids', []);
            foreach ($oldDiagnosticCategoryIds as $index => $categoryId) {
                $diagnosticFillerRows->push([
                    'image' => old('diagnostic_filler_images.' . $index, ''),
                    'category_id' => $categoryId,
                    'letter' => old('diagnostic_filler_letters.' . $index, ''),
                    'block_index' => old('diagnostic_filler_block_indexes.' . $index, $index),
                    'x' => old('diagnostic_filler_xs.' . $index, 0),
                    'y' => old('diagnostic_filler_ys.' . $index, 0),
                    'width' => old('diagnostic_filler_widths.' . $index, 1),
                    'height' => old('diagnostic_filler_heights.' . $index, 1),
                    'spaces' => old('diagnostic_filler_spaces.' . $index, 1),
                    'width_mm' => old('diagnostic_filler_width_mms.' . $index, 1),
                    'height_mm' => old('diagnostic_filler_height_mms.' . $index, 1),
                    'width_px_300' => old('diagnostic_filler_width_pixels.' . $index, 1),
                    'height_px_300' => old('diagnostic_filler_height_pixels.' . $index, 1),
                    'products_on_last_page' => old('diagnostic_filler_products_on_last_page.' . $index, 0),
                    'capacity' => old('diagnostic_filler_capacities.' . $index, 12),
                    'free_spaces' => old('diagnostic_filler_free_spaces.' . $index, 1),
                ]);
            }
        } else {
            $diagnosticFillerRows = collect($settings['diagnostic_filler_blocks'] ?? []);
        }
        $diagnosticFillerGroups = $diagnosticFillerRows
            ->groupBy(fn ($item) => (string) ($item['category_id'] ?? '') . '|' . (string) ($item['letter'] ?? ''));

        $productsPerPage = (int) old('products_per_page', $settings['products_per_page'] ?? 12) === 20 ? 20 : 12;
        $fullPageImageHint = 'Tamano recomendado: 2550 x 3300 px - carta vertical';
        $advertisingImageHint = 'Tamano recomendado: 1600 x 900 px - imagen horizontal';
        $selectedCoverImage = old('cover_image', $settings['cover_image'] ?? '');
        $coverImageOptions = collect($settings['cover_category_images'] ?? [])->map(function ($item) use ($categories) {
            $category = $categories->firstWhere('id', (int) ($item['category_id'] ?? 0));

            return [
                'category_id' => $item['category_id'] ?? '',
                'category_name' => $category ? $category->getTranslation('name') : 'Categoria',
                'image' => $item['image'] ?? '',
            ];
        })->filter(function ($item) {
            return ! empty($item['image']);
        })->values();

        if ($selectedCoverImage && $coverImageOptions->where('image', $selectedCoverImage)->isEmpty()) {
            $coverImageOptions->prepend([
                'category_id' => '',
                'category_name' => 'Imagen actual del catalogo',
                'image' => $selectedCoverImage,
            ]);
        }

        $catalogMessages = [
            'categoriesSelected' => 'categorias seleccionadas',
            'productsSelected' => 'productos seleccionados',
            'selectProductsHint' => 'Selecciona productos para habilitar este boton',
            'selectCategoriesTitle' => 'Selecciona categorias para cargar productos',
            'selectCategoriesBody' => 'La lista aparecera agrupada por categoria y letra',
            'loadingProductsTitle' => 'Cargando productos',
            'loadingProductsBody' => 'Espera un momento mientras se cargan los productos',
            'noProductsTitle' => 'No se encontraron productos en las categorias seleccionadas',
            'noProductsBody' => 'Intenta seleccionar otra categoria',
            'loadErrorTitle' => 'No se pudieron cargar los productos',
            'loadErrorBody' => 'Intenta nuevamente o revisa las categorias seleccionadas',
        ];
    @endphp

    <style>
        .catalog-index-shell .card {
            border: 1px solid #e5e7eb;
            box-shadow: 0 6px 18px rgba(15, 23, 42, 0.04);
        }
        .catalog-index-shell .catalog-hero {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 18px;
        }
        .catalog-index-shell .catalog-helper {
            color: #64748b;
            margin: 4px 0 0;
        }
        .catalog-index-shell .catalog-flow {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 10px;
            margin-bottom: 18px;
        }
        .catalog-index-shell .catalog-flow-step {
            display: flex;
            align-items: center;
            gap: 10px;
            background: #f8fafc;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 12px;
            min-height: 70px;
        }
        .catalog-index-shell .catalog-flow-step span {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: #f36f21;
            color: #fff;
            font-weight: 700;
            flex: 0 0 34px;
        }
        .catalog-index-shell .catalog-flow-step strong {
            display: block;
            color: #1f2937;
        }
        .catalog-index-shell .catalog-flow-step small {
            display: block;
            color: #64748b;
            line-height: 1.3;
        }
        .catalog-index-shell .catalog-section {
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 16px;
            margin-bottom: 16px;
            background: #fff;
        }
        .catalog-index-shell .catalog-section-soft {
            background: #f8fafc;
        }
        .catalog-index-shell .catalog-section-focus {
            border-color: #fed7aa;
            background: #fffaf5;
        }
        .catalog-index-shell .section-kicker {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: #f36f21;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        .catalog-index-shell .section-kicker span {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 22px;
            height: 22px;
            border-radius: 50%;
            color: #fff;
            background: #f36f21;
            font-size: 12px;
        }
        .catalog-index-shell .catalog-section-title {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 14px;
        }
        .catalog-index-shell .catalog-section-title h6 {
            margin: 0;
            color: #1f2937;
            font-size: 15px;
            font-weight: 700;
        }
        .catalog-index-shell .catalog-section-title p {
            color: #64748b;
            margin: 3px 0 0;
        }
        .catalog-index-shell .catalog-stat-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 10px;
            margin-bottom: 14px;
        }
        .catalog-index-shell .catalog-stat {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 12px;
        }
        .catalog-index-shell .catalog-stat strong {
            display: block;
            color: #111827;
            font-size: 20px;
            line-height: 1;
        }
        .catalog-index-shell .catalog-stat span {
            display: block;
            color: #64748b;
            margin-top: 4px;
        }
        .catalog-index-shell .catalog-empty-state {
            border: 1px dashed #cbd5e1;
            border-radius: 6px;
            background: #f8fafc;
            color: #64748b;
            padding: 28px 16px;
            text-align: center;
        }
        .catalog-index-shell .catalog-empty-state i {
            display: block;
            color: #94a3b8;
            font-size: 32px;
            margin-bottom: 8px;
        }
        .catalog-index-shell .catalog-tip {
            border: 1px solid #fde68a;
            border-radius: 6px;
            background: #fffbeb;
            color: #92400e;
            padding: 10px 12px;
            margin-top: 10px;
        }
        .catalog-index-shell .catalog-tip i { margin-right: 6px; }
        .catalog-index-shell .letter-intro-ad-row-empty { background: #fffbeb; }
        .catalog-index-shell .catalog-product-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 14px;
        }
        .catalog-index-shell .catalog-product-toolbar .form-control {
            max-width: 320px;
        }
        .catalog-index-shell .catalog-bulk-actions {
            display: flex;
            gap: 8px;
            flex-shrink: 0;
        }
        .catalog-index-shell .catalog-status {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border-radius: 20px;
            padding: 3px 10px;
            font-size: 12px;
            font-weight: 700;
        }
        .catalog-index-shell .catalog-status-ready { background: #ecfdf5; color: #047857; }
        .catalog-index-shell .catalog-status-pending { background: #fffbeb; color: #92400e; }
        .catalog-index-shell .catalog-status-failed { background: #fef2f2; color: #b91c1c; }
        .catalog-index-shell .catalog-progress {
            margin-top: 6px;
            min-width: 150px;
            max-width: 220px;
        }
        .catalog-index-shell .catalog-progress-track {
            height: 6px;
            border-radius: 3px;
            background: #e5e7eb;
            overflow: hidden;
        }
        .catalog-index-shell .catalog-progress-fill {
            height: 100%;
            width: 0;
            border-radius: 3px;
            background: #f36f21;
            transition: width .4s ease;
        }
        /* Fases sin total conocido (consultas, armado del HTML, guardado): franja que se
           desplaza, en vez de un porcentaje inventado. */
        .catalog-index-shell .catalog-progress-indeterminate .catalog-progress-fill {
            width: 40%;
            background: linear-gradient(90deg, rgba(243,111,33,.25), #f36f21, rgba(243,111,33,.25));
            animation: catalog-progress-slide 1.2s linear infinite;
        }
        @keyframes catalog-progress-slide {
            0%   { transform: translateX(-100%); }
            100% { transform: translateX(250%); }
        }
        .catalog-index-shell .catalog-progress-text {
            display: block;
            color: #64748b;
            margin-top: 3px;
        }
        .catalog-index-shell .catalog-download.disabled {
            opacity: .45;
            pointer-events: none;
        }
        .catalog-index-shell .catalog-status-error {
            display: block;
            color: #b91c1c;
            margin-top: 4px;
            word-break: break-word;
            max-width: 420px;
        }
        .catalog-index-shell .catalog-density-options {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
            max-width: 620px;
            margin-bottom: 16px;
        }
        .catalog-index-shell .catalog-density-option {
            position: relative;
            display: block;
            margin: 0;
            cursor: pointer;
        }
        .catalog-index-shell .catalog-density-option input {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }
        .catalog-index-shell .catalog-density-card {
            display: flex;
            align-items: center;
            gap: 12px;
            min-height: 76px;
            padding: 12px 14px;
            background: #fff;
            border: 1px solid #dbe3ec;
            border-radius: 6px;
            transition: border-color .15s ease, box-shadow .15s ease, background-color .15s ease;
        }
        .catalog-index-shell .catalog-density-option input:checked + .catalog-density-card {
            background: #fff8f3;
            border-color: #f36f21;
            box-shadow: 0 0 0 2px rgba(243, 111, 33, 0.12);
        }
        .catalog-index-shell .catalog-density-option input:focus + .catalog-density-card {
            outline: 2px solid rgba(243, 111, 33, 0.35);
            outline-offset: 2px;
        }
        .catalog-index-shell .catalog-density-icon {
            display: grid;
            flex: 0 0 54px;
            width: 54px;
            height: 44px;
            padding: 5px;
            gap: 3px;
            background: #f8fafc;
            border: 1px solid #e5e7eb;
            border-radius: 4px;
        }
        .catalog-index-shell .catalog-density-icon span {
            background: #f36f21;
            border-radius: 1px;
            opacity: .72;
        }
        .catalog-index-shell .catalog-density-icon-twenty {
            grid-template-columns: repeat(4, 1fr);
            grid-template-rows: repeat(5, 1fr);
        }
        .catalog-index-shell .catalog-density-icon-twelve {
            grid-template-columns: repeat(3, 1fr);
            grid-template-rows: repeat(4, 1fr);
        }
        .catalog-index-shell .catalog-density-copy strong,
        .catalog-index-shell .catalog-density-copy small {
            display: block;
        }
        .catalog-index-shell .catalog-density-copy strong {
            color: #1f2937;
            font-size: 14px;
        }
        .catalog-index-shell .catalog-density-copy small {
            color: #64748b;
            margin-top: 2px;
            line-height: 1.3;
        }
        .catalog-index-shell .catalog-product-table th {
            background: #f8fafc;
            border-top: 0;
            color: #475569;
            font-weight: 700;
        }
        .catalog-index-shell .catalog-category-row td {
            background: #eef6ff;
            color: #1f2937;
            font-weight: 700;
        }
        .catalog-index-shell .catalog-letter-row td {
            background: #f8fafc;
            color: #f36f21;
            font-weight: 800;
            letter-spacing: 0;
        }
        .catalog-index-shell .catalog-product-row-selectable {
            cursor: pointer;
        }
        .catalog-index-shell .catalog-product-row-selectable:hover td {
            background: #fff7ed;
        }
        .catalog-index-shell .catalog-actions {
            white-space: nowrap;
        }
        .catalog-index-shell .catalog-actions .btn {
            margin-left: 4px;
        }
        .catalog-index-shell .catalog-sticky-submit {
            position: sticky;
            bottom: 0;
            z-index: 3;
            background: rgba(255, 255, 255, 0.95);
            border-top: 1px solid #e5e7eb;
            padding: 12px 0 0;
            margin-top: 4px;
        }
        .catalog-index-shell .catalog-badge {
            border-radius: 20px;
            padding: 4px 10px;
            background: #ecfdf5;
            color: #047857;
            font-weight: 700;
            font-size: 12px;
        }
        @media (max-width: 991.98px) {
            .catalog-index-shell .catalog-flow,
            .catalog-index-shell .catalog-stat-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }
        @media (max-width: 767.98px) {
            .catalog-index-shell .catalog-flow,
            .catalog-index-shell .catalog-stat-grid {
                grid-template-columns: 1fr;
            }
            .catalog-index-shell .catalog-section-title,
            .catalog-index-shell .catalog-product-toolbar {
                display: block;
            }
            .catalog-index-shell .catalog-product-toolbar .form-control {
                max-width: 100%;
                margin-bottom: 10px;
            }
            .catalog-index-shell .catalog-actions .btn {
                margin-bottom: 4px;
            }
        }
    </style>

    <div class="catalog-index-shell">
        <div class="aiz-titlebar text-left mt-2 mb-3">
            <div class="row align-items-center">
                <div class="col-lg-7">
                    <h1 class="h3">Catalogos PDF</h1>
                    <p class="mb-0 text-muted">
                        Crea, edita y descarga catalogos de productos desde un solo lugar.
                    </p>
                </div>
                <div class="col-lg-5 text-lg-right mt-3 mt-lg-0">
                    @if ($isEdit)
                        <a href="{{ route('product_catalogs.index') }}" class="btn btn-soft-secondary mr-2">
                            <i class="las la-plus"></i>
                            Crear nuevo catalogo
                        </a>
                    @endif
                    <a class="btn btn-soft-primary" href="{{ $isEdit ? route('product_catalogs.configuration', $catalog['id']) : route('product_catalogs.configuration.defaults') }}">
                        <i class="las la-cog"></i>
                        Configuracion del catalogo
                    </a>
                </div>
            </div>
        </div>

        <div class="catalog-hero mb-3">
            <div class="catalog-flow">
                <div class="catalog-flow-step">
                    <span>1</span>
                    <div>
                        <strong>Portada</strong>
                        <small>Imagen, titulo y asesor</small>
                    </div>
                </div>
                <div class="catalog-flow-step">
                    <span>2</span>
                    <div>
                        <strong>Categorias</strong>
                        <small>Grupos de productos</small>
                    </div>
                </div>
                <div class="catalog-flow-step">
                    <span>3</span>
                    <div>
                        <strong>Productos</strong>
                        <small>Buscar y seleccionar</small>
                    </div>
                </div>
                <div class="catalog-flow-step">
                    <span>4</span>
                    <div>
                        <strong>PDF</strong>
                        <small>Generar archivo</small>
                    </div>
                </div>
            </div>
            <p class="catalog-helper mb-0">
                Selecciona las categorias, elige la portada y marca los productos que iran en el catalogo.
            </p>
        </div>

        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <div>
                    <h5 class="mb-0 h6">{{ $isEdit ? 'Editar catalogo' : 'Crear catalogo' }}</h5>
                    <small class="text-muted">
                        {{ $isEdit ? 'Ajusta la informacion y vuelve a generar el PDF' : 'Completa las secciones para generar un nuevo PDF' }}
                    </small>
                </div>
                <span class="catalog-badge">{{ $isEdit ? 'Editando' : 'Nuevo' }}</span>
            </div>
            <div class="card-body">
                <form action="{{ $isEdit ? route('product_catalogs.update', $catalog['id']) : route('product_catalogs.store') }}" method="POST" id="catalog-form">
                    @csrf
                    @if ($isEdit)
                        @method('PUT')
                    @endif

                    <div class="catalog-section catalog-section-focus">
                        <div class="catalog-section-title">
                            <div>
                                <div class="section-kicker"><span>1</span> Portada</div>
                                <h6>Datos de la primera pagina</h6>
                                <p>Elige una portada configurada por categoria y, si aplica, la imagen final del catalogo.</p>
                            </div>
                        </div>

                        <div class="row gutters-10">
                            <div class="col-lg-4">
                                <div class="form-group mb-lg-0">
                                    <label>Primera imagen del catalogo</label>
                                    @if ($coverImageOptions->isNotEmpty())
                                        <select class="form-control aiz-selectpicker" name="cover_image" id="catalog-cover-image" data-live-search="true">
                                            <option value="">Selecciona una portada</option>
                                            @foreach ($coverImageOptions as $coverImageOption)
                                                <option value="{{ $coverImageOption['image'] }}" data-category-id="{{ $coverImageOption['category_id'] }}" @if ($selectedCoverImage === $coverImageOption['image']) selected @endif>
                                                    {{ $coverImageOption['category_name'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <small class="text-muted d-block mt-1">Estas imagenes se administran desde Configuracion del catalogo.</small>
                                        <div class="catalog-tip d-none" id="catalog-cover-empty-tip">
                                            <i class="las la-info-circle"></i>
                                            No hay portadas configuradas para las categorias seleccionadas. Puedes elegir otra categoria o agregar la portada en Configuracion del catalogo.
                                        </div>
                                    @else
                                        <input type="hidden" name="cover_image" value="">
                                        <div class="alert alert-soft-warning mb-0">
                                            Primero configura portadas por categoria en Configuracion del catalogo.
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group mb-lg-0">
                                    <label>Imagen final del catalogo</label>
                                    <div class="input-group" data-toggle="aizuploader" data-type="image">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text bg-soft-secondary font-weight-medium">Buscar</div>
                                        </div>
                                        <div class="form-control file-amount">Elegir archivo</div>
                                        <input type="hidden" name="final_page_image" class="selected-files" value="{{ old('final_page_image', $settings['final_page_image'] ?? '') }}">
                                    </div>
                                    <div class="file-preview box sm"></div>
                                    <small class="text-muted d-block mt-1">{{ $fullPageImageHint }} â€” la imagen es obligatoria para mostrar la pagina final.</small>
                                    <label class="aiz-checkbox mb-0 mt-2">
                                        <input type="checkbox" name="final_page_blank" value="1" @if (old('final_page_blank', $settings['final_page_blank'] ?? false)) checked @endif>
                                        <span class="aiz-square-check"></span>
                                        <span>Mostrar pagina final (requiere haber subido la imagen)</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="catalog-section">
                        <div class="catalog-section-title">
                            <div>
                                <div class="section-kicker"><span>2</span> Datos</div>
                                <h6>Categorias y nombre del catalogo</h6>
                                <p>Selecciona una o varias categorias para cargar los productos disponibles.</p>
                            </div>
                            <small class="text-muted" id="selected-categories-count">0 categorias seleccionadas</small>
                        </div>

                        <div class="row gutters-10 align-items-end">
                            <div class="col-lg-5">
                                <div class="form-group mb-lg-0">
                                    <label>Categorias</label>
                                    <select class="form-control aiz-selectpicker" name="category_ids[]" id="catalog-category" data-live-search="true" data-actions-box="true" multiple required>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}" @if (in_array((string) $category->id, $selectedCategoryIds)) selected @endif>{{ $category->getTranslation('name') }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group mb-lg-0">
                                    <label>Nombre del catalogo</label>
                                    <input type="text" class="form-control" name="name" value="{{ old('name', $catalog['name'] ?? '') }}" placeholder="Nombre del catalogo">
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group mb-0">
                                    <button type="submit" class="btn btn-primary btn-block" id="generate-catalog" disabled>
                                        <i class="las la-file-pdf"></i>
                                        <span class="submit-label">{{ $isEdit ? 'Actualizar catalogo PDF' : 'Generar catalogo PDF' }}</span>
                                    </button>
                                    <small class="text-muted d-block mt-2" id="catalog-submit-hint">Selecciona productos para habilitar este boton</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="catalog-section catalog-section-soft">
                        <div class="catalog-section-title">
                            <div>
                                <div class="section-kicker"><span>3</span> Publicidad</div>
                                <h6>Publicidad dentro de productos</h6>
                                <p>Imagen horizontal que aparece al lado derecho de los productos de una letra.</p>
                            </div>
                            <button type="button" class="btn btn-soft-primary btn-sm" id="add-advertising-row">
                                <i class="las la-plus"></i>
                                Agregar publicidad
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered mb-0" id="advertising-table">
                                <thead>
                                    <tr>
                                        <th>Imagen de publicidad</th>
                                        <th width="190">Mostrar con la letra</th>
                                        <th width="80" class="text-center">Opciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($advertisingRows as $advertisingRow)
                                        <tr class="advertising-row">
                                            <td>
                                                <div class="input-group" data-toggle="aizuploader" data-type="image">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text bg-soft-secondary font-weight-medium">Buscar</div>
                                                    </div>
                                                    <div class="form-control file-amount">Elegir archivo</div>
                                                    <input type="hidden" name="advertising_images[]" class="selected-files" value="{{ $advertisingRow['image'] ?? '' }}">
                                                </div>
                                                <div class="file-preview box sm"></div>
                                                <small class="text-muted d-block mt-1">{{ $advertisingImageHint }}</small>
                                            </td>
                                            <td>
                                                <select class="form-control aiz-selectpicker" name="advertising_letters[]">
                                                    @foreach ($advertisingLetters as $advertisingLetter)
                                                        <option value="{{ $advertisingLetter }}" @if (($advertisingRow['letter'] ?? 'A') === $advertisingLetter) selected @endif>{{ $advertisingLetter }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-soft-danger btn-icon btn-circle btn-sm remove-advertising-row" title="Eliminar">
                                                    <i class="las la-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="catalog-section catalog-section-soft">
                        <div class="catalog-section-title">
                            <div>
                                <div class="section-kicker"><span>4</span> Separadores</div>
                                <h6>Publicidad al iniciar una letra</h6>
                                <p>Permite colocar hasta dos imágenes a página completa antes de iniciar la letra elegida y definir cuál aparece primero.</p>
                                <p class="text-muted mb-0"><small>Solo se muestra si la categoria seleccionada tiene productos con esa letra. Las letras sin productos quedan deshabilitadas.</small></p>
                            </div>
                            <button type="button" class="btn btn-soft-primary btn-sm" id="add-letter-intro-ad-row">
                                <i class="las la-plus"></i>
                                Agregar pagina
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered mb-0" id="letter-intro-ad-table">
                                <thead>
                                    <tr>
                                        <th>Imagen a pagina completa</th>
                                        <th width="260">Categoria</th>
                                        <th width="150">Letra</th>
                                        <th width="110">Orden</th>
                                        <th width="80" class="text-center">Opciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($letterIntroAdRows as $letterIntroAdRow)
                                        <tr class="letter-intro-ad-row">
                                            <td>
                                                <div class="input-group" data-toggle="aizuploader" data-type="image">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text bg-soft-secondary font-weight-medium">Buscar</div>
                                                    </div>
                                                    <div class="form-control file-amount">Elegir archivo</div>
                                                    <input type="hidden" name="letter_intro_ad_images[]" class="selected-files" value="{{ $letterIntroAdRow['image'] ?? '' }}">
                                                </div>
                                                <div class="file-preview box sm"></div>
                                                <small class="text-muted d-block mt-1">{{ $fullPageImageHint }}</small>
                                            </td>
                                            <td>
                                                <select class="form-control aiz-selectpicker letter-intro-ad-category" name="letter_intro_ad_category_ids[]" data-live-search="true">
                                                    <option value="">Selecciona una categoria</option>
                                                    @foreach ($categories as $category)
                                                        <option value="{{ $category->id }}" @if ((string) ($letterIntroAdRow['category_id'] ?? '') === (string) $category->id) selected @endif>{{ $category->getTranslation('name') }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <select class="form-control aiz-selectpicker" name="letter_intro_ad_letters[]">
                                                    @foreach ($advertisingLetters as $advertisingLetter)
                                                        <option value="{{ $advertisingLetter }}" @if (($letterIntroAdRow['letter'] ?? 'A') === $advertisingLetter) selected @endif>{{ $advertisingLetter }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <select class="form-control aiz-selectpicker" name="letter_intro_ad_orders[]">
                                                    <option value="1" @if ((int) ($letterIntroAdRow['order'] ?? 1) === 1) selected @endif>1 - Primero</option>
                                                    <option value="2" @if ((int) ($letterIntroAdRow['order'] ?? 1) === 2) selected @endif>2 - Segundo</option>
                                                </select>
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-soft-danger btn-icon btn-circle btn-sm remove-letter-intro-ad-row" title="Eliminar">
                                                    <i class="las la-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="catalog-section catalog-section-soft">
                        <div class="catalog-section-title">
                            <div>
                                <div class="section-kicker"><span>5</span> Rellenos</div>
                                <h6>Piezas gráficas en espacios libres</h6>
                                <p>Calcula los espacios al finalizar cada letra y carga directamente una imagen para cada bloque detectado.</p>
                            </div>
                        </div>

                        <div class="config-panel-white mb-0" id="filler-diagnostics-panel">
                            <div class="catalog-section-title mb-3">
                                <div>
                                    <h6>Diagnóstico de espacios</h6>
                                    <p>Las medidas aparecen debajo de cada selector. Para reemplazar una pieza, elige otra imagen en el mismo campo.</p>
                                </div>
                                <button type="button" class="btn btn-primary btn-sm" id="calculate-filler-spaces">
                                    <i class="las la-calculator"></i> Calcular espacios disponibles
                                </button>
                            </div>

                            <div id="filler-diagnostics-empty" class="alert alert-light border mb-0 {{ $diagnosticFillerGroups->isNotEmpty() ? 'd-none' : '' }}">
                                Primero selecciona las categorías y los productos. Después pulsa <strong>Calcular espacios disponibles</strong>.
                            </div>

                            <div id="filler-diagnostics-wrap" class="table-responsive {{ $diagnosticFillerGroups->isEmpty() ? 'd-none' : '' }}">
                                <table class="table table-bordered mb-0">
                                    <thead>
                                        <tr>
                                            <th>Categoría y letra</th>
                                            <th width="150">Última página</th>
                                            <th width="120">Espacios libres</th>
                                            <th>Bloques detectados</th>
                                            <th width="310">Piezas gráficas</th>
                                        </tr>
                                    </thead>
                                    <tbody id="filler-diagnostics-body">
                                        @foreach ($diagnosticFillerGroups as $diagnosticKey => $diagnosticGroup)
                                            @php
                                                $diagnosticGroup = collect($diagnosticGroup)->sortBy('block_index')->values();
                                                $diagnosticFirst = $diagnosticGroup->first();
                                                $diagnosticCategory = $categories->firstWhere('id', (int) ($diagnosticFirst['category_id'] ?? 0));
                                                $diagnosticCategoryName = $diagnosticCategory ? $diagnosticCategory->getTranslation('name') : 'Categoría';
                                                $diagnosticBlocksLabel = $diagnosticGroup->map(fn ($block) => ($block['width'] ?? 1) . ' × ' . ($block['height'] ?? 1) . ' (' . ($block['spaces'] ?? 1) . ' espacios)')->join(' + ');
                                            @endphp
                                            <tr>
                                                <td><strong>{{ $diagnosticCategoryName }}</strong><br><span class="badge badge-soft-success">Letra {{ $diagnosticFirst['letter'] ?? '' }}</span></td>
                                                <td>{{ $diagnosticFirst['products_on_last_page'] ?? 0 }} de {{ $diagnosticFirst['capacity'] ?? 12 }}</td>
                                                <td><strong>{{ $diagnosticFirst['free_spaces'] ?? $diagnosticGroup->sum('spaces') }}</strong></td>
                                                <td>{{ $diagnosticBlocksLabel }}</td>
                                                <td>
                                                    @foreach ($diagnosticGroup as $diagnosticBlock)
                                                        <div class="border rounded p-2 mb-2 diagnostic-filler-piece">
                                                            <strong class="d-block mb-1">{{ $diagnosticBlock['width'] ?? 1 }} × {{ $diagnosticBlock['height'] ?? 1 }}</strong>
                                                            <div class="input-group" data-toggle="aizuploader" data-type="image">
                                                                <div class="input-group-prepend"><div class="input-group-text bg-soft-secondary font-weight-medium">Subir gráfica</div></div>
                                                                <div class="form-control file-amount">Elegir archivo</div>
                                                                <input type="hidden" name="diagnostic_filler_images[]" class="selected-files diagnostic-filler-image" value="{{ $diagnosticBlock['image'] ?? '' }}">
                                                            </div>
                                                            <div class="file-preview box sm"></div>
                                                            <input type="hidden" name="diagnostic_filler_category_ids[]" value="{{ $diagnosticBlock['category_id'] ?? '' }}">
                                                            <input type="hidden" name="diagnostic_filler_letters[]" value="{{ $diagnosticBlock['letter'] ?? '' }}">
                                                            <input type="hidden" name="diagnostic_filler_block_indexes[]" value="{{ $diagnosticBlock['block_index'] ?? $loop->index }}">
                                                            <input type="hidden" name="diagnostic_filler_xs[]" value="{{ $diagnosticBlock['x'] ?? 0 }}">
                                                            <input type="hidden" name="diagnostic_filler_ys[]" value="{{ $diagnosticBlock['y'] ?? 0 }}">
                                                            <input type="hidden" name="diagnostic_filler_widths[]" value="{{ $diagnosticBlock['width'] ?? 1 }}">
                                                            <input type="hidden" name="diagnostic_filler_heights[]" value="{{ $diagnosticBlock['height'] ?? 1 }}">
                                                            <input type="hidden" name="diagnostic_filler_spaces[]" value="{{ $diagnosticBlock['spaces'] ?? 1 }}">
                                                            <input type="hidden" name="diagnostic_filler_width_mms[]" value="{{ $diagnosticBlock['width_mm'] ?? 1 }}">
                                                            <input type="hidden" name="diagnostic_filler_height_mms[]" value="{{ $diagnosticBlock['height_mm'] ?? 1 }}">
                                                            <input type="hidden" name="diagnostic_filler_width_pixels[]" value="{{ $diagnosticBlock['width_px_300'] ?? 1 }}">
                                                            <input type="hidden" name="diagnostic_filler_height_pixels[]" value="{{ $diagnosticBlock['height_px_300'] ?? 1 }}">
                                                            <input type="hidden" name="diagnostic_filler_products_on_last_page[]" value="{{ $diagnosticBlock['products_on_last_page'] ?? 0 }}">
                                                            <input type="hidden" name="diagnostic_filler_capacities[]" value="{{ $diagnosticBlock['capacity'] ?? 12 }}">
                                                            <input type="hidden" name="diagnostic_filler_free_spaces[]" value="{{ $diagnosticBlock['free_spaces'] ?? 1 }}">
                                                            <small class="text-muted d-block mt-1">{{ $diagnosticBlock['width_mm'] ?? 1 }} × {{ $diagnosticBlock['height_mm'] ?? 1 }} mm · {{ $diagnosticBlock['width_px_300'] ?? 1 }} × {{ $diagnosticBlock['height_px_300'] ?? 1 }} px</small>
                                                        </div>
                                                    @endforeach
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <small class="text-muted d-block mt-2">Al volver a calcular se eliminarán todas las piezas cargadas, previa confirmación. La última imagen seleccionada en cada campo reemplaza a la anterior.</small>
                        </div>
                    </div>

                    <div class="catalog-section">
                        <div class="catalog-section-title">
                            <div>
                                <div class="section-kicker"><span>6</span> Productos</div>
                                <h6>Seleccion de productos</h6>
                                <p>Solo se pueden seleccionar productos con precio mayor a cero.</p>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold mb-2">Productos por pagina</label>
                            <div class="catalog-density-options">
                                <label class="catalog-density-option">
                                    <input type="radio" name="products_per_page" value="12" @checked($productsPerPage === 12)>
                                    <span class="catalog-density-card">
                                        <span class="catalog-density-icon catalog-density-icon-twelve" aria-hidden="true">
                                            @for ($i = 0; $i < 12; $i++)<span></span>@endfor
                                        </span>
                                        <span class="catalog-density-copy">
                                            <strong>12 productos</strong>
                                            <small>Tarjetas amplias e imagenes mas grandes</small>
                                        </span>
                                    </span>
                                </label>
                                <label class="catalog-density-option">
                                    <input type="radio" name="products_per_page" value="20" @checked($productsPerPage === 20)>
                                    <span class="catalog-density-card">
                                        <span class="catalog-density-icon catalog-density-icon-twenty" aria-hidden="true">
                                            @for ($i = 0; $i < 20; $i++)<span></span>@endfor
                                        </span>
                                        <span class="catalog-density-copy">
                                            <strong>20 productos</strong>
                                            <small>Formato compacto para catalogos extensos</small>
                                        </span>
                                    </span>
                                </label>
                            </div>
                        </div>

                        <div class="catalog-stat-grid">
                            <div class="catalog-stat">
                                <strong id="selected-products-count">0</strong>
                                <span>Productos seleccionados</span>
                            </div>
                            <div class="catalog-stat">
                                <strong id="loaded-products-count">0</strong>
                                <span>Productos en pantalla</span>
                            </div>
                            <div class="catalog-stat">
                                <strong id="unavailable-products-count">0</strong>
                                <span>No disponibles</span>
                            </div>
                        </div>

                        {{-- Un solo campo con los ids separados por coma: un input por producto
                             choca contra max_input_vars de PHP (1000 por defecto), que descarta
                             en silencio todo lo que pase de esa cantidad. --}}
                        <input type="hidden" name="product_ids" id="catalog-product-ids" value="{{ implode(',', $selectedProductIds) }}">

                        <div class="catalog-product-toolbar">
                            <input type="text" class="form-control form-control-sm d-none" id="catalog-product-search" placeholder="Buscar por nombre, referencia o ID">
                            <div class="catalog-bulk-actions d-none" id="catalog-bulk-actions">
                                <button type="button" class="btn btn-soft-primary btn-sm" id="select-all-matching">
                                    <i class="las la-check-double"></i>
                                    <span id="select-all-matching-label">Seleccionar todos</span>
                                </button>
                                <button type="button" class="btn btn-soft-secondary btn-sm" id="clear-selection">
                                    <i class="las la-times"></i>
                                    Quitar seleccion
                                </button>
                            </div>
                        </div>

                        <div id="catalog-products" class="catalog-empty-state">
                            <i class="las la-box-open"></i>
                            <strong>Selecciona categorias para cargar productos</strong>
                            <div>La lista aparecera agrupada por categoria y letra</div>
                        </div>

                        <div class="text-center mt-3 d-none" id="catalog-load-more-wrap">
                            <button type="button" class="btn btn-soft-primary btn-sm" id="catalog-load-more">
                                <i class="las la-angle-down"></i>
                                Cargar mas productos
                            </button>
                            <small class="d-block text-muted mt-2" id="catalog-load-more-hint"></small>
                        </div>
                    </div>

                    <div class="catalog-sticky-submit">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <small class="text-muted">
                                    Revisa los productos seleccionados antes de generar el PDF.
                                </small>
                            </div>
                            <div class="col-md-4 text-md-right mt-2 mt-md-0">
                                <button type="submit" class="btn btn-primary" id="generate-catalog-bottom" disabled>
                                    <i class="las la-file-pdf"></i>
                                    <span class="submit-label">{{ $isEdit ? 'Actualizar catalogo PDF' : 'Generar catalogo PDF' }}</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h5 class="mb-0 h6">Catalogos generados</h5>
                        <small class="text-muted">Descarga, edita o elimina los archivos existentes.</small>
                    </div>
                    <div class="col-md-6 mt-3 mt-md-0">
                        <input type="text" class="form-control form-control-sm" id="catalog-list-search" placeholder="Buscar catalogos generados">
                    </div>
                </div>
            </div>
            <div class="card-body">
                <table class="table aiz-table mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nombre</th>
                            <th data-breakpoints="lg">Categorias</th>
                            <th data-breakpoints="lg">Productos</th>
                            <th>Estado</th>
                            <th data-breakpoints="lg">Creado</th>
                            <th class="text-right">Opciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($catalogs as $key => $catalogItem)
                            @php
                                $catalogSearch = strtolower(($catalogItem['name'] ?? '') . ' ' . ($catalogItem['category_name'] ?? '') . ' ' . ($catalogItem['created_at'] ?? ''));
                                $catalogStatus = $catalogItem['status'] ?? 'ready';
                                $catalogHasFile = ! empty($catalogItem['file_path']);
                            @endphp
                            <tr class="catalog-list-row" data-catalog-search="{{ $catalogSearch }}" data-catalog-id="{{ $catalogItem['id'] }}">
                                <td>{{ $key + 1 }}</td>
                                <td>
                                    <span class="fw-700 text-dark">{{ $catalogItem['name'] }}</span>
                                    @if (! empty($catalogItem['updated_at']))
                                        <small class="d-block text-muted">Actualizado: {{ $catalogItem['updated_at'] }}</small>
                                    @endif
                                </td>
                                <td>{{ $catalogItem['category_name'] }}</td>
                                <td><span class="badge badge-inline badge-soft-info catalog-products-count">{{ $catalogItem['products_count'] }}</span></td>
                                <td class="catalog-status-cell">
                                    <span class="catalog-status"></span>
                                    <div class="catalog-progress d-none">
                                        <div class="catalog-progress-track">
                                            <div class="catalog-progress-fill"></div>
                                        </div>
                                        <small class="catalog-progress-text"></small>
                                    </div>
                                    <small class="catalog-status-error d-none"></small>
                                </td>
                                <td>{{ $catalogItem['created_at'] }}</td>
                                <td class="text-right catalog-actions">
                                    <a class="btn btn-soft-info btn-sm" href="{{ route('product_catalogs.edit', $catalogItem['id']) }}" title="Editar">
                                        <i class="las la-edit"></i>
                                        Editar
                                    </a>
                                    <form action="{{ route('product_catalogs.duplicate', $catalogItem['id']) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Crear una copia independiente de este catalogo?');">
                                        @csrf
                                        <button type="submit" class="btn btn-soft-success btn-sm" title="Crear una copia para editar sin alterar el original">
                                            <i class="las la-copy"></i>
                                            Copiar
                                        </button>
                                    </form>
                                    <a class="btn btn-soft-primary btn-sm catalog-download" href="{{ route('product_catalogs.download', $catalogItem['id']) }}" title="Descargar">
                                        <i class="las la-download"></i>
                                        Descargar
                                    </a>
                                    <form action="{{ route('product_catalogs.regenerate', $catalogItem['id']) }}" method="POST" class="d-inline-block">
                                        @csrf
                                        <button type="submit" class="btn btn-soft-warning btn-sm catalog-regenerate" title="Volver a generar el PDF">
                                            <i class="las la-redo-alt"></i>
                                            Regenerar
                                        </button>
                                    </form>
                                    <form action="{{ route('product_catalogs.destroy', $catalogItem['id']) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Seguro que deseas eliminar este catalogo?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-soft-danger btn-sm" title="Eliminar">
                                            <i class="las la-trash"></i>
                                            Eliminar
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                        @if ($catalogs->isEmpty())
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <div class="catalog-empty-state">
                                        <i class="las la-file-pdf"></i>
                                        <strong>No hay catalogos generados</strong>
                                        <div>Los catalogos creados apareceran aqui.</div>
                                    </div>
                                </td>
                            </tr>
                        @else
                            <tr id="catalogs-no-results" class="d-none">
                                <td colspan="7" class="text-center text-muted py-4">No hay catalogos que coincidan con la busqueda</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script type="text/javascript">
        var PRODUCTS_PER_REQUEST = 250;
        var CATEGORY_PRODUCTS_URL = '{{ route('product_catalogs.category_products') }}';
        var CATALOG_STATUSES_URL = '{{ route('product_catalogs.statuses') }}';
        // Solo se consulta mientras haya un catalogo en cola o generandose, y el renderer
        // publica progreso como maximo una vez por segundo, asi que 2 s va sobrado.
        var STATUS_POLL_MS = 2000;

        // La seleccion vive en este Set, no en los checkboxes: de un catalogo de miles de
        // productos solo hay una pagina pintada a la vez, asi que el estado no puede
        // depender de lo que este en el DOM.
        var selectedIds = new Set(@json($selectedProductIds));
        var advertisingLetterOptions = @json($advertisingLetters);
        var categoryLetterMap = {};
        var catalogCategoryOptions = @json($categories->map(function ($category) {
            return [
                'id' => $category->id,
                'name' => $category->getTranslation('name'),
            ];
        })->values());
        var generateCatalogText = @json($isEdit ? 'Actualizar catalogo PDF' : 'Generar catalogo PDF');
        var catalogMessages = @json($catalogMessages);
        var catalogStatuses = @json($catalogStatusMap);

        var picker = {
            page: 0,
            total: 0,
            selectableTotal: 0,
            loaded: 0,
            hasMore: false,
            search: '',
            token: 0,
            loading: false,
            lastCategoryId: null,
            lastLetter: null
        };
        var searchTimer = null;
        var statusTimer = null;

        var STATUS_LABELS = {
            queued:     { text: 'En cola',   cls: 'catalog-status-pending', icon: 'las la-clock' },
            processing: { text: 'Generando', cls: 'catalog-status-pending', icon: 'las la-spinner la-spin' },
            ready:      { text: 'Listo',     cls: 'catalog-status-ready',   icon: 'las la-check-circle' },
            failed:     { text: 'Error',     cls: 'catalog-status-failed',  icon: 'las la-exclamation-triangle' }
        };

        function escapeHtml(value) {
            return $('<div>').text(value === null || typeof value === 'undefined' ? '' : value).html();
        }

        function productEmptyState(icon, title, body) {
            return '<div class="catalog-empty-state"><i class="' + icon + '"></i><strong>' + title + '</strong><div>' + body + '</div></div>';
        }

        function advertisingRowTemplate() {
            var options = advertisingLetterOptions.map(function(letter) {
                return '<option value="' + escapeHtml(letter) + '">' + escapeHtml(letter) + '</option>';
            }).join('');

            return '<tr class="advertising-row">' +
                '<td><div class="input-group" data-toggle="aizuploader" data-type="image"><div class="input-group-prepend"><div class="input-group-text bg-soft-secondary font-weight-medium">Buscar</div></div><div class="form-control file-amount">Elegir archivo</div><input type="hidden" name="advertising_images[]" class="selected-files" value=""></div><div class="file-preview box sm"></div><small class="text-muted d-block mt-1">{{ $advertisingImageHint }}</small></td>' +
                '<td><select class="form-control aiz-selectpicker" name="advertising_letters[]">' + options + '</select></td>' +
                '<td class="text-center"><button type="button" class="btn btn-soft-danger btn-icon btn-circle btn-sm remove-advertising-row" title="Eliminar"><i class="las la-trash"></i></button></td>' +
            '</tr>';
        }

        function categoryOptionsTemplate() {
            return '<option value="">Selecciona una categoria</option>' + catalogCategoryOptions.map(function(category) {
                return '<option value="' + escapeHtml(category.id) + '">' + escapeHtml(category.name) + '</option>';
            }).join('');
        }

        function letterIntroAdRowTemplate() {
            var letterOptions = advertisingLetterOptions.map(function(letter) {
                return '<option value="' + escapeHtml(letter) + '">' + escapeHtml(letter) + '</option>';
            }).join('');

            return '<tr class="letter-intro-ad-row">' +
                '<td><div class="input-group" data-toggle="aizuploader" data-type="image"><div class="input-group-prepend"><div class="input-group-text bg-soft-secondary font-weight-medium">Buscar</div></div><div class="form-control file-amount">Elegir archivo</div><input type="hidden" name="letter_intro_ad_images[]" class="selected-files" value=""></div><div class="file-preview box sm"></div><small class="text-muted d-block mt-1">{{ $fullPageImageHint }}</small></td>' +
                '<td><select class="form-control aiz-selectpicker letter-intro-ad-category" name="letter_intro_ad_category_ids[]" data-live-search="true">' + categoryOptionsTemplate() + '</select></td>' +
                '<td><select class="form-control aiz-selectpicker" name="letter_intro_ad_letters[]">' + letterOptions + '</select></td>' +
                '<td><select class="form-control aiz-selectpicker" name="letter_intro_ad_orders[]"><option value="1">1 - Primero</option><option value="2">2 - Segundo</option></select></td>' +
                '<td class="text-center"><button type="button" class="btn btn-soft-danger btn-icon btn-circle btn-sm remove-letter-intro-ad-row" title="Eliminar"><i class="las la-trash"></i></button></td>' +
            '</tr>';
        }

        var fillerDiagnosticsUrl = @json(route('product_catalogs.filler_diagnostics'));

        function diagnosticPieceHtml(d, b, blockIndex) {
            var prefix = '<input type="hidden" name="diagnostic_filler_category_ids[]" value="'+escapeHtml(d.category_id)+'">' +
                '<input type="hidden" name="diagnostic_filler_letters[]" value="'+escapeHtml(d.letter)+'">' +
                '<input type="hidden" name="diagnostic_filler_block_indexes[]" value="'+blockIndex+'">' +
                '<input type="hidden" name="diagnostic_filler_xs[]" value="'+b.x+'">' +
                '<input type="hidden" name="diagnostic_filler_ys[]" value="'+b.y+'">' +
                '<input type="hidden" name="diagnostic_filler_widths[]" value="'+b.width+'">' +
                '<input type="hidden" name="diagnostic_filler_heights[]" value="'+b.height+'">' +
                '<input type="hidden" name="diagnostic_filler_spaces[]" value="'+b.spaces+'">' +
                '<input type="hidden" name="diagnostic_filler_width_mms[]" value="'+b.width_mm+'">' +
                '<input type="hidden" name="diagnostic_filler_height_mms[]" value="'+b.height_mm+'">' +
                '<input type="hidden" name="diagnostic_filler_width_pixels[]" value="'+b.width_px_300+'">' +
                '<input type="hidden" name="diagnostic_filler_height_pixels[]" value="'+b.height_px_300+'">' +
                '<input type="hidden" name="diagnostic_filler_products_on_last_page[]" value="'+d.products_on_last_page+'">' +
                '<input type="hidden" name="diagnostic_filler_capacities[]" value="'+d.capacity+'">' +
                '<input type="hidden" name="diagnostic_filler_free_spaces[]" value="'+d.free_spaces+'">';

            return '<div class="border rounded p-2 mb-2 diagnostic-filler-piece">' +
                '<strong class="d-block mb-1">'+b.width+' × '+b.height+'</strong>' +
                '<div class="input-group" data-toggle="aizuploader" data-type="image">' +
                    '<div class="input-group-prepend"><div class="input-group-text bg-soft-secondary font-weight-medium">Subir gráfica</div></div>' +
                    '<div class="form-control file-amount">Elegir archivo</div>' +
                    '<input type="hidden" name="diagnostic_filler_images[]" class="selected-files diagnostic-filler-image" value="">' +
                '</div><div class="file-preview box sm"></div>' + prefix +
                '<small class="text-muted d-block mt-1">'+b.width_mm+' × '+b.height_mm+' mm · '+b.width_px_300+' × '+b.height_px_300+' px</small>' +
            '</div>';
        }

        $('#calculate-filler-spaces').on('click', function() {
            var categoryIds = $('select[name="category_ids[]"]').val() || [];
            var productIds = $('#catalog-product-ids').val() || '';
            if (!categoryIds.length || !productIds) {
                AIZ.plugins.notify('warning', 'Selecciona categorías y productos antes de calcular.');
                return;
            }

            var hasUploadedPieces = $('input[name="diagnostic_filler_images[]"]').filter(function() {
                return $.trim($(this).val() || '') !== '';
            }).length > 0;

            if (hasUploadedPieces && !window.confirm('Los espacios serán recalculados y se eliminarán todas las piezas gráficas cargadas. ¿Continuar?')) {
                return;
            }

            var btn = $(this);
            btn.prop('disabled', true).html('<i class="las la-spinner la-spin"></i> Calculando');
            $.ajax({
                url: fillerDiagnosticsUrl,
                method: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    category_ids: categoryIds,
                    product_ids: productIds,
                    products_per_page: $('input[name="products_per_page"]:checked').val(),
                    advertising_letters: $('.advertising-row').map(function() {
                        var image = $(this).find('input[name="advertising_images[]"]').val();
                        return image ? $(this).find('select[name="advertising_letters[]"]').val() : null;
                    }).get()
                }
            }).done(function(resp) {
                var rows = '';
                (resp.diagnostics || []).forEach(function(d) {
                    var blocks = d.blocks.map(function(b) {
                        return b.width+' × '+b.height+' ('+b.spaces+' espacios)';
                    }).join(' + ');
                    var pieces = d.blocks.map(function(b, i) {
                        return diagnosticPieceHtml(d, b, i);
                    }).join('');
                    rows += '<tr><td><strong>'+escapeHtml(d.category_name)+'</strong><br><span class="badge badge-soft-success">Letra '+escapeHtml(d.letter)+'</span></td>' +
                        '<td>'+d.products_on_last_page+' de '+d.capacity+'</td>' +
                        '<td><strong>'+d.free_spaces+'</strong></td>' +
                        '<td>'+blocks+'</td><td>'+pieces+'</td></tr>';
                });
                $('#filler-diagnostics-body').html(rows || '<tr><td colspan="5" class="text-center text-muted">No se detectaron espacios libres.</td></tr>');
                $('#filler-diagnostics-empty').addClass('d-none');
                $('#filler-diagnostics-wrap').removeClass('d-none');
            }).fail(function(xhr) {
                AIZ.plugins.notify('danger', (xhr.responseJSON && xhr.responseJSON.message) || 'No fue posible calcular los espacios.');
            }).always(function() {
                btn.prop('disabled', false).html('<i class="las la-calculator"></i> Calcular espacios disponibles');
            });
        });

        function refreshCategorySummary() {
            var count = ($('#catalog-category').val() || []).length;
            $('#selected-categories-count').text(count + ' ' + catalogMessages.categoriesSelected);
        }

        function refreshCoverImageOptions() {
            var categoryIds = ($('#catalog-category').val() || []).map(String);
            var coverSelect = $('#catalog-cover-image');

            if (!coverSelect.length) { return; }

            var availableOptions = 0;

            coverSelect.find('option').each(function() {
                var categoryId = ($(this).data('category-id') || '').toString();
                var isPlaceholder = $(this).val() === '';
                var isSelected = $(this).is(':selected');
                var isAvailable = isPlaceholder || categoryIds.length === 0 || categoryIds.indexOf(categoryId) !== -1 || isSelected;

                $(this).prop('disabled', !isAvailable);

                if (!isPlaceholder && isAvailable) {
                    availableOptions++;
                }
            });

            var hasCoverGap = categoryIds.length > 0 && availableOptions === 0;
            $('#catalog-cover-empty-tip').toggleClass('d-none', !hasCoverGap);
            if ($.fn.selectpicker) { coverSelect.selectpicker('refresh'); }
        }

        function refreshLetterIntroCategoryOptions() {
            var categoryIds = ($('#catalog-category').val() || []).map(String);

            $('.letter-intro-ad-category').each(function() {
                var categorySelect = $(this);

                categorySelect.find('option').each(function() {
                    var optionValue = ($(this).val() || '').toString();
                    var isPlaceholder = optionValue === '';
                    var isSelected = $(this).is(':selected');
                    var shouldShow = isPlaceholder || categoryIds.length === 0 || categoryIds.indexOf(optionValue) !== -1 || isSelected;

                    $(this).prop('disabled', !shouldShow);
                });
            });

            if ($.fn.selectpicker) { $('.letter-intro-ad-category').selectpicker('refresh'); }
        }

        function refreshLetterIntroAdLetterOptions() {
            $('.letter-intro-ad-row').each(function() {
                var row = $(this);
                var categoryId = String(row.find('.letter-intro-ad-category').val() || '');
                var availableLetters = categoryLetterMap[categoryId];
                var letterSelect = row.find('select[name="letter_intro_ad_letters[]"]');

                letterSelect.find('option').each(function() {
                    var optionValue = ($(this).val() || '').toString();
                    var isSelected = $(this).is(':selected');
                    var isAvailable = !availableLetters || availableLetters.indexOf(optionValue) !== -1 || isSelected;

                    $(this).prop('disabled', !isAvailable);
                });

                row.toggleClass('letter-intro-ad-row-empty', !!availableLetters && availableLetters.length === 0);
            });

            if ($.fn.selectpicker) { $('.letter-intro-ad-row select[name="letter_intro_ad_letters[]"]').selectpicker('refresh'); }
        }

        // El campo oculto se mantiene siempre sincronizado con el Set, de modo que el envio
        // no depende de un hook de submit.
        function refreshSelectionUi() {
            var selected = selectedIds.size;

            $('#catalog-product-ids').val(Array.from(selectedIds).join(','));
            $('#selected-products-count').text(selected);
            $('#loaded-products-count').text(picker.loaded);
            $('#unavailable-products-count').text(Math.max(0, picker.total - picker.selectableTotal));
            $('#generate-catalog, #generate-catalog-bottom').prop('disabled', selected === 0);
            $('#catalog-submit-hint').text(selected > 0 ? selected + ' ' + catalogMessages.productsSelected : catalogMessages.selectProductsHint);
            $('.submit-label').text(generateCatalogText);
            $('#select-all-matching-label').text(picker.selectableTotal > 0
                ? 'Seleccionar los ' + picker.selectableTotal
                : 'Seleccionar todos');
        }

        function productListShell() {
            return '<div class="table-responsive"><table class="table table-hover mb-0 catalog-product-table">' +
                '<thead><tr><th width="58">Sel.</th><th>Producto</th><th width="180" class="text-right">Precio</th></tr></thead>' +
                '<tbody id="catalog-product-rows"></tbody></table></div>';
        }

        // Los productos llegan ya ordenados por categoria y nombre, asi que los encabezados
        // de grupo se emiten cuando cambia la categoria o la letra.
        function appendProductRows(products) {
            var html = '';

            products.forEach(function(product) {
                if (product.category_id !== picker.lastCategoryId) {
                    picker.lastCategoryId = product.category_id;
                    picker.lastLetter = null;
                    html += '<tr class="catalog-category-row"><td colspan="3"><i class="las la-folder-open"></i> ' + escapeHtml(product.category_name) + '</td></tr>';
                }

                if (product.letter !== picker.lastLetter) {
                    picker.lastLetter = product.letter;
                    html += '<tr class="catalog-letter-row"><td colspan="3">' + escapeHtml(product.letter) + '</td></tr>';
                }

                var disabled = product.is_disabled ? ' disabled' : '';
                var checked = (!product.is_disabled && selectedIds.has(product.id)) ? ' checked' : '';
                var rowClass = product.is_disabled ? 'catalog-product-row opacity-60' : 'catalog-product-row catalog-product-row-selectable';
                var checkboxId = 'catalog-product-' + product.category_id + '-' + product.id;

                html += '<tr class="' + rowClass + '">';
                html += '<td class="align-middle"><label class="aiz-checkbox mb-0' + (product.is_disabled ? ' aiz-checkbox-disabled' : '') + '"><input type="checkbox" id="' + checkboxId + '" class="catalog-product-checkbox" data-product-id="' + escapeHtml(product.id) + '"' + disabled + checked + '><span class="aiz-square-check"></span></label></td>';
                html += '<td class="align-middle"><label class="mb-0 d-block' + (product.is_disabled ? '' : ' c-pointer') + '" for="' + checkboxId + '"><span class="d-block fw-600 text-dark" style="white-space: normal; word-break: break-word;">' + escapeHtml(product.name) + '</span><small class="text-muted">ID: ' + escapeHtml(product.id) + '</small>';

                if (product.is_disabled) {
                    html += '<span class="badge badge-inline badge-soft-danger ml-2">Precio en cero</span>';
                }

                html += '</label></td><td class="align-middle text-right fw-600">' + escapeHtml(product.price) + '</td></tr>';
            });

            $('#catalog-product-rows').append(html);
            picker.loaded += products.length;
        }

        function resetPickerChrome() {
            $('#catalog-product-search').addClass('d-none');
            $('#catalog-bulk-actions').addClass('d-none');
            $('#catalog-load-more-wrap').addClass('d-none');
        }

        function loadProducts(append) {
            var categoryIds = $('#catalog-category').val() || [];

            refreshCategorySummary();
            refreshCoverImageOptions();
            refreshLetterIntroCategoryOptions();

            if (categoryIds.length === 0) {
                picker.token++;
                picker.loading = false;
                picker.page = 0;
                picker.total = 0;
                picker.selectableTotal = 0;
                picker.loaded = 0;
                picker.hasMore = false;
                categoryLetterMap = {};
                resetPickerChrome();
                $('#catalog-products').html(productEmptyState('las la-box-open', catalogMessages.selectCategoriesTitle, catalogMessages.selectCategoriesBody));
                refreshSelectionUi();
                refreshLetterIntroAdLetterOptions();
                return;
            }

            if (append && picker.loading) { return; }

            picker.loading = true;
            var token = ++picker.token;

            if (append) {
                $('#catalog-load-more').prop('disabled', true);
            } else {
                picker.page = 0;
                picker.loaded = 0;
                picker.lastCategoryId = null;
                picker.lastLetter = null;
                $('#catalog-load-more-wrap').addClass('d-none');
                $('#catalog-products').html(productEmptyState('las la-spinner la-spin', catalogMessages.loadingProductsTitle, catalogMessages.loadingProductsBody));
            }

            $.get(CATEGORY_PRODUCTS_URL, {
                category_ids: categoryIds,
                search: picker.search,
                page: picker.page + 1,
                per_page: PRODUCTS_PER_REQUEST
            }).done(function(response) {
                if (token !== picker.token) { return; }

                picker.page = response.page;
                picker.total = response.total;
                picker.selectableTotal = response.selectable_total;
                picker.hasMore = response.has_more;

                if (response.letters_by_category) {
                    categoryLetterMap = response.letters_by_category;
                }

                if (!append) {
                    $('#catalog-product-search').removeClass('d-none');

                    if (response.total === 0) {
                        $('#catalog-bulk-actions').addClass('d-none');
                        $('#catalog-products').html(productEmptyState('las la-search', catalogMessages.noProductsTitle, catalogMessages.noProductsBody));
                        refreshSelectionUi();
                        refreshLetterIntroAdLetterOptions();
                        return;
                    }

                    $('#catalog-bulk-actions').removeClass('d-none');
                    $('#catalog-products').html(productListShell());
                }

                appendProductRows(response.products || []);
                $('#catalog-load-more-wrap').toggleClass('d-none', !picker.hasMore);
                $('#catalog-load-more-hint').text('Mostrando ' + picker.loaded + ' de ' + picker.total + ' productos');
                refreshSelectionUi();
                refreshLetterIntroAdLetterOptions();
            }).fail(function() {
                if (token !== picker.token) { return; }

                if (!append) {
                    resetPickerChrome();
                    $('#catalog-products').html(productEmptyState('las la-exclamation-circle', catalogMessages.loadErrorTitle, catalogMessages.loadErrorBody));
                }

                refreshSelectionUi();
            }).always(function() {
                // Se libera siempre, no solo para la peticion vigente: si se descarta por
                // token, dejar el flag arriba bloquearia cualquier carga posterior.
                picker.loading = false;
                $('#catalog-load-more').prop('disabled', false);
            });
        }

        // Fases que el renderer reporta. Solo "paginating" conoce el total de paginas; el
        // resto se muestra como barra indeterminada.
        var PHASE_LABELS = {
            loading:    'Cargando productos',
            rendering:  'Armando el contenido',
            paginating: 'Maquetando paginas',
            writing:    'Guardando el PDF'
        };

        function renderCatalogProgress(row, data) {
            var wrap = row.find('.catalog-progress');
            var isPending = data.status === 'queued' || data.status === 'processing';

            if (!isPending) {
                wrap.addClass('d-none').removeClass('catalog-progress-indeterminate');
                wrap.find('.catalog-progress-fill').css('width', '0');
                wrap.find('.catalog-progress-text').text('');
                return;
            }

            wrap.removeClass('d-none');

            var progress = data.progress || null;
            var phase = progress ? progress.phase : null;
            var total = progress ? Number(progress.total_pages || 0) : 0;
            var done = progress ? Number(progress.done_pages || 0) : 0;

            if (phase === 'paginating' && total > 0) {
                // done_pages puede pasarse del total si una pagina desborda; se acota para
                // que la barra no supere el 100%.
                var capped = Math.min(done, total);
                var percent = Math.round((capped / total) * 100);

                wrap.removeClass('catalog-progress-indeterminate');
                wrap.find('.catalog-progress-fill').css('width', percent + '%');
                wrap.find('.catalog-progress-text').text('Pagina ' + capped + ' de ' + total + ' (' + percent + '%)');
                return;
            }

            wrap.addClass('catalog-progress-indeterminate');
            wrap.find('.catalog-progress-fill').css('width', '');
            wrap.find('.catalog-progress-text').text(
                phase && PHASE_LABELS[phase] ? PHASE_LABELS[phase] : 'Preparando la generacion'
            );
        }

        function renderCatalogStatus(id, data) {
            var row = $('.catalog-list-row[data-catalog-id="' + id + '"]');

            if (!row.length) { return; }

            var meta = STATUS_LABELS[data.status] || STATUS_LABELS.ready;
            var isPending = data.status === 'queued' || data.status === 'processing';

            row.find('.catalog-status')
                .attr('class', 'catalog-status ' + meta.cls)
                .html('<i class="' + meta.icon + '"></i> ' + meta.text);

            renderCatalogProgress(row, data);

            var error = row.find('.catalog-status-error');

            if (data.status === 'failed' && data.status_message) {
                error.removeClass('d-none').text(data.status_message);
            } else {
                error.addClass('d-none').text('');
            }

            if (typeof data.products_count !== 'undefined' && data.products_count !== null) {
                row.find('.catalog-products-count').text(data.products_count);
            }

            row.find('.catalog-download').toggleClass('disabled', !data.has_file);
            row.find('.catalog-regenerate').prop('disabled', isPending);
        }

        function hasPendingCatalogs() {
            return Object.keys(catalogStatuses).some(function(id) {
                var status = (catalogStatuses[id] || {}).status;
                return status === 'queued' || status === 'processing';
            });
        }

        function scheduleStatusPoll() {
            if (statusTimer) {
                clearTimeout(statusTimer);
                statusTimer = null;
            }

            if (!hasPendingCatalogs()) { return; }

            statusTimer = setTimeout(pollCatalogStatuses, STATUS_POLL_MS);
        }

        function pollCatalogStatuses() {
            $.get(CATALOG_STATUSES_URL).done(function(map) {
                Object.keys(map || {}).forEach(function(id) {
                    catalogStatuses[id] = map[id];
                    renderCatalogStatus(id, map[id]);
                });
            }).always(scheduleStatusPoll);
        }

        $('#catalog-category').on('change', function() { loadProducts(false); });
        $('#catalog-load-more').on('click', function() { loadProducts(true); });

        $('#catalog-product-search').on('input', function() {
            var value = $(this).val() || '';

            if (searchTimer) { clearTimeout(searchTimer); }

            searchTimer = setTimeout(function() {
                picker.search = value.trim();
                loadProducts(false);
            }, 350);
        });

        $(document).on('change', '.catalog-product-checkbox', function() {
            var id = String($(this).data('product-id'));

            if (this.checked) {
                selectedIds.add(id);
            } else {
                selectedIds.delete(id);
            }

            refreshSelectionUi();
        });

        $(document).on('click', '.catalog-product-row-selectable', function(e) {
            if ($(e.target).is('input, label, span, small')) { return; }
            var checkbox = $(this).find('.catalog-product-checkbox');
            checkbox.prop('checked', !checkbox.prop('checked')).trigger('change');
        });

        // Selecciona todo lo que coincide con el filtro actual, no solo lo que esta pintado:
        // los ids los devuelve el servidor en una sola consulta.
        $('#select-all-matching').on('click', function() {
            var categoryIds = $('#catalog-category').val() || [];

            if (categoryIds.length === 0) { return; }

            var button = $(this);
            button.prop('disabled', true);

            $.get(CATEGORY_PRODUCTS_URL, {
                category_ids: categoryIds,
                search: picker.search,
                mode: 'ids'
            }).done(function(response) {
                (response.ids || []).forEach(function(id) { selectedIds.add(String(id)); });

                $('.catalog-product-checkbox').each(function() {
                    if (!this.disabled && selectedIds.has(String($(this).data('product-id')))) {
                        this.checked = true;
                    }
                });

                refreshSelectionUi();
            }).always(function() {
                button.prop('disabled', false);
            });
        });

        $('#clear-selection').on('click', function() {
            selectedIds.clear();
            $('.catalog-product-checkbox').prop('checked', false);
            refreshSelectionUi();
        });

        $('#catalog-form').on('submit', function() {
            $('#catalog-product-ids').val(Array.from(selectedIds).join(','));
            $('#generate-catalog, #generate-catalog-bottom').prop('disabled', true);
            $('.submit-label').text('Enviando...');
        });


        $('#add-advertising-row').on('click', function() {
            $('#advertising-table tbody').append(advertisingRowTemplate());
            if ($.fn.selectpicker) { $('.aiz-selectpicker').selectpicker('refresh'); }
        });

        $('#add-letter-intro-ad-row').on('click', function() {
            $('#letter-intro-ad-table tbody').append(letterIntroAdRowTemplate());
            refreshLetterIntroCategoryOptions();
            refreshLetterIntroAdLetterOptions();
            if ($.fn.selectpicker) { $('.aiz-selectpicker').selectpicker('refresh'); }
        });

        $(document).on('change', '.letter-intro-ad-category', function() {
            refreshLetterIntroAdLetterOptions();
        });

        $(document).on('click', '.remove-letter-intro-ad-row', function() {
            if ($('.letter-intro-ad-row').length === 1) {
                var row = $(this).closest('.letter-intro-ad-row');
                row.find('.selected-files').val('');
                row.find('.file-amount').text('Elegir archivo');
                row.find('.file-preview').empty();
                row.find('select').val('');
                row.find('select[name="letter_intro_ad_letters[]"]').val('A');
                row.find('select[name="letter_intro_ad_orders[]"]').val('1');
                if ($.fn.selectpicker) { $('.aiz-selectpicker').selectpicker('refresh'); }
                return;
            }

            $(this).closest('.letter-intro-ad-row').remove();
        });

        $(document).on('click', '.remove-advertising-row', function() {
            if ($('.advertising-row').length === 1) {
                var row = $(this).closest('.advertising-row');
                row.find('.selected-files').val('');
                row.find('.file-amount').text('Elegir archivo');
                row.find('.file-preview').empty();
                row.find('select').val('A');
                if ($.fn.selectpicker) { $('.aiz-selectpicker').selectpicker('refresh'); }
                return;
            }
            $(this).closest('.advertising-row').remove();
        });

        $('#catalog-list-search').on('input', function() {
            var search = ($(this).val() || '').toLowerCase().trim();
            $('.catalog-list-row').each(function() {
                var haystack = ($(this).data('catalog-search') || '').toString();
                $(this).toggle(search === '' || haystack.indexOf(search) !== -1);
            });
            $('#catalogs-no-results').toggle($('.catalog-list-row:visible').length === 0);
        });

        Object.keys(catalogStatuses).forEach(function(id) {
            renderCatalogStatus(id, catalogStatuses[id]);
        });
        scheduleStatusPoll();

        refreshCategorySummary();
        refreshCoverImageOptions();
        refreshLetterIntroCategoryOptions();
        refreshSelectionUi();
        if (($('#catalog-category').val() || []).length > 0) { loadProducts(false); }
    </script>
@endsection
