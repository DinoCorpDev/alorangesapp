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
    @endphp

    <div class="aiz-titlebar text-left mt-2 mb-3">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="h3">{{ translate('Catalog Configuration') }}</h1>
                <p class="mb-0 text-muted">
                    {{ $catalog ? $catalog['name'] : translate('Default configuration for new catalogs') }}
                </p>
            </div>
            <div class="col-md-6 text-md-right">
                <a href="{{ route('product_catalogs.index') }}" class="btn btn-soft-secondary">
                    {{ translate('Back to PDF Catalogs') }}
                </a>
            </div>
        </div>
    </div>

    <form action="{{ $action }}" method="POST">
        @csrf
        @if ($method === 'PUT')
            @method('PUT')
        @endif

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 h6">{{ translate('Payment Methods Image') }}</h5>
            </div>
            <div class="card-body">
                <label class="aiz-checkbox d-block mb-3">
                    <input type="checkbox" name="show_payment_page" value="1" @if (old('show_payment_page', $settings['show_payment_page'] ?? true)) checked @endif>
                    <span class="aiz-square-check"></span>
                    <span>{{ translate('Show payment page') }}</span>
                </label>
                <div class="row gutters-10">
                    <div class="col-md-4">
                        <div class="form-group">
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
                    <div class="col-md-8">
                        <div class="row gutters-10">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ translate('Payment page title') }}</label>
                                    <input type="text" class="form-control" name="payment_title" value="{{ old('payment_title', $settings['payment_title'] ?? '') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ translate('Main payment banner') }}</label>
                                    <input type="text" class="form-control" name="payment_delivery_title" value="{{ old('payment_delivery_title', $settings['payment_delivery_title'] ?? '') }}">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>{{ translate('Bank account / transfer information') }}</label>
                            <textarea class="form-control" name="payment_bank_info" rows="4">{{ old('payment_bank_info', $settings['payment_bank_info'] ?? '') }}</textarea>
                        </div>
                        <div class="form-group">
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
                        <div class="row gutters-10">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ translate('Debit cards title') }}</label>
                                    <input type="text" class="form-control" name="payment_debit_title" value="{{ old('payment_debit_title', $settings['payment_debit_title'] ?? '') }}">
                                </div>
                                <div class="form-group">
                                    <label>{{ translate('Debit cards information') }}</label>
                                    <textarea class="form-control" name="payment_debit_info" rows="3">{{ old('payment_debit_info', $settings['payment_debit_info'] ?? '') }}</textarea>
                                </div>
                                <div class="form-group">
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
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ translate('Credit cards title') }}</label>
                                    <input type="text" class="form-control" name="payment_credit_title" value="{{ old('payment_credit_title', $settings['payment_credit_title'] ?? '') }}">
                                </div>
                                <div class="form-group">
                                    <label>{{ translate('Credit cards information') }}</label>
                                    <textarea class="form-control" name="payment_credit_info" rows="3">{{ old('payment_credit_info', $settings['payment_credit_info'] ?? '') }}</textarea>
                                </div>
                                <div class="form-group">
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
                        <div class="form-group">
                            <label>{{ translate('Cash payment title') }}</label>
                            <input type="text" class="form-control" name="payment_cash_title" value="{{ old('payment_cash_title', $settings['payment_cash_title'] ?? '') }}">
                        </div>
                        <div class="form-group mb-0">
                            <label>{{ translate('Cash payment information') }}</label>
                            <textarea class="form-control" name="payment_cash_info" rows="3">{{ old('payment_cash_info', $settings['payment_cash_info'] ?? '') }}</textarea>
                        </div>
                        <div class="form-group mb-0">
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

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 h6">{{ translate('Information Table Image') }}</h5>
            </div>
            <div class="card-body">
                <label class="aiz-checkbox d-block mb-3">
                    <input type="checkbox" name="show_info_page" value="1" @if (old('show_info_page', $settings['show_info_page'] ?? true)) checked @endif>
                    <span class="aiz-square-check"></span>
                    <span>{{ translate('Show information table') }}</span>
                </label>
                <div class="row gutters-10">
                    <div class="col-md-4">
                        <div class="form-group mb-md-0">
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
                    <div class="col-md-8">
                        <div class="form-group">
                            <label>{{ translate('Information table title') }}</label>
                            <input type="text" class="form-control" name="info_page_title" value="{{ old('info_page_title', $settings['info_page_title'] ?? '') }}">
                        </div>
                        <div class="form-group mb-0">
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
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 h6">{{ translate('Fourth Page Image') }}</h5>
            </div>
            <div class="card-body">
                <label class="aiz-checkbox d-block mb-3">
                    <input type="checkbox" name="show_page_four" value="1" @if (old('show_page_four', $settings['show_page_four'] ?? false)) checked @endif>
                    <span class="aiz-square-check"></span>
                    <span>{{ translate('Show page 4') }}</span>
                </label>
                <div class="form-group mb-0">
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

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 h6">{{ translate('Product Layout') }}</h5>
            </div>
            <div class="card-body">
                <label class="aiz-checkbox d-block mb-3">
                    <input type="checkbox" name="show_prices" value="1" @if (old('show_prices', $settings['show_prices'] ?? true)) checked @endif>
                    <span class="aiz-square-check"></span>
                    <span>{{ translate('Show prices') }}</span>
                </label>
                <div class="form-group mb-0">
                    <label>{{ translate('Description character limit') }}</label>
                    <input type="number" class="form-control" name="description_limit" min="40" max="220" value="{{ old('description_limit', $settings['description_limit'] ?? 90) }}">
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 h6">{{ translate('Letter Colors') }}</h5>
            </div>
            <div class="card-body">
                <div class="row gutters-5">
                    @foreach ($letters as $letter)
                        <div class="col-xl-2 col-md-3 col-6">
                            <div class="form-group">
                                <label class="mb-1">{{ $letter }}</label>
                                <div class="d-flex">
                                    <input type="color" class="form-control p-1 mr-1" name="product_box_colors[{{ $letter }}]" value="{{ old('product_box_colors.' . $letter, $settings['product_box_colors'][$letter] ?? $letterPalette[$letter] ?? '#f36f21') }}" title="{{ translate('Box color') }}">
                                    <input type="color" class="form-control p-1" name="product_text_colors[{{ $letter }}]" value="{{ old('product_text_colors.' . $letter, $settings['product_text_colors'][$letter] ?? '#ffffff') }}" title="{{ translate('Text color') }}">
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        @php
            $fontFamilies = ['DejaVu Sans', 'Arial', 'Georgia', 'Times New Roman', 'Verdana', 'Tahoma', 'Courier New'];
            $typographyFields = [
                ['label' => translate('Product title'), 'family' => 'product_title_font_family', 'size' => 'product_title_font_size', 'default_size' => 12],
                ['label' => translate('Description'), 'family' => 'product_description_font_family', 'size' => 'product_description_font_size', 'default_size' => 10],
                ['label' => translate('Price'), 'family' => 'product_price_font_family', 'size' => 'product_price_font_size', 'default_size' => 16],
                ['label' => translate('Reference'), 'family' => 'product_reference_font_family', 'size' => 'product_reference_font_size', 'default_size' => 12],
            ];
        @endphp

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 h6">{{ translate('Product Typography') }}</h5>
            </div>
            <div class="card-body">
                <div class="row gutters-10">
                    @foreach ($typographyFields as $field)
                        <div class="col-xl-3 col-md-6">
                            <div class="border rounded p-3 mb-3">
                                <h6 class="mb-3">{{ $field['label'] }}</h6>
                                <div class="form-group">
                                    <label>{{ translate('Font family') }}</label>
                                    <select class="form-control aiz-selectpicker" name="{{ $field['family'] }}">
                                        @foreach ($fontFamilies as $fontFamily)
                                            <option value="{{ $fontFamily }}" @if (old($field['family'], $settings[$field['family']] ?? 'DejaVu Sans') === $fontFamily) selected @endif>{{ $fontFamily }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group mb-0">
                                    <label>{{ translate('Font size') }}</label>
                                    <input type="number" class="form-control" name="{{ $field['size'] }}" min="7" max="30" value="{{ old($field['size'], $settings[$field['size']] ?? $field['default_size']) }}">
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="mb-4 text-right">
            <button type="submit" class="btn btn-primary">{{ translate('Save Configuration') }}</button>
        </div>
    </form>
@endsection

@section('script')
    <script type="text/javascript">
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
    </script>
@endsection
