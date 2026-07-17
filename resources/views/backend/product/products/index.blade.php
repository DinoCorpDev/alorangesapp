@extends('backend.layouts.app')

@section('content')
    <div class="aiz-titlebar text-left mt-2 mb-3">
        <div class="row align-items-center">
            <div class="col-md-2">
                <h1 class="h3 mb-1">{{ translate('All products') }}</h1>
                @if ($uncategorizedCount > 0)
                    <span class="badge badge-inline badge-soft-warning fs-12">
                        {{ translate('Without category') }}: {{ number_format($uncategorizedCount) }}
                    </span>
                @endif
            </div>
            <div class="col-md-2 offset-md-2 text-md-right">
                @can('add_products')
                    <label
                        class="btn btn-secondary w-100 mb-0"
                        for="openCSV"
                        href="#"
                    >
                        <span id="span-btn-excel">{{ translate('Import Products') }}</span>
                        <div
                            class="spinner-border m-auto"
                            id="spinner-excel"
                            role="status"
                            style="width: 20px; height: 20px; display: none"
                        >
                            <span class="sr-only">Loading...</span>
                        </div>
                    </label>
                    <form
                        class="d-none"
                        id="formCSV"
                        action="{{ route('product.import') }}"
                        method="post"
                        enctype="multipart/form-data"
                    >
                        @csrf
                        <input
                            class="form-control"
                            id="openCSV"
                            id="uploaded_file"
                            name="uploaded_file"
                            type="file"
                            onchange="document.getElementById('formCSV').submit();document.getElementById('span-btn-excel').style.display = 'none';document.getElementById('spinner-excel').style.display = 'block'"
                            required
                        >
                    </form>
                @endcan
            </div>
            <div class="col-md-3">
                @can('add_products')
                    <a
                        href="{{ route('product.create') }}"
                        class="btn btn-primary w-100"
                    >
                        <span>{{ translate('Add New Product') }}</span>
                    </a>
                @endcan
            </div>

            <div class="col-md-2 text-md-right">
                <button
                    type="button"
                    id="btn-alegra-import"
                    class="btn btn-outline-primary w-100"
                    title="{{ translate('Sync products from Alegra (takes several minutes, runs in background)') }}"
                >
                    <span id="span-btn-alegra">{{ translate('Update Product') }}</span>
                    <div
                        class="spinner-border spinner-border-sm m-auto"
                        id="spinner-alegra"
                        role="status"
                        style="display: none"
                    >
                        <span class="sr-only">Loading...</span>
                    </div>
                </button>
                <div id="alegra-import-progress" class="text-left mt-2 small" style="display: none">
                    <div class="d-flex align-items-center mb-1">
                        <div class="progress flex-grow-1 mr-2" style="height: 6px">
                            <div id="alegra-progress-bar" class="progress-bar bg-primary" role="progressbar" style="width: 0%"></div>
                        </div>
                        <span id="alegra-progress-percent" class="text-muted" style="min-width: 36px; text-align: right;"></span>
                    </div>
                    <div id="alegra-progress-text" class="text-muted"></div>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Table improvements for admin product list */
        .aiz-table.table {
            border-collapse: collapse;
        }
        .aiz-table th, .aiz-table td {
            padding: 0.75rem 0.9rem;
            vertical-align: middle;
            white-space: nowrap;
        }
        .aiz-table td .product-title {
            white-space: normal !important;
            overflow: visible !important;
            text-overflow: clip !important;
        }
        .aiz-table.table-striped tbody tr:nth-of-type(odd) {
            background-color: #f9fbfc;
        }
        .product-thumb {
            width: 60px;
            height: auto;
            object-fit: cover;
        }
        @media (max-width: 768px) {
            .aiz-table th, .aiz-table td { white-space: normal; }
        }
    </style>

    <div class="card">
        <form
            class=""
            id="sort_products"
            action=""
            method="GET"
        >
            <div class="card-header row gutters-5">
                <div class="col text-center text-md-left">
                </div>
                <div class="col-md-2 ml-auto">
                    <select
                        class="form-control form-control-sm aiz-selectpicker mb-2 mb-md-0"
                        name="type"
                        id="type"
                        onchange="sort_products()"
                    >
                        <option value="">{{ translate('Sort By') }}</option>
                        <option
                            value="rating,desc"
                            @isset($col_name, $query) @if ($col_name == 'rating' && $query == 'desc') selected @endif @endisset
                        >
                            {{ translate('Rating (High > Low)') }}
                        </option>
                        <option
                            value="rating,asc"
                            @isset($col_name, $query) @if ($col_name == 'rating' && $query == 'asc') selected @endif @endisset
                        >
                            {{ translate('Rating (Low > High)') }}
                        </option>
                        <option
                            value="num_of_sale,desc"
                            @isset($col_name, $query) @if ($col_name == 'num_of_sale' && $query == 'desc') selected @endif @endisset
                        >
                            {{ translate('Num of Sale (High > Low)') }}
                        </option>
                        <option
                            value="num_of_sale,asc"
                            @isset($col_name, $query) @if ($col_name == 'num_of_sale' && $query == 'asc') selected @endif @endisset
                        >
                            {{ translate('Num of Sale (Low > High)') }}
                        </option>
                        <option
                            value="unit_price,desc"
                            @isset($col_name, $query) @if ($col_name == 'unit_price' && $query == 'desc') selected @endif @endisset
                        >
                            {{ translate('Base Price (High > Low)') }}
                        </option>
                        <option
                            value="unit_price,asc"
                            @isset($col_name, $query) @if ($col_name == 'unit_price' && $query == 'asc') selected @endif @endisset
                        >
                            {{ translate('Base Price (Low > High)') }}
                        </option>
                    </select>
                </div>
                <div class="col-md-2">
                    <div class="input-group">
                        <input
                            type="text"
                            class="form-control form-control-sm"
                            id="search"
                            name="search"
                            @isset($sort_search) value="{{ $sort_search }}" @endisset
                            placeholder="{{ translate('Type & Enter') }}"
                        >
                    </div>
                </div>
            </div>
        </form>
        <div class="card-body">
            <div class="table-responsive">
            <table class="table aiz-table mb-0 table-striped table-hover table-bordered">
                <thead>
                    <tr>
                        <th class="w-40px">#</th>
                        <th class="col-xl-2">{{ translate('Name') }}</th>
                        <th data-breakpoints="md">{{ translate('Info') }}</th>
                        <th
                            data-breakpoints="md"
                            width="20%"
                        >{{ translate('Categories') }}</th>
                        
                        <th
                            data-breakpoints="md"
                            class="text-right"
                        >{{ translate('Options') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $isGrouped = !$col_name;
                        $lastCategoryId = null;
                        $categoryColors = ['primary', 'success', 'info', 'warning', 'danger', 'dark', 'secondary'];
                    @endphp
                    @foreach ($products as $key => $product)
                        @php
                            $primaryCategory = $product->categories->first();
                            $isNewGroup = $isGrouped && $primaryCategory && $primaryCategory->id !== $lastCategoryId;
                            if ($isNewGroup) {
                                $lastCategoryId = $primaryCategory->id;
                            }
                        @endphp
                        <tr @if ($isNewGroup) class="border-top border-2" @endif>
                            <td>{{ $key + 1 + ($products->currentPage() - 1) * $products->perPage() }}</td>
                            <td>
                                @if ($isNewGroup)
                                    <div class="fs-10 text-uppercase text-muted fw-600 mb-1">
                                        {{ $primaryCategory->name }}
                                    </div>
                                @endif
                                <div class="d-flex align-items-center">
                                    <img
                                        src="{{ uploaded_asset($product->thumbnail_img) }}"
                                        alt="Image"
                                        class="size-60px size-xxl-80px mr-2"
                                        onerror="this.onerror=null;this.src='{{ static_asset('/assets/img/placeholder.jpg') }}';"
                                    />
                                    <span class="flex-grow-1 minw-0">
                                        <div class=" text-truncate-2 fs-12">
                                            {{ $product->getTranslation('name') }}</div>
                                    </span>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <div><span>{{ translate('Rating') }}</span>: <span
                                            class="rating rating-sm my-2">{{ renderStarRating($product->rating) }}</span>
                                    </div>
                                    <div>
                                        <span>{{ translate('Price') }}</span>:
                                        @if ($product->highest_price != $product->lowest_price)
                                            <span class="fw-600">{{ format_price($product->lowest_price) }} -
                                                {{ format_price($product->highest_price) }}</span>
                                        @else
                                            <span class="fw-600">{{ format_price($product->lowest_price) }}</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                @foreach ($product->categories as $category)
                                    @php $color = $categoryColors[$category->id % count($categoryColors)]; @endphp
                                    <span
                                        class="badge badge-inline badge-md badge-soft-{{ $color }} mb-1">{{ $category->name }}</span>
                                @endforeach
                            </td>

                            <td class="text-right">
                                @can('view_products')
                                    <a
                                        class="btn btn-soft-primary btn-icon btn-circle btn-sm"
                                        href="{{ route('product.show', $product->id) }}"
                                        title="{{ translate('View') }}"
                                    >
                                        <i class="las la-eye"></i>
                                    </a>
                                @endcan
                                @can('edit_products')
                                    <a
                                        class="btn btn-soft-info btn-icon btn-circle btn-sm"
                                        href="{{ route('product.edit', ['id' => $product->id, 'lang' => env('DEFAULT_LANGUAGE')]) }}"
                                        title="{{ translate('Edit') }}"
                                    >
                                        <i class="las la-edit"></i>
                                    </a>
                                @endcan
                                @canany(['duplicate_products', 'delete_products'])
                                    <div class="dropdown d-inline-block">
                                        <a
                                            class="btn btn-soft-secondary btn-icon btn-circle btn-sm"
                                            href="#"
                                            data-toggle="dropdown"
                                            title="{{ translate('More') }}"
                                        >
                                            <i class="las la-ellipsis-v"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            @can('duplicate_products')
                                                <a
                                                    class="dropdown-item"
                                                    href="{{ route('product.duplicate', ['id' => $product->id, 'type' => $type]) }}"
                                                >
                                                    <i class="las la-copy mr-2"></i>
                                                    <span>{{ translate('Duplicate') }}</span>
                                                </a>
                                            @endcan
                                            @can('delete_products')
                                                <a
                                                    href="#"
                                                    class="dropdown-item confirm-delete"
                                                    data-href="{{ route('product.destroy', $product->id) }}"
                                                >
                                                    <i class="las la-trash mr-2"></i>
                                                    <span>{{ translate('Delete') }}</span>
                                                </a>
                                            @endcan
                                        </div>
                                    </div>
                                @endcanany
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            </div>
            <div class="aiz-pagination">
                {{ $products->appends(request()->input())->links() }}
            </div>
        </div>
    </div>

    @php
        CoreComponentRepository::instantiateShopRepository();
    @endphp
@endsection

@section('modal')
    @include('backend.inc.delete_modal')
@endsection


@section('script')
    <script type="text/javascript">
        $(document).ready(function() {
            //$('#container').removeClass('mainnav-lg').addClass('mainnav-sm');
        });

        function update_published(el) {
            if (el.checked) {
                var status = 1;
            } else {
                var status = 0;
            }
            $.post('{{ route('product.published') }}', {
                _token: '{{ csrf_token() }}',
                id: el.value,
                status: status
            }, function(data) {
                if (data == 1) {
                    AIZ.plugins.notify('success', '{{ translate('Published products updated successfully') }}');
                } else {
                    AIZ.plugins.notify('danger', '{{ translate('Something went wrong') }}');
                }
            });
        }

        function sort_products(el) {
            $('#sort_products').submit();
        }

        (function () {
            var $btn = $('#btn-alegra-import');
            var $span = $('#span-btn-alegra');
            var $spinner = $('#spinner-alegra');
            var $progressWrap = $('#alegra-import-progress');
            var $progressBar = $('#alegra-progress-bar');
            var $progressPercent = $('#alegra-progress-percent');
            var $progressText = $('#alegra-progress-text');
            var pollTimer = null;
            var isLiveSession = false;

            function formatElapsed(seconds) {
                seconds = Math.max(0, Math.floor(seconds));
                var m = Math.floor(seconds / 60);
                var s = seconds % 60;
                return m + 'm ' + s + 's';
            }

            function stopPolling() {
                if (pollTimer) {
                    clearInterval(pollTimer);
                    pollTimer = null;
                }
                $span.show();
                $spinner.hide();
                $btn.prop('disabled', false);
            }

            function renderStatus(data) {
                var status = data.status || 'idle';

                if (status === 'idle') {
                    return;
                }

                $progressWrap.show();

                var imported = data.imported || 0;
                var total = data.total || null;
                var elapsed = data.started_at ? (Date.now() - new Date(data.started_at).getTime()) / 1000 : 0;

                if (status === 'starting') {
                    $progressBar.css('width', '3%');
                    $progressPercent.text('...');
                    $progressText.text('{{ translate('Starting import...') }}');
                    return;
                }

                if (status === 'running') {
                    var isEstimate = !!data.total_is_estimate;
                    var percent = total ? Math.min(99, Math.round((imported / total) * 100)) : null;
                    $progressBar.css('width', (percent !== null ? percent : 10) + '%');
                    $progressPercent.text(percent !== null ? percent + (isEstimate ? '%~' : '%') : '...');

                    var text = imported + (total ? ' / ' + (isEstimate ? '~' : '') + total : '') + ' {{ translate('products imported') }} - {{ translate('elapsed') }}: ' + formatElapsed(elapsed);

                    if (total && imported > 0) {
                        var remaining = (elapsed / imported) * (total - imported);
                        text += ' - {{ translate('estimated time remaining') }}: ' + formatElapsed(remaining);
                    }

                    $progressText.text(text);
                    return;
                }

                if (status === 'completed') {
                    $progressBar.css('width', '100%');
                    $progressPercent.text('100%');
                    $progressText.text(
                        imported + ' {{ translate('products updated successfully') }}' +
                        (data.errors ? ' (' + data.errors + ' {{ translate('errors') }})' : '')
                    );
                    stopPolling();

                    // Only notify + reload the first time we witness this completion
                    // (i.e. we were actively polling a run). A plain page load that
                    // finds an already-completed status must not repeat this, or a
                    // reload would keep finding "completed" and reload forever.
                    if (isLiveSession) {
                        isLiveSession = false;
                        AIZ.plugins.notify('success', imported + ' {{ translate('products updated successfully') }}');
                        setTimeout(function () { window.location.reload(); }, 2500);
                    }
                    return;
                }

                if (status === 'failed') {
                    $progressText.text('{{ translate('The import failed, please check the logs.') }}');
                    stopPolling();

                    if (isLiveSession) {
                        isLiveSession = false;
                        AIZ.plugins.notify('danger', '{{ translate('The import failed, please check the logs.') }}');
                    }
                }
            }

            function poll() {
                $.get('{{ route('product.alegra_import_status') }}', renderStatus);
            }

            function startPolling() {
                isLiveSession = true;
                $span.hide();
                $spinner.show();
                $btn.prop('disabled', true);
                $progressWrap.show();
                poll();
                pollTimer = setInterval(poll, 3000);
            }

            $btn.on('click', function () {
                $.ajax({
                    url: '{{ route('product.alegra_import') }}',
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': AIZ.data.csrf },
                    success: startPolling
                });
            });

            // Resume polling if an import is already running (e.g. after a page refresh)
            $.get('{{ route('product.alegra_import_status') }}', function (data) {
                if (data.status === 'starting' || data.status === 'running') {
                    startPolling();
                } else if (data.status === 'completed' || data.status === 'failed') {
                    renderStatus(data);
                }
            });
        })();
    </script>
@endsection
