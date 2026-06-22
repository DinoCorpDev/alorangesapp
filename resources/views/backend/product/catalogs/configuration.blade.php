@extends('backend.layouts.app')

@section('content')
    @php
        $infoRows = old('info_table_labels')
            ? collect(old('info_table_labels'))->map(function ($label, $index) {
                return [
                    'label' => $label,
                    'value' => old('info_table_values.' . $index),
                ];
            })->values()->all()
            : ($settings['info_table_rows'] ?? []);

        if (empty($infoRows) && ! empty($settings['info_page_content'])) {
            $infoRows = collect(preg_split('/\r\n|\r|\n/', $settings['info_page_content']))
                ->filter()
                ->map(function ($line) {
                    $parts = explode(':', $line, 2);
                    return [
                        'label' => trim($parts[0] ?? ''),
                        'value' => trim($parts[1] ?? $line),
                    ];
                })->values()->all();
        }

        if (empty($infoRows)) {
            $infoRows = [
                ['label' => '', 'value' => ''],
                ['label' => '', 'value' => ''],
                ['label' => '', 'value' => ''],
            ];
        }

        $fontFamilies = ['DejaVu Sans', 'Arial', 'Georgia', 'Times New Roman', 'Verdana', 'Tahoma', 'Courier New'];
        $typographyFields = [
            ['label' => translate('Product title'), 'family' => 'product_title_font_family', 'size' => 'product_title_font_size', 'default_size' => 12],
            ['label' => translate('Description'), 'family' => 'product_description_font_family', 'size' => 'product_description_font_size', 'default_size' => 10],
            ['label' => translate('Price'), 'family' => 'product_price_font_family', 'size' => 'product_price_font_size', 'default_size' => 16],
            ['label' => translate('Reference'), 'family' => 'product_reference_font_family', 'size' => 'product_reference_font_size', 'default_size' => 12],
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
                    <h1 class="h3">{{ translate('Catalog Configuration') }}</h1>
                    <p class="mb-0 text-muted">
                        {{ $catalog ? $catalog['name'] : translate('Default configuration for new catalogs') }}
                    </p>
                </div>
                <div class="col-md-5 text-md-right mt-3 mt-md-0">
                    <a href="{{ route('product_catalogs.index') }}" class="btn btn-soft-secondary">
                        <i class="las la-arrow-left"></i>
                        {{ translate('Back to PDF Catalogs') }}
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
                                <i class="las la-file-alt"></i> {{ translate('Pages') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#config-payment" role="tab">
                                <i class="las la-credit-card"></i> {{ translate('Payment') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#config-information" role="tab">
                                <i class="las la-table"></i> {{ translate('Information') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#config-products" role="tab">
                                <i class="las la-box"></i> {{ translate('Products') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#config-style" role="tab">
                                <i class="las la-palette"></i> {{ translate('Style') }}
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
                                                <h6>{{ translate('Payment page') }}</h6>
                                                <p>{{ translate('Payment image and payment blocks') }}</p>
                                            </div>
                                            <label class="aiz-checkbox mb-0">
                                                <input type="checkbox" name="show_payment_page" value="1" data-config-toggle="#payment-fields" @if (old('show_payment_page', $settings['show_payment_page'] ?? true)) checked @endif>
                                                <span class="aiz-square-check"></span>
                                                <span>{{ translate('Show') }}</span>
                                            </label>
                                        </div>
                                        <div class="form-group mb-0 compact-uploader">
                                            <label>{{ translate('Payment section image') }}</label>
                                            <div class="input-group" data-toggle="aizuploader" data-type="image">
                                                <div class="input-group-prepend">
                                                    <div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse') }}</div>
                                                </div>
                                                <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                                <input type="hidden" name="payment_page_image" class="selected-files" value="{{ old('payment_page_image', $settings['payment_page_image'] ?? '') }}">
                                            </div>
                                            <div class="file-preview box sm"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="config-panel">
                                        <div class="config-toggle-row">
                                            <div>
                                                <h6>{{ translate('Information page') }}</h6>
                                                <p>{{ translate('Information image and editable table') }}</p>
                                            </div>
                                            <label class="aiz-checkbox mb-0">
                                                <input type="checkbox" name="show_info_page" value="1" data-config-toggle="#information-fields" @if (old('show_info_page', $settings['show_info_page'] ?? true)) checked @endif>
                                                <span class="aiz-square-check"></span>
                                                <span>{{ translate('Show') }}</span>
                                            </label>
                                        </div>
                                        <div class="form-group mb-0 compact-uploader">
                                            <label>{{ translate('Information table image') }}</label>
                                            <div class="input-group" data-toggle="aizuploader" data-type="image">
                                                <div class="input-group-prepend">
                                                    <div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse') }}</div>
                                                </div>
                                                <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                                <input type="hidden" name="info_page_image" class="selected-files" value="{{ old('info_page_image', $settings['info_page_image'] ?? '') }}">
                                            </div>
                                            <div class="file-preview box sm"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="config-panel mb-lg-0">
                                        <div class="config-toggle-row">
                                            <div>
                                                <h6>{{ translate('Fourth page') }}</h6>
                                                <p>{{ translate('Optional full page image') }}</p>
                                            </div>
                                            <label class="aiz-checkbox mb-0">
                                                <input type="checkbox" name="show_page_four" value="1" data-config-toggle="#page-four-fields" @if (old('show_page_four', $settings['show_page_four'] ?? false)) checked @endif>
                                                <span class="aiz-square-check"></span>
                                                <span>{{ translate('Show') }}</span>
                                            </label>
                                        </div>
                                        <div id="page-four-fields" class="form-group mb-0 compact-uploader">
                                            <label>{{ translate('Page 4 image') }}</label>
                                            <div class="input-group" data-toggle="aizuploader" data-type="image">
                                                <div class="input-group-prepend">
                                                    <div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse') }}</div>
                                                </div>
                                                <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                                <input type="hidden" name="page_four_image" class="selected-files" value="{{ old('page_four_image', $settings['page_four_image'] ?? '') }}">
                                            </div>
                                            <div class="file-preview box sm"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="config-panel mb-0">
                                        <div class="config-toggle-row mb-0">
                                            <div>
                                                <h6>{{ translate('Product prices') }}</h6>
                                                <p>{{ translate('Show or hide product prices') }}</p>
                                            </div>
                                            <label class="aiz-checkbox mb-0">
                                                <input type="checkbox" name="show_prices" value="1" @if (old('show_prices', $settings['show_prices'] ?? true)) checked @endif>
                                                <span class="aiz-square-check"></span>
                                                <span>{{ translate('Show') }}</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="config-payment" role="tabpanel">
                            <div id="payment-fields">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="config-panel-white">
                                            <h5 class="config-section-title">{{ translate('Header') }}</h5>
                                            <p class="config-section-subtitle">{{ translate('Main payment titles') }}</p>
                                            <div class="form-group mt-3">
                                                <label>{{ translate('Payment page title') }}</label>
                                                <input type="text" class="form-control" name="payment_title" value="{{ old('payment_title', $settings['payment_title'] ?? '') }}">
                                            </div>
                                            <div class="form-group mb-0">
                                                <label>{{ translate('Main payment banner') }}</label>
                                                <input type="text" class="form-control" name="payment_delivery_title" value="{{ old('payment_delivery_title', $settings['payment_delivery_title'] ?? '') }}">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-6">
                                        <div class="config-panel-white">
                                            <h5 class="config-section-title">{{ translate('Bank transfer') }}</h5>
                                            <p class="config-section-subtitle">{{ translate('Bank or transfer details') }}</p>
                                            <div class="form-group mt-3">
                                                <label>{{ translate('Bank account / transfer information') }}</label>
                                                <textarea class="form-control" name="payment_bank_info" rows="4">{{ old('payment_bank_info', $settings['payment_bank_info'] ?? '') }}</textarea>
                                            </div>
                                            <div class="form-group mb-0 compact-uploader">
                                                <label>{{ translate('Bank / transfer icon') }}</label>
                                                <div class="input-group" data-toggle="aizuploader" data-type="image">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse') }}</div>
                                                    </div>
                                                    <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                                    <input type="hidden" name="payment_bank_icon" class="selected-files" value="{{ old('payment_bank_icon', $settings['payment_bank_icon'] ?? '') }}">
                                                </div>
                                                <div class="file-preview box sm"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-6">
                                        <div class="config-panel-white">
                                            <h5 class="config-section-title">{{ translate('Debit cards') }}</h5>
                                            <div class="form-group mt-3">
                                                <label>{{ translate('Debit cards title') }}</label>
                                                <input type="text" class="form-control" name="payment_debit_title" value="{{ old('payment_debit_title', $settings['payment_debit_title'] ?? '') }}">
                                            </div>
                                            <div class="form-group">
                                                <label>{{ translate('Debit cards information') }}</label>
                                                <textarea class="form-control" name="payment_debit_info" rows="3">{{ old('payment_debit_info', $settings['payment_debit_info'] ?? '') }}</textarea>
                                            </div>
                                            <div class="form-group mb-0 compact-uploader">
                                                <label>{{ translate('Debit cards icon') }}</label>
                                                <div class="input-group" data-toggle="aizuploader" data-type="image">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse') }}</div>
                                                    </div>
                                                    <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                                    <input type="hidden" name="payment_debit_icon" class="selected-files" value="{{ old('payment_debit_icon', $settings['payment_debit_icon'] ?? '') }}">
                                                </div>
                                                <div class="file-preview box sm"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-6">
                                        <div class="config-panel-white">
                                            <h5 class="config-section-title">{{ translate('Credit cards') }}</h5>
                                            <div class="form-group mt-3">
                                                <label>{{ translate('Credit cards title') }}</label>
                                                <input type="text" class="form-control" name="payment_credit_title" value="{{ old('payment_credit_title', $settings['payment_credit_title'] ?? '') }}">
                                            </div>
                                            <div class="form-group">
                                                <label>{{ translate('Credit cards information') }}</label>
                                                <textarea class="form-control" name="payment_credit_info" rows="3">{{ old('payment_credit_info', $settings['payment_credit_info'] ?? '') }}</textarea>
                                            </div>
                                            <div class="form-group mb-0 compact-uploader">
                                                <label>{{ translate('Credit cards icon') }}</label>
                                                <div class="input-group" data-toggle="aizuploader" data-type="image">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse') }}</div>
                                                    </div>
                                                    <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                                    <input type="hidden" name="payment_credit_icon" class="selected-files" value="{{ old('payment_credit_icon', $settings['payment_credit_icon'] ?? '') }}">
                                                </div>
                                                <div class="file-preview box sm"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-12">
                                        <div class="config-panel-white mb-0">
                                            <h5 class="config-section-title">{{ translate('Cash payments') }}</h5>
                                            <div class="row gutters-10 mt-3">
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>{{ translate('Cash payment title') }}</label>
                                                        <input type="text" class="form-control" name="payment_cash_title" value="{{ old('payment_cash_title', $settings['payment_cash_title'] ?? '') }}">
                                                    </div>
                                                    <div class="form-group mb-lg-0">
                                                        <label>{{ translate('Cash payment information') }}</label>
                                                        <textarea class="form-control" name="payment_cash_info" rows="3">{{ old('payment_cash_info', $settings['payment_cash_info'] ?? '') }}</textarea>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="form-group mb-0 compact-uploader">
                                                        <label>{{ translate('Cash payment icon') }}</label>
                                                        <div class="input-group" data-toggle="aizuploader" data-type="image">
                                                            <div class="input-group-prepend">
                                                                <div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse') }}</div>
                                                            </div>
                                                            <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                                            <input type="hidden" name="payment_cash_icon" class="selected-files" value="{{ old('payment_cash_icon', $settings['payment_cash_icon'] ?? '') }}">
                                                        </div>
                                                        <div class="file-preview box sm"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="config-information" role="tabpanel">
                            <div id="information-fields">
                                <div class="config-panel-white">
                                    <h5 class="config-section-title">{{ translate('Information table') }}</h5>
                                    <div class="form-group mt-3">
                                        <label>{{ translate('Information table title') }}</label>
                                        <input type="text" class="form-control" name="info_page_title" value="{{ old('info_page_title', $settings['info_page_title'] ?? '') }}">
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <label class="mb-0">{{ translate('Information table rows') }}</label>
                                        <button type="button" class="btn btn-soft-primary btn-sm" id="add-info-row">
                                            <i class="las la-plus"></i>
                                            {{ translate('Add Row') }}
                                        </button>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-bordered mb-0" id="info-table-editor">
                                            <thead>
                                                <tr>
                                                    <th width="35%">{{ translate('Label') }}</th>
                                                    <th>{{ translate('Value') }}</th>
                                                    <th width="60" class="text-center">{{ translate('Action') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($infoRows as $row)
                                                    <tr>
                                                        <td>
                                                            <input type="text" class="form-control" name="info_table_labels[]" value="{{ $row['label'] ?? '' }}">
                                                        </td>
                                                        <td>
                                                            <input type="text" class="form-control" name="info_table_values[]" value="{{ $row['value'] ?? '' }}">
                                                        </td>
                                                        <td class="text-center">
                                                            <button type="button" class="btn btn-soft-danger btn-icon btn-circle btn-sm remove-info-row">
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
                        </div>

                        <div class="tab-pane fade" id="config-products" role="tabpanel">
                            <div class="config-panel-white">
                                <h5 class="config-section-title">{{ translate('Product layout') }}</h5>
                                <div class="row gutters-10 mt-3">
                                    <div class="col-lg-4">
                                        <div class="form-group mb-lg-0">
                                            <label>{{ translate('Description character limit') }}</label>
                                            <input type="number" class="form-control" name="description_limit" min="40" max="220" value="{{ old('description_limit', $settings['description_limit'] ?? 90) }}">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="config-panel-white mb-0">
                                <h5 class="config-section-title">{{ translate('Product typography') }}</h5>
                                <div class="table-responsive mt-3">
                                    <table class="table table-bordered typography-table mb-0">
                                        <thead>
                                            <tr>
                                                <th>{{ translate('Element') }}</th>
                                                <th width="260">{{ translate('Font family') }}</th>
                                                <th width="160">{{ translate('Font size') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($typographyFields as $field)
                                                <tr>
                                                    <td class="fw-600">{{ $field['label'] }}</td>
                                                    <td>
                                                        <select class="form-control aiz-selectpicker" name="{{ $field['family'] }}">
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
                            <div class="config-panel-white mb-0">
                                <h5 class="config-section-title">{{ translate('Letter colors') }}</h5>
                                <div class="row gutters-10 mt-3">
                                    @foreach ($letters as $letter)
                                        <div class="col-xl-2 col-md-3 col-6">
                                            <div class="letter-color-cell">
                                                <label class="mb-2">{{ $letter }}</label>
                                                <div class="d-flex">
                                                    <input type="color" class="form-control mr-1" name="product_box_colors[{{ $letter }}]" value="{{ old('product_box_colors.' . $letter, $settings['product_box_colors'][$letter] ?? $letterPalette[$letter] ?? '#f36f21') }}" title="{{ translate('Box color') }}">
                                                    <input type="color" class="form-control" name="product_text_colors[{{ $letter }}]" value="{{ old('product_text_colors.' . $letter, $settings['product_text_colors'][$letter] ?? '#ffffff') }}" title="{{ translate('Text color') }}">
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
                    {{ translate('Save Configuration') }}
                </button>
            </div>
        </form>
    </div>
@endsection

@section('script')
    <script type="text/javascript">
        function syncConfigToggles() {
            $('[data-config-toggle]').each(function() {
                var target = $($(this).data('config-toggle'));
                target.toggleClass('d-none', !$(this).is(':checked'));
            });
        }

        $('[data-config-toggle]').on('change', syncConfigToggles);
        syncConfigToggles();

        $('#add-info-row').on('click', function() {
            $('#info-table-editor tbody').append(
                '<tr>' +
                    '<td><input type="text" class="form-control" name="info_table_labels[]" value=""></td>' +
                    '<td><input type="text" class="form-control" name="info_table_values[]" value=""></td>' +
                    '<td class="text-center"><button type="button" class="btn btn-soft-danger btn-icon btn-circle btn-sm remove-info-row"><i class="las la-trash"></i></button></td>' +
                '</tr>'
            );
        });

        $(document).on('click', '.remove-info-row', function() {
            if ($('#info-table-editor tbody tr').length > 1) {
                $(this).closest('tr').remove();
            } else {
                $(this).closest('tr').find('input').val('');
            }
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
