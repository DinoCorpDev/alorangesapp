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

        $coverTitlePosition = old('cover_title_position', $settings['cover_title_position'] ?? 'middle');
        $productsPerPage = (int) old('products_per_page', $settings['products_per_page'] ?? 12) === 20 ? 20 : 12;
        $catalogMessages = [
            'categoriesSelected' => translate('categories selected'),
            'productsSelected' => translate('products selected'),
            'selectProductsHint' => translate('Select products to enable this button'),
            'selectCategoriesTitle' => translate('Select categories to load products'),
            'selectCategoriesBody' => translate('The product list will appear here grouped by category and letter'),
            'loadingProductsTitle' => translate('Loading products'),
            'loadingProductsBody' => translate('Please wait while the category products are loaded'),
            'noProductsTitle' => translate('No products found for the selected categories'),
            'noProductsBody' => translate('Try selecting a different category'),
            'loadErrorTitle' => translate('Products could not be loaded'),
            'loadErrorBody' => translate('Please try again or review the selected categories'),
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
                    <h1 class="h3">{{ translate('PDF Catalogs') }}</h1>
                    <p class="mb-0 text-muted">
                        {{ translate('Create, edit and download product catalogs from one place') }}
                    </p>
                </div>
                <div class="col-lg-5 text-lg-right mt-3 mt-lg-0">
                    @if ($isEdit)
                        <a href="{{ route('product_catalogs.index') }}" class="btn btn-soft-secondary mr-2">
                            <i class="las la-plus"></i>
                            {{ translate('Create New Catalog') }}
                        </a>
                    @endif
                    <a class="btn btn-soft-primary" href="{{ $isEdit ? route('product_catalogs.configuration', $catalog['id']) : route('product_catalogs.configuration.defaults') }}">
                        <i class="las la-cog"></i>
                        {{ translate('Catalog Configuration') }}
                    </a>
                </div>
            </div>
        </div>

        <div class="catalog-hero mb-3">
            <div class="catalog-flow">
                <div class="catalog-flow-step">
                    <span>1</span>
                    <div>
                        <strong>{{ translate('Cover') }}</strong>
                        <small>{{ translate('Image, title and advisor') }}</small>
                    </div>
                </div>
                <div class="catalog-flow-step">
                    <span>2</span>
                    <div>
                        <strong>{{ translate('Categories') }}</strong>
                        <small>{{ translate('Choose the product groups') }}</small>
                    </div>
                </div>
                <div class="catalog-flow-step">
                    <span>3</span>
                    <div>
                        <strong>{{ translate('Products') }}</strong>
                        <small>{{ translate('Search and select items') }}</small>
                    </div>
                </div>
                <div class="catalog-flow-step">
                    <span>4</span>
                    <div>
                        <strong>{{ translate('PDF') }}</strong>
                        <small>{{ translate('Generate or update the file') }}</small>
                    </div>
                </div>
            </div>
            <p class="catalog-helper mb-0">
                {{ translate('Start by choosing categories. Products load automatically and you can select them one by one or all visible results at once.') }}
            </p>
        </div>

        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <div>
                    <h5 class="mb-0 h6">{{ $isEdit ? translate('Edit Catalog') : translate('Create Catalog') }}</h5>
                    <small class="text-muted">
                        {{ $isEdit ? translate('Adjust the catalog details and regenerate the PDF') : translate('Complete the sections below to generate a new PDF') }}
                    </small>
                </div>
                <span class="catalog-badge">{{ $isEdit ? translate('Editing') : translate('New') }}</span>
            </div>
            <div class="card-body">
                <form action="{{ $isEdit ? route('product_catalogs.update', $catalog['id']) : route('product_catalogs.store') }}" method="POST" id="catalog-form">
                    @csrf
                    @if ($isEdit)
                        @method('PUT')
                    @endif

                    <div class="catalog-section catalog-section-soft">
                        <div class="catalog-section-title">
                            <div>
                                <h6>{{ translate('Catalog Cover') }}</h6>
                                <p>{{ translate('This information appears on the first page of the catalog') }}</p>
                            </div>
                        </div>

                        <div class="row gutters-10">
                            <div class="col-lg-4">
                                <div class="form-group mb-lg-0">
                                    <label>{{ translate('First Catalog Image') }}</label>
                                    <div class="input-group" data-toggle="aizuploader" data-type="image">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse') }}</div>
                                        </div>
                                        <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                        <input type="hidden" name="cover_image" class="selected-files" value="{{ old('cover_image', $settings['cover_image'] ?? '') }}">
                                    </div>
                                    <div class="file-preview box sm"></div>
                                </div>
                            </div>
                            <div class="col-lg-8">
                                <div class="row gutters-10">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>{{ translate('Title Position') }}</label>
                                            <select class="form-control aiz-selectpicker" name="cover_title_position">
                                                <option value="top" @if ($coverTitlePosition === 'top') selected @endif>{{ translate('Top') }}</option>
                                                <option value="middle" @if ($coverTitlePosition === 'middle') selected @endif>{{ translate('Middle') }}</option>
                                                <option value="bottom" @if ($coverTitlePosition === 'bottom') selected @endif>{{ translate('Bottom') }}</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>{{ translate('Advisor Name') }}</label>
                                            <input type="text" class="form-control" name="advisor_name" value="{{ old('advisor_name', $settings['advisor_name'] ?? '') }}" placeholder="{{ translate('Advisor Name') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>{{ translate('Advisor Phone') }}</label>
                                            <input type="text" class="form-control" name="advisor_phone" value="{{ old('advisor_phone', $settings['advisor_phone'] ?? '') }}" placeholder="{{ translate('Advisor Phone') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-md-0">
                                            <label>{{ translate('Advisor Email') }} 1</label>
                                            <input type="email" class="form-control" name="advisor_email_1" value="{{ old('advisor_email_1', $settings['advisor_email_1'] ?? '') }}" placeholder="correo@ejemplo.com">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-0">
                                            <label>{{ translate('Advisor Email') }} 2</label>
                                            <input type="email" class="form-control" name="advisor_email_2" value="{{ old('advisor_email_2', $settings['advisor_email_2'] ?? '') }}" placeholder="correo@ejemplo.com">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="catalog-section">
                        <div class="catalog-section-title">
                            <div>
                                <h6>{{ translate('Catalog Details') }}</h6>
                                <p>{{ translate('Choose one or more categories and name the catalog') }}</p>
                            </div>
                            <small class="text-muted" id="selected-categories-count">0 {{ translate('categories selected') }}</small>
                        </div>

                        <div class="row gutters-10 align-items-end">
                            <div class="col-lg-5">
                                <div class="form-group mb-lg-0">
                                    <label>{{ translate('Categories') }}</label>
                                    <select class="form-control aiz-selectpicker" name="category_ids[]" id="catalog-category" data-live-search="true" data-actions-box="true" multiple required>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}" @if (in_array((string) $category->id, $selectedCategoryIds)) selected @endif>{{ $category->getTranslation('name') }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group mb-lg-0">
                                    <label>{{ translate('Catalog Name') }}</label>
                                    <input type="text" class="form-control" name="name" value="{{ old('name', $catalog['name'] ?? '') }}" placeholder="{{ translate('Catalog Name') }}">
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group mb-0">
                                    <button type="submit" class="btn btn-primary btn-block" id="generate-catalog" disabled>
                                        <i class="las la-file-pdf"></i>
                                        <span class="submit-label">{{ $isEdit ? translate('Update PDF Catalog') : translate('Generate PDF Catalog') }}</span>
                                    </button>
                                    <small class="text-muted d-block mt-2" id="catalog-submit-hint">{{ translate('Select products to enable this button') }}</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="catalog-section catalog-section-soft">
                        <div class="catalog-section-title">
                            <div>
                                <h6>{{ translate('Advertising') }}</h6>
                                <p>Imagen destacada junto a los productos de la letra seleccionada</p>
                            </div>
                            <button type="button" class="btn btn-soft-primary btn-sm" id="add-advertising-row">
                                <i class="las la-plus"></i>
                                {{ translate('Add Advertising') }}
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered mb-0" id="advertising-table">
                                <thead>
                                    <tr>
                                        <th>{{ translate('Advertising Image') }}</th>
                                        <th width="190">Mostrar con la letra</th>
                                        <th width="80" class="text-center">{{ translate('Options') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($advertisingRows as $advertisingRow)
                                        <tr class="advertising-row">
                                            <td>
                                                <div class="input-group" data-toggle="aizuploader" data-type="image">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse') }}</div>
                                                    </div>
                                                    <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                                    <input type="hidden" name="advertising_images[]" class="selected-files" value="{{ $advertisingRow['image'] ?? '' }}">
                                                </div>
                                                <div class="file-preview box sm"></div>
                                            </td>
                                            <td>
                                                <select class="form-control aiz-selectpicker" name="advertising_letters[]">
                                                    @foreach ($advertisingLetters as $advertisingLetter)
                                                        <option value="{{ $advertisingLetter }}" @if (($advertisingRow['letter'] ?? 'A') === $advertisingLetter) selected @endif>{{ $advertisingLetter }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-soft-danger btn-icon btn-circle btn-sm remove-advertising-row" title="{{ translate('Delete') }}">
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
                                <h6>{{ translate('Products') }}</h6>
                                <p>{{ translate('Only products with price greater than zero can be selected') }}</p>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold mb-2">Productos por página</label>
                            <div class="catalog-density-options">
                                <label class="catalog-density-option">
                                    <input type="radio" name="products_per_page" value="12" @checked($productsPerPage === 12)>
                                    <span class="catalog-density-card">
                                        <span class="catalog-density-icon catalog-density-icon-twelve" aria-hidden="true">
                                            @for ($i = 0; $i < 12; $i++)<span></span>@endfor
                                        </span>
                                        <span class="catalog-density-copy">
                                            <strong>12 productos</strong>
                                            <small>Tarjetas amplias e imágenes más grandes</small>
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
                                            <small>Formato compacto para catálogos extensos</small>
                                        </span>
                                    </span>
                                </label>
                            </div>
                        </div>

                        <div class="catalog-stat-grid">
                            <div class="catalog-stat">
                                <strong id="selected-products-count">0</strong>
                                <span>{{ translate('Selected products') }}</span>
                            </div>
                            <div class="catalog-stat">
                                <strong id="loaded-products-count">0</strong>
                                <span>{{ translate('Loaded products') }}</span>
                            </div>
                            <div class="catalog-stat">
                                <strong id="unavailable-products-count">0</strong>
                                <span>{{ translate('Unavailable products') }}</span>
                            </div>
                        </div>

                        <div class="catalog-product-toolbar">
                            <input type="text" class="form-control form-control-sm d-none" id="catalog-product-search" placeholder="{{ translate('Search products by name, ID or category') }}">
                            <label class="aiz-checkbox mb-0 fw-600">
                                <input type="checkbox" id="select-all-products" disabled>
                                <span class="aiz-square-check"></span>
                                <span>{{ translate('Select All Visible') }}</span>
                            </label>
                        </div>

                        <div id="catalog-products" class="catalog-empty-state">
                            <i class="las la-box-open"></i>
                            <strong>{{ translate('Select categories to load products') }}</strong>
                            <div>{{ translate('The product list will appear here grouped by category and letter') }}</div>
                        </div>
                    </div>

                    <div class="catalog-sticky-submit">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <small class="text-muted">
                                    {{ translate('Review the selected products before generating the PDF') }}
                                </small>
                            </div>
                            <div class="col-md-4 text-md-right mt-2 mt-md-0">
                                <button type="submit" class="btn btn-primary" id="generate-catalog-bottom" disabled>
                                    <i class="las la-file-pdf"></i>
                                    <span class="submit-label">{{ $isEdit ? translate('Update PDF Catalog') : translate('Generate PDF Catalog') }}</span>
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
                        <h5 class="mb-0 h6">{{ translate('Generated Catalogs') }}</h5>
                        <small class="text-muted">{{ translate('Download, edit or delete existing catalog files') }}</small>
                    </div>
                    <div class="col-md-6 mt-3 mt-md-0">
                        <input type="text" class="form-control form-control-sm" id="catalog-list-search" placeholder="{{ translate('Search generated catalogs') }}">
                    </div>
                </div>
            </div>
            <div class="card-body">
                <table class="table aiz-table mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ translate('Name') }}</th>
                            <th data-breakpoints="lg">{{ translate('Categories') }}</th>
                            <th data-breakpoints="lg">{{ translate('Products') }}</th>
                            <th data-breakpoints="lg">{{ translate('Created At') }}</th>
                            <th class="text-right">{{ translate('Options') }}</th>
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
                                        <small class="d-block text-muted">{{ translate('Updated') }}: {{ $catalogItem['updated_at'] }}</small>
                                    @endif
                                </td>
                                <td>{{ $catalogItem['category_name'] }}</td>
                                <td><span class="badge badge-inline badge-soft-info">{{ $catalogItem['products_count'] }}</span></td>
                                <td>{{ $catalogItem['created_at'] }}</td>
                                <td class="text-right catalog-actions">
                                    <a class="btn btn-soft-info btn-sm" href="{{ route('product_catalogs.edit', $catalogItem['id']) }}" title="{{ translate('Edit') }}">
                                        <i class="las la-edit"></i>
                                        {{ translate('Edit') }}
                                    </a>
                                    <a class="btn btn-soft-primary btn-sm" href="{{ route('product_catalogs.download', $catalogItem['id']) }}" title="{{ translate('Download') }}">
                                        <i class="las la-download"></i>
                                        {{ translate('Download') }}
                                    </a>
                                    <form action="{{ route('product_catalogs.destroy', $catalogItem['id']) }}" method="POST" class="d-inline-block" onsubmit="return confirm('{{ translate('Are you sure you want to delete this catalog?') }}');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-soft-danger btn-sm" title="{{ translate('Delete') }}">
                                            <i class="las la-trash"></i>
                                            {{ translate('Delete') }}
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
                                        <strong>{{ translate('No catalogs found') }}</strong>
                                        <div>{{ translate('Generated catalogs will appear here') }}</div>
                                    </div>
                                </td>
                            </tr>
                        @else
                            <tr id="catalogs-no-results" class="d-none">
                                <td colspan="6" class="text-center text-muted py-4">{{ translate('No catalogs match your search') }}</td>
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
        var generateCatalogText = @json($isEdit ? translate('Update PDF Catalog') : translate('Generate PDF Catalog'));
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
                '<td><div class="input-group" data-toggle="aizuploader" data-type="image"><div class="input-group-prepend"><div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse') }}</div></div><div class="form-control file-amount">{{ translate('Choose File') }}</div><input type="hidden" name="advertising_images[]" class="selected-files" value=""></div><div class="file-preview box sm"></div></td>' +
                '<td><select class="form-control aiz-selectpicker" name="advertising_letters[]">' + options + '</select></td>' +
                '<td class="text-center"><button type="button" class="btn btn-soft-danger btn-icon btn-circle btn-sm remove-advertising-row" title="{{ translate('Delete') }}"><i class="las la-trash"></i></button></td>' +
            '</tr>';
        }

        function refreshCategorySummary() {
            var count = ($('#catalog-category').val() || []).length;
            $('#selected-categories-count').text(count + ' ' + catalogMessages.categoriesSelected);
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
            if (categoryGroups.length === 0) {
                $('#catalog-products').html(productEmptyState('las la-search', catalogMessages.noProductsTitle, catalogMessages.noProductsBody));
                $('#select-all-products').prop('checked', false).prop('disabled', true);
                $('#catalog-product-search').addClass('d-none').val('');
                refreshGenerateButton();
                return;
            }

            var html = '<div class="table-responsive"><table class="table table-hover mb-0 catalog-product-table"><thead><tr><th width="58">{{ translate('Select') }}</th><th>{{ translate('Product Name') }}</th><th width="180" class="text-right">{{ translate('Price') }}</th></tr></thead><tbody>';

            categoryGroups.forEach(function(categoryGroup) {
                var groups = {};

                categoryGroup.products.forEach(function(product) {
                    var letter = (product.name || '#').trim().charAt(0).toUpperCase();
                    if (!letter.match(/[A-Z0-9]/)) { letter = '#'; }
                    groups[letter] = groups[letter] || [];
                    groups[letter].push(product);
                });

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
                        if (product.is_disabled) { html += '<span class="badge badge-inline badge-soft-danger ml-2">{{ translate('Price is zero') }}</span>'; }
                        html += '</label></td><td class="align-middle text-right fw-600">' + escapeHtml(product.price) + '</td></tr>';
                    });
                });
            });

            html += '<tr id="catalog-no-search-results" class="d-none"><td colspan="3" class="text-center text-muted py-4">{{ translate('No products match your search') }}</td></tr></tbody></table></div>';
            $('#catalog-products').html(html);
            $('#catalog-product-search').removeClass('d-none').val('');
            refreshSelectAllState();
            refreshGenerateButton();
        }

        function loadCatalogProducts() {
            var categoryIds = $('#catalog-category').val() || [];
            refreshCategorySummary();
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
        $(document).on('click', '.remove-advertising-row', function() {
            if ($('.advertising-row').length === 1) {
                var row = $(this).closest('.advertising-row');
                row.find('.selected-files').val('');
                row.find('.file-amount').text('{{ translate('Choose File') }}');
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
        refreshGenerateButton();
        if (($('#catalog-category').val() || []).length > 0) { loadCatalogProducts(); }
    </script>
@endsection
