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
    @endphp

    <div class="aiz-titlebar text-left mt-2 mb-3">
        <div class="row align-items-center">
            <div class="col-md-6"><h1 class="h3">{{ translate('PDF Catalogs') }}</h1></div>
            @if ($isEdit)
                <div class="col-md-6 text-md-right"><a href="{{ route('product_catalogs.index') }}" class="btn btn-soft-secondary">{{ translate('Create New Catalog') }}</a></div>
            @endif
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="mb-0 h6">{{ $isEdit ? translate('Edit Catalog') : translate('Create Catalog') }}</h5>
            <a class="btn btn-soft-primary btn-sm" href="{{ $isEdit ? route('product_catalogs.configuration', $catalog['id']) : route('product_catalogs.configuration.defaults') }}">
                <i class="las la-cog"></i>
                {{ translate('Catalog Configuration') }}
            </a>
        </div>
        <div class="card-body">
            <form action="{{ $isEdit ? route('product_catalogs.update', $catalog['id']) : route('product_catalogs.store') }}" method="POST" id="catalog-form">
                @csrf
                @if ($isEdit) @method('PUT') @endif

                <div class="border rounded p-3 mb-3">
                    <h6 class="mb-3">{{ translate('Catalog Cover') }}</h6>
                    <div class="row gutters-10">
                        <div class="col-md-4">
                            <div class="form-group">
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

                <div class="row gutters-10">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>{{ translate('Categories') }}</label>
                            <select class="form-control aiz-selectpicker" name="category_ids[]" id="catalog-category" data-live-search="true" data-actions-box="true" multiple required>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" @if (in_array((string) $category->id, $selectedCategoryIds)) selected @endif>{{ $category->getTranslation('name') }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>{{ translate('Catalog Name') }}</label>
                            <input type="text" class="form-control" name="name" value="{{ old('name', $catalog['name'] ?? '') }}" placeholder="{{ translate('Catalog Name') }}">
                        </div>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <div class="form-group w-100 text-md-right">
                            <button type="submit" class="btn btn-primary" id="generate-catalog" disabled>{{ $isEdit ? translate('Update PDF Catalog') : translate('Generate PDF Catalog') }}</button>
                        </div>
                    </div>
                </div>

                <div class="border rounded p-3 mt-3">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h6 class="mb-0">{{ translate('Advertising') }}</h6>
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
                                    <th width="180">{{ translate('Show After Letter') }}</th>
                                    <th width="70" class="text-center">{{ translate('Options') }}</th>
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
                                            <button type="button" class="btn btn-soft-danger btn-icon btn-circle btn-sm remove-advertising-row">
                                                <i class="las la-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="border rounded p-3 mt-3">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div>
                            <h6 class="mb-0">{{ translate('Products') }}</h6>
                            <small class="text-muted" id="selected-products-count">0 {{ translate('selected') }}</small>
                        </div>
                        <div class="d-flex align-items-center">
                            <input type="text" class="form-control form-control-sm mr-3 d-none" id="catalog-product-search" placeholder="{{ translate('Search products') }}" style="width: 260px;">
                            <label class="aiz-checkbox mb-0 fw-600"><input type="checkbox" id="select-all-products" disabled><span class="aiz-square-check"></span><span>{{ translate('Select All') }}</span></label>
                        </div>
                    </div>
                    <div id="catalog-products" class="text-muted">{{ translate('Select categories to load products') }}</div>
                </div>
            </form>
        </div>
    </div>
    <div class="card">
        <div class="card-header"><h5 class="mb-0 h6">{{ translate('Generated Catalogs') }}</h5></div>
        <div class="card-body">
            <table class="table aiz-table mb-0">
                <thead><tr><th>#</th><th>{{ translate('Name') }}</th><th data-breakpoints="lg">{{ translate('Categories') }}</th><th data-breakpoints="lg">{{ translate('Products') }}</th><th data-breakpoints="lg">{{ translate('Created At') }}</th><th class="text-right">{{ translate('Options') }}</th></tr></thead>
                <tbody>
                    @foreach ($catalogs as $key => $catalogItem)
                        <tr>
                            <td>{{ $key + 1 }}</td><td>{{ $catalogItem['name'] }}</td><td>{{ $catalogItem['category_name'] }}</td><td>{{ $catalogItem['products_count'] }}</td><td>{{ $catalogItem['created_at'] }}</td>
                            <td class="text-right">
                                <a class="btn btn-soft-info btn-icon btn-circle btn-sm" href="{{ route('product_catalogs.edit', $catalogItem['id']) }}" title="{{ translate('Edit') }}"><i class="las la-edit"></i></a>
                                <a class="btn btn-soft-primary btn-icon btn-circle btn-sm" href="{{ route('product_catalogs.download', $catalogItem['id']) }}" title="{{ translate('Download') }}"><i class="las la-download"></i></a>
                                <form action="{{ route('product_catalogs.destroy', $catalogItem['id']) }}" method="POST" class="d-inline-block" onsubmit="return confirm('{{ translate('Are you sure you want to delete this catalog?') }}');">@csrf @method('DELETE')<button type="submit" class="btn btn-soft-danger btn-icon btn-circle btn-sm" title="{{ translate('Delete') }}"><i class="las la-trash"></i></button></form>
                            </td>
                        </tr>
                    @endforeach
                    @if ($catalogs->isEmpty()) <tr><td colspan="6" class="text-center">{{ translate('No catalogs found') }}</td></tr> @endif
                </tbody>
            </table>
        </div>
    </div>
