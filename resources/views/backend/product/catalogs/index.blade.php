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
                            <label>{{ translate('Category') }}</label>
                            <select class="form-control aiz-selectpicker" name="category_id" id="catalog-category" data-live-search="true" required>
                                <option value="">{{ translate('Select Category') }}</option>
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
                        <label class="aiz-checkbox mb-0 fw-600">
                            <input type="checkbox" id="select-all-products" disabled>
                            <span class="aiz-square-check"></span>
                            <span>{{ translate('Select All') }}</span>
                        </label>
                    </div>
                    <div id="catalog-products" class="text-muted">
                        {{ translate('Select a category to load products') }}
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
                        <th data-breakpoints="lg">{{ translate('Category') }}</th>
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

        function renderProducts(products) {
            if (products.length === 0) {
                $('#catalog-products').html('{{ translate('No products found for this category') }}');
                $('#select-all-products').prop('checked', false).prop('disabled', true);
                refreshGenerateButton();
                return;
            }

            var groups = {};

            products.forEach(function(product) {
                var letter = (product.name || '#').trim().charAt(0).toUpperCase();
                if (!letter.match(/[A-Z0-9]/)) {
                    letter = '#';
                }
                groups[letter] = groups[letter] || [];
                groups[letter].push(product);
            });

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
            Object.keys(groups).sort().forEach(function(letter) {
                html += '<tr class="bg-soft-secondary">';
                html += '<td colspan="3" class="fw-700 text-dark">' + letter + '</td>';
                html += '</tr>';
                groups[letter].forEach(function(product) {
                    var disabled = product.is_disabled ? ' disabled' : '';
                    var disabledClass = product.is_disabled ? ' opacity-60' : '';
                    var rowClass = product.is_disabled ? '' : ' catalog-product-row';
                    html += '<tr class="' + rowClass + disabledClass + '">';
                    html += '<td class="align-middle">';
                    html += '<label class="aiz-checkbox mb-0' + (product.is_disabled ? ' aiz-checkbox-disabled' : '') + '">';
                    html += '<input type="checkbox" id="catalog-product-' + product.id + '" class="catalog-product-checkbox" name="product_ids[]" value="' + product.id + '"' + disabled + '>';
                    html += '<span class="aiz-square-check"></span>';
                    html += '</label>';
                    html += '</td>';
                    html += '<td class="align-middle">';
                    html += '<label class="mb-0 d-block' + (product.is_disabled ? '' : ' c-pointer') + '" for="catalog-product-' + product.id + '">';
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
            html += '</tbody></table></div>';

            $('#catalog-products').html(html);
            $('#select-all-products').prop('checked', false).prop('disabled', $('.catalog-product-checkbox:not(:disabled)').length === 0);
            refreshGenerateButton();
        }

        $('#catalog-category').on('change', function() {
            var categoryId = $(this).val();
            $('#catalog-products').html('{{ translate('Loading products') }}...');
            $('#select-all-products').prop('checked', false).prop('disabled', true);
            $('#generate-catalog').prop('disabled', true);

            if (!categoryId) {
                $('#catalog-products').html('{{ translate('Select a category to load products') }}');
                return;
            }

            $.get('{{ route('product_catalogs.category_products') }}', {
                category_id: categoryId
            }, function(products) {
                renderProducts(products);
            });
        });

        $('#select-all-products').on('change', function() {
            $('.catalog-product-checkbox:not(:disabled)').prop('checked', this.checked);
            refreshGenerateButton();
        });

        $(document).on('change', '.catalog-product-checkbox', function() {
            var total = $('.catalog-product-checkbox:not(:disabled)').length;
            var checked = $('.catalog-product-checkbox:not(:disabled):checked').length;
            $('#select-all-products').prop('checked', total > 0 && total === checked);
            refreshGenerateButton();
        });

        $(document).on('click', '.catalog-product-row', function(e) {
            if ($(e.target).is('input, label, span, small')) {
                return;
            }
            var checkbox = $(this).closest('tr').find('.catalog-product-checkbox');
            checkbox.prop('checked', !checkbox.prop('checked')).trigger('change');
        });
    </script>
@endsection
