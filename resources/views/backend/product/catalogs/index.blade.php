@extends('backend.layouts.app')

@section('content')
    @php
        $isEdit = $mode === 'edit';
        $selectedCategoryIds = collect(old('category_ids', $catalog['category_ids'] ?? []))->map(fn($id) => (string) $id)->all();
        $selectedProductIds = collect(old('product_ids', $catalog['product_ids'] ?? []))->map(fn($id) => (string) $id)->all();
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
                ];
            })->values()->all()
            : ($settings['letter_intro_ads'] ?? []);

        if (empty($letterIntroAdRows)) {
            $letterIntroAdRows = [['image' => '', 'category_id' => '', 'letter' => 'A']];
        }

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
                                    <small class="text-muted d-block mt-1">{{ $fullPageImageHint }} — la imagen es obligatoria para mostrar la pagina final.</small>
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
                                <p>Imagen a pagina completa que se muestra una sola vez antes de iniciar la letra elegida.</p>
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

                    <div class="catalog-section">
                        <div class="catalog-section-title">
                            <div>
                                <div class="section-kicker"><span>5</span> Productos</div>
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
                                <span>Productos cargados</span>
                            </div>
                            <div class="catalog-stat">
                                <strong id="unavailable-products-count">0</strong>
                                <span>No disponibles</span>
                            </div>
                        </div>

                        <div class="catalog-product-toolbar">
                            <input type="text" class="form-control form-control-sm d-none" id="catalog-product-search" placeholder="Buscar productos por nombre, ID o categoria">
                            <label class="aiz-checkbox mb-0 fw-600">
                                <input type="checkbox" id="select-all-products" disabled>
                                <span class="aiz-square-check"></span>
                                <span>Seleccionar visibles</span>
                            </label>
                        </div>

                        <div id="catalog-products" class="catalog-empty-state">
                            <i class="las la-box-open"></i>
                            <strong>Selecciona categorias para cargar productos</strong>
                            <div>La lista aparecera agrupada por categoria y letra</div>
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
                            <th data-breakpoints="lg">Creado</th>
                            <th class="text-right">Opciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($catalogs as $key => $catalogItem)
                            @php
                                $catalogSearch = strtolower(($catalogItem['name'] ?? '') . ' ' . ($catalogItem['category_name'] ?? '') . ' ' . ($catalogItem['created_at'] ?? ''));
                            @endphp
                            <tr class="catalog-list-row" data-catalog-search="{{ $catalogSearch }}">
                                <td>{{ $key + 1 }}</td>
                                <td>
                                    <span class="fw-700 text-dark">{{ $catalogItem['name'] }}</span>
                                    @if (! empty($catalogItem['updated_at']))
                                        <small class="d-block text-muted">Actualizado: {{ $catalogItem['updated_at'] }}</small>
                                    @endif
                                </td>
                                <td>{{ $catalogItem['category_name'] }}</td>
                                <td><span class="badge badge-inline badge-soft-info">{{ $catalogItem['products_count'] }}</span></td>
                                <td>{{ $catalogItem['created_at'] }}</td>
                                <td class="text-right catalog-actions">
                                    <a class="btn btn-soft-info btn-sm" href="{{ route('product_catalogs.edit', $catalogItem['id']) }}" title="Editar">
                                        <i class="las la-edit"></i>
                                        Editar
                                    </a>
                                    <a class="btn btn-soft-primary btn-sm" href="{{ route('product_catalogs.download', $catalogItem['id']) }}" title="Descargar">
                                        <i class="las la-download"></i>
                                        Descargar
                                    </a>
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
                                <td colspan="6" class="text-center py-4">
                                    <div class="catalog-empty-state">
                                        <i class="las la-file-pdf"></i>
                                        <strong>No hay catalogos generados</strong>
                                        <div>Los catalogos creados apareceran aqui.</div>
                                    </div>
                                </td>
                            </tr>
                        @else
                            <tr id="catalogs-no-results" class="d-none">
                                <td colspan="6" class="text-center text-muted py-4">No hay catalogos que coincidan con la busqueda</td>
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
        var selectedProductIds = @json($selectedProductIds);
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

        function escapeHtml(value) {
            return $('<div>').text(value || '').html();
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
                '<td class="text-center"><button type="button" class="btn btn-soft-danger btn-icon btn-circle btn-sm remove-letter-intro-ad-row" title="Eliminar"><i class="las la-trash"></i></button></td>' +
            '</tr>';
        }

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

        function refreshGenerateButton() {
            var selected = $('.catalog-product-checkbox:checked').length;
            var loaded = $('.catalog-product-checkbox').length;
            var unavailable = $('.catalog-product-checkbox:disabled').length;
            var hasProducts = selected > 0;

            $('#selected-products-count').text(selected);
            $('#loaded-products-count').text(loaded);
            $('#unavailable-products-count').text(unavailable);
            $('#generate-catalog, #generate-catalog-bottom').prop('disabled', ! hasProducts);
            $('#catalog-submit-hint').text(hasProducts ? selected + ' ' + catalogMessages.productsSelected : catalogMessages.selectProductsHint);
            $('.submit-label').text(generateCatalogText);
        }

        function refreshSelectAllState() {
            var visibleCheckboxes = $('.catalog-product-row:visible .catalog-product-checkbox:not(:disabled)');
            var checkedVisibleCheckboxes = visibleCheckboxes.filter(':checked');
            $('#select-all-products')
                .prop('disabled', visibleCheckboxes.length === 0)
                .prop('checked', visibleCheckboxes.length > 0 && visibleCheckboxes.length === checkedVisibleCheckboxes.length);
        }

        function filterCatalogProducts() {
            var search = ($('#catalog-product-search').val() || '').toLowerCase().trim();
            $('.catalog-product-row').each(function() {
                var haystack = ($(this).data('search') || '').toString();
                $(this).toggle(search === '' || haystack.indexOf(search) !== -1);
            });
            $('.catalog-letter-row').each(function() {
                var rows = $(this).nextUntil('.catalog-letter-row, .catalog-category-row', '.catalog-product-row');
                $(this).toggle(rows.filter(':visible').length > 0);
            });
            $('.catalog-category-row').each(function() {
                var rows = $(this).nextUntil('.catalog-category-row', '.catalog-product-row');
                $(this).toggle(rows.filter(':visible').length > 0);
            });
            $('#catalog-no-search-results').toggle($('.catalog-product-row:visible').length === 0);
            refreshSelectAllState();
        }

        function renderProducts(categoryGroups) {
            categoryLetterMap = {};

            if (categoryGroups.length === 0) {
                $('#catalog-products').html(productEmptyState('las la-search', catalogMessages.noProductsTitle, catalogMessages.noProductsBody));
                $('#select-all-products').prop('checked', false).prop('disabled', true);
                $('#catalog-product-search').addClass('d-none').val('');
                refreshGenerateButton();
                refreshLetterIntroAdLetterOptions();
                return;
            }

            var html = '<div class="table-responsive"><table class="table table-hover mb-0 catalog-product-table"><thead><tr><th width="58">Sel.</th><th>Producto</th><th width="180" class="text-right">Precio</th></tr></thead><tbody>';

            categoryGroups.forEach(function(categoryGroup) {
                var groups = {};
                var enabledLetters = {};

                categoryGroup.products.forEach(function(product) {
                    var letter = (product.name || '#').trim().charAt(0).toUpperCase();
                    if (!letter.match(/[A-Z0-9]/)) { letter = '#'; }
                    groups[letter] = groups[letter] || [];
                    groups[letter].push(product);
                    if (!product.is_disabled) { enabledLetters[letter] = true; }
                });

                categoryLetterMap[String(categoryGroup.category_id)] = Object.keys(enabledLetters).sort();

                html += '<tr class="catalog-category-row"><td colspan="3"><i class="las la-folder-open"></i> ' + escapeHtml(categoryGroup.category_name) + '</td></tr>';

                Object.keys(groups).sort().forEach(function(letter) {
                    html += '<tr class="catalog-letter-row"><td colspan="3">' + letter + '</td></tr>';

                    groups[letter].forEach(function(product) {
                        var disabled = product.is_disabled ? ' disabled' : '';
                        var disabledClass = product.is_disabled ? ' opacity-60' : '';
                        var checked = selectedProductIds.indexOf(String(product.id)) !== -1 && !product.is_disabled ? ' checked' : '';
                        var rowClass = product.is_disabled ? ' catalog-product-row' : ' catalog-product-row catalog-product-row-selectable';
                        var checkboxId = 'catalog-product-' + categoryGroup.category_id + '-' + product.id;
                        var searchText = (product.name + ' ' + product.id + ' ' + categoryGroup.category_name).toLowerCase();

                        html += '<tr class="' + rowClass + disabledClass + '" data-search="' + escapeHtml(searchText) + '">';
                        html += '<td class="align-middle"><label class="aiz-checkbox mb-0' + (product.is_disabled ? ' aiz-checkbox-disabled' : '') + '"><input type="checkbox" id="' + checkboxId + '" class="catalog-product-checkbox" name="product_ids[]" value="' + product.id + '"' + disabled + checked + '><span class="aiz-square-check"></span></label></td>';
                        html += '<td class="align-middle"><label class="mb-0 d-block' + (product.is_disabled ? '' : ' c-pointer') + '" for="' + checkboxId + '"><span class="d-block fw-600 text-dark" style="white-space: normal; word-break: break-word;">' + escapeHtml(product.name) + '</span><small class="text-muted">ID: ' + product.id + '</small>';
                        if (product.is_disabled) { html += '<span class="badge badge-inline badge-soft-danger ml-2">Precio en cero</span>'; }
                        html += '</label></td><td class="align-middle text-right fw-600">' + escapeHtml(product.price) + '</td></tr>';
                    });
                });
            });

            html += '<tr id="catalog-no-search-results" class="d-none"><td colspan="3" class="text-center text-muted py-4">No hay productos que coincidan con la busqueda</td></tr></tbody></table></div>';
            $('#catalog-products').html(html);
            $('#catalog-product-search').removeClass('d-none').val('');
            refreshSelectAllState();
            refreshGenerateButton();
            refreshLetterIntroAdLetterOptions();
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

        function loadCatalogProducts() {
            var categoryIds = $('#catalog-category').val() || [];
            refreshCategorySummary();
            refreshCoverImageOptions();
            refreshLetterIntroCategoryOptions();
            $('#catalog-products').html(productEmptyState('las la-spinner la-spin', catalogMessages.loadingProductsTitle, catalogMessages.loadingProductsBody));
            $('#select-all-products').prop('checked', false).prop('disabled', true);
            $('#generate-catalog, #generate-catalog-bottom').prop('disabled', true);
            $('#catalog-product-search').addClass('d-none').val('');

            if (categoryIds.length === 0) {
                $('#catalog-products').html(productEmptyState('las la-box-open', catalogMessages.selectCategoriesTitle, catalogMessages.selectCategoriesBody));
                refreshGenerateButton();
                return;
            }

            $.get('{{ route('product_catalogs.category_products') }}', { category_ids: categoryIds }, function(products) {
                renderProducts(products);
            }).fail(function() {
                $('#catalog-products').html(productEmptyState('las la-exclamation-circle', catalogMessages.loadErrorTitle, catalogMessages.loadErrorBody));
                refreshGenerateButton();
            });
        }

        $('#catalog-category').on('change', loadCatalogProducts);
        $('#select-all-products').on('change', function() {
            $('.catalog-product-row:visible .catalog-product-checkbox:not(:disabled)').prop('checked', this.checked);
            refreshGenerateButton();
            refreshSelectAllState();
        });
        $(document).on('change', '.catalog-product-checkbox', function() {
            refreshGenerateButton();
            refreshSelectAllState();
        });
        $('#catalog-product-search').on('input', filterCatalogProducts);
        $(document).on('click', '.catalog-product-row-selectable', function(e) {
            if ($(e.target).is('input, label, span, small')) { return; }
            var checkbox = $(this).closest('tr').find('.catalog-product-checkbox');
            checkbox.prop('checked', !checkbox.prop('checked')).trigger('change');
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

        refreshCategorySummary();
        refreshCoverImageOptions();
        refreshLetterIntroCategoryOptions();
        refreshGenerateButton();
        if (($('#catalog-category').val() || []).length > 0) { loadCatalogProducts(); }
    </script>
@endsection