@endsection

@section('script')
    <script type="text/javascript">
        var selectedProductIds = @json($selectedProductIds);
        var advertisingLetterOptions = @json($advertisingLetters);
        function escapeHtml(value) { return $('<div>').text(value || '').html(); }
        function advertisingRowTemplate() {
            var options = advertisingLetterOptions.map(function(letter) {
                return '<option value="' + escapeHtml(letter) + '">' + escapeHtml(letter) + '</option>';
            }).join('');

            return '<tr class="advertising-row">' +
                '<td><div class="input-group" data-toggle="aizuploader" data-type="image"><div class="input-group-prepend"><div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse') }}</div></div><div class="form-control file-amount">{{ translate('Choose File') }}</div><input type="hidden" name="advertising_images[]" class="selected-files" value=""></div><div class="file-preview box sm"></div></td>' +
                '<td><select class="form-control aiz-selectpicker" name="advertising_letters[]">' + options + '</select></td>' +
                '<td class="text-center"><button type="button" class="btn btn-soft-danger btn-icon btn-circle btn-sm remove-advertising-row"><i class="las la-trash"></i></button></td>' +
            '</tr>';
        }
        function refreshGenerateButton() {
            var selected = $('.catalog-product-checkbox:checked').length;
            $('#selected-products-count').text(selected + ' {{ translate('selected') }}');
            $('#generate-catalog').prop('disabled', selected === 0);
        }
        function refreshSelectAllState() {
            var visibleCheckboxes = $('.catalog-product-row:visible .catalog-product-checkbox:not(:disabled)');
            var checkedVisibleCheckboxes = visibleCheckboxes.filter(':checked');
            $('#select-all-products').prop('disabled', visibleCheckboxes.length === 0).prop('checked', visibleCheckboxes.length > 0 && visibleCheckboxes.length === checkedVisibleCheckboxes.length);
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
                $('#catalog-products').html('{{ translate('No products found for the selected categories') }}');
                $('#select-all-products').prop('checked', false).prop('disabled', true);
                $('#catalog-product-search').addClass('d-none').val('');
                refreshGenerateButton();
                return;
            }
            var html = '<div class="table-responsive"><table class="table table-hover mb-0"><thead><tr><th width="54">{{ translate('Select') }}</th><th>{{ translate('Product Name') }}</th><th width="180" class="text-right">{{ translate('Price') }}</th></tr></thead><tbody>';
            categoryGroups.forEach(function(categoryGroup) {
                var groups = {};
                categoryGroup.products.forEach(function(product) {
                    var letter = (product.name || '#').trim().charAt(0).toUpperCase();
                    if (!letter.match(/[A-Z0-9]/)) { letter = '#'; }
                    groups[letter] = groups[letter] || [];
                    groups[letter].push(product);
                });
                html += '<tr class="bg-soft-primary catalog-category-row"><td colspan="3" class="fw-700 text-dark">' + escapeHtml(categoryGroup.category_name) + '</td></tr>';
                Object.keys(groups).sort().forEach(function(letter) {
                    html += '<tr class="bg-soft-secondary catalog-letter-row"><td colspan="3" class="fw-700 text-dark">' + letter + '</td></tr>';
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
            $('#catalog-products').html('{{ translate('Loading products') }}...');
            $('#select-all-products').prop('checked', false).prop('disabled', true);
            $('#generate-catalog').prop('disabled', true);
            $('#catalog-product-search').addClass('d-none').val('');
            if (categoryIds.length === 0) { $('#catalog-products').html('{{ translate('Select categories to load products') }}'); refreshGenerateButton(); return; }
            $.get('{{ route('product_catalogs.category_products') }}', { category_ids: categoryIds }, function(products) { renderProducts(products); });
        }
        $('#catalog-category').on('change', loadCatalogProducts);
        $('#select-all-products').on('change', function() { $('.catalog-product-row:visible .catalog-product-checkbox:not(:disabled)').prop('checked', this.checked); refreshGenerateButton(); refreshSelectAllState(); });
        $(document).on('change', '.catalog-product-checkbox', function() { refreshGenerateButton(); refreshSelectAllState(); });
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
        if (($('#catalog-category').val() || []).length > 0) { loadCatalogProducts(); }
    </script>
@endsection
