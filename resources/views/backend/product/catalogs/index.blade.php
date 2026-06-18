@extends('backend.layouts.app')

@section('content')
    <div class="aiz-titlebar text-left mt-2 mb-3">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="h3">{{ translate('PDF Catalogs') }}</h1>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0 h6">{{ translate('Create Catalog') }}</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('product_catalogs.store') }}" method="POST" id="catalog-form">
                @csrf
                <div class="row gutters-10">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>{{ translate('Categories') }}</label>
                            <select class="form-control aiz-selectpicker" name="category_ids[]" id="catalog-category" data-live-search="true" data-actions-box="true" multiple required>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->getTranslation('name') }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>{{ translate('Catalog Name') }}</label>
                            <input type="text" class="form-control" name="name" placeholder="{{ translate('Catalog Name') }}">
                        </div>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <div class="form-group w-100 text-md-right">
                            <button type="submit" class="btn btn-primary" id="generate-catalog" disabled>
                                {{ translate('Generate PDF Catalog') }}
                            </button>
                        </div>
                    </div>
                </div>

                <div class="border rounded p-3">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div>
                            <h6 class="mb-0">{{ translate('Products') }}</h6>
                            <small class="text-muted" id="selected-products-count">0 {{ translate('selected') }}</small>
                        </div>
                        <div class="d-flex align-items-center">
                            <input type="text" class="form-control form-control-sm mr-3 d-none" id="catalog-product-search"
                                placeholder="{{ translate('Search products') }}" style="width: 260px;">
                            <label class="aiz-checkbox mb-0 fw-600">
                                <input type="checkbox" id="select-all-products" disabled>
                                <span class="aiz-square-check"></span>
                                <span>{{ translate('Select All') }}</span>
                            </label>
                        </div>
                    </div>
                    <div id="catalog-products" class="text-muted">
                        {{ translate('Select categories to load products') }}
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0 h6">{{ translate('Generated Catalogs') }}</h5>
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
                    @foreach ($catalogs as $key => $catalog)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $catalog['name'] }}</td>
                            <td>{{ $catalog['category_name'] }}</td>
                            <td>{{ $catalog['products_count'] }}</td>
                            <td>{{ $catalog['created_at'] }}</td>
                            <td class="text-right">
                                <a class="btn btn-soft-primary btn-icon btn-circle btn-sm"
                                    href="{{ route('product_catalogs.download', $catalog['id']) }}"
                                    title="{{ translate('Download') }}">
                                    <i class="las la-download"></i>
                                </a>
                                <form action="{{ route('product_catalogs.destroy', $catalog['id']) }}" method="POST" class="d-inline-block"
                                    onsubmit="return confirm('{{ translate('Are you sure you want to delete this catalog?') }}');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-soft-danger btn-icon btn-circle btn-sm"
                                        title="{{ translate('Delete') }}">
                                        <i class="las la-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    @if ($catalogs->isEmpty())
                        <tr>
                            <td colspan="6" class="text-center">{{ translate('No catalogs found') }}</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
@endsection

@section('script')
    <script type="text/javascript">
        function escapeHtml(value) {
            return $('<div>').text(value || '').html();
        }

        function refreshGenerateButton() {
            var selected = $('.catalog-product-checkbox:checked').length;
            $('#selected-products-count').text(selected + ' {{ translate('selected') }}');
            $('#generate-catalog').prop('disabled', selected === 0);
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
                var nextRows = $(this).nextUntil('.catalog-letter-row, .catalog-category-row', '.catalog-product-row');
                $(this).toggle(nextRows.filter(':visible').length > 0);
            });

            $('.catalog-category-row').each(function() {
                var nextRows = $(this).nextUntil('.catalog-category-row', '.catalog-product-row');
                $(this).toggle(nextRows.filter(':visible').length > 0);
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

            var html = '<div class="table-responsive">';
            html += '<table class="table table-hover mb-0">';
            html += '<thead>';
            html += '<tr>';
            html += '<th width="54">{{ translate('Select') }}</th>';
            html += '<th>{{ translate('Product Name') }}</th>';
            html += '<th width="180" class="text-right">{{ translate('Price') }}</th>';
            html += '</tr>';
            html += '</thead>';
            html += '<tbody>';

            categoryGroups.forEach(function(categoryGroup) {
                var groups = {};

                categoryGroup.products.forEach(function(product) {
                    var letter = (product.name || '#').trim().charAt(0).toUpperCase();
                    if (!letter.match(/[A-Z0-9]/)) {
                        letter = '#';
                    }
                    groups[letter] = groups[letter] || [];
                    groups[letter].push(product);
                });

                html += '<tr class="bg-soft-primary catalog-category-row">';
                html += '<td colspan="3" class="fw-700 text-dark">' + escapeHtml(categoryGroup.category_name) + '</td>';
                html += '</tr>';

                Object.keys(groups).sort().forEach(function(letter) {
                    html += '<tr class="bg-soft-secondary catalog-letter-row">';
                    html += '<td colspan="3" class="fw-700 text-dark">' + letter + '</td>';
                    html += '</tr>';
                    groups[letter].forEach(function(product) {
                        var disabled = product.is_disabled ? ' disabled' : '';
                        var disabledClass = product.is_disabled ? ' opacity-60' : '';
                        var rowClass = product.is_disabled ? ' catalog-product-row' : ' catalog-product-row catalog-product-row-selectable';
                        var checkboxId = 'catalog-product-' + categoryGroup.category_id + '-' + product.id;
                        var searchText = (product.name + ' ' + product.id + ' ' + categoryGroup.category_name).toLowerCase();
                        html += '<tr class="' + rowClass + disabledClass + '" data-search="' + escapeHtml(searchText) + '">';
                        html += '<td class="align-middle">';
                        html += '<label class="aiz-checkbox mb-0' + (product.is_disabled ? ' aiz-checkbox-disabled' : '') + '">';
                        html += '<input type="checkbox" id="' + checkboxId + '" class="catalog-product-checkbox" name="product_ids[]" value="' + product.id + '"' + disabled + '>';
                        html += '<span class="aiz-square-check"></span>';
                        html += '</label>';
                        html += '</td>';
                        html += '<td class="align-middle">';
                        html += '<label class="mb-0 d-block' + (product.is_disabled ? '' : ' c-pointer') + '" for="' + checkboxId + '">';
                        html += '<span class="d-block fw-600 text-dark" style="white-space: normal; word-break: break-word;">' + escapeHtml(product.name) + '</span>';
                        html += '<small class="text-muted">ID: ' + product.id + '</small>';
                        if (product.is_disabled) {
                            html += '<span class="badge badge-inline badge-soft-danger ml-2">{{ translate('Price is zero') }}</span>';
                        }
                        html += '</label>';
                        html += '</td>';
                        html += '<td class="align-middle text-right fw-600">' + escapeHtml(product.price) + '</td>';
                        html += '</tr>';
                    });
                });
            });

            html += '<tr id="catalog-no-search-results" class="d-none">';
            html += '<td colspan="3" class="text-center text-muted py-4">{{ translate('No products match your search') }}</td>';
            html += '</tr>';
            html += '</tbody></table></div>';

            $('#catalog-products').html(html);
            $('#catalog-product-search').removeClass('d-none').val('');
            $('#select-all-products').prop('checked', false);
            refreshSelectAllState();
            refreshGenerateButton();
        }

        $('#catalog-category').on('change', function() {
            var categoryIds = $(this).val() || [];
            $('#catalog-products').html('{{ translate('Loading products') }}...');
            $('#select-all-products').prop('checked', false).prop('disabled', true);
            $('#generate-catalog').prop('disabled', true);
            $('#catalog-product-search').addClass('d-none').val('');

            if (categoryIds.length === 0) {
                $('#catalog-products').html('{{ translate('Select categories to load products') }}');
                return;
            }

            $.get('{{ route('product_catalogs.category_products') }}', {
                category_ids: categoryIds
            }, function(products) {
                renderProducts(products);
            });
        });

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
            if ($(e.target).is('input, label, span, small')) {
                return;
            }
            var checkbox = $(this).closest('tr').find('.catalog-product-checkbox');
            checkbox.prop('checked', !checkbox.prop('checked')).trigger('change');
        });
    </script>
@endsection
