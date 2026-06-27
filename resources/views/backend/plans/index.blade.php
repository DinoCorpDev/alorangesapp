@extends('backend.layouts.app')

@section('content')
    <div class="aiz-titlebar text-left mt-2 mb-3">
        <div class="row align-items-center">
            <div class="col-md-4">
                <h1 class="h3">Todos los Planes</h1>
            </div>

            <div class="col-md-3 text-md-right">
                    <a class="btn btn-primary w-100" href="{{ route('plans.create') }}">
                        <span>Agregar Plan</span>
                    </a>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <table class="table aiz-table mb-0">
                <thead>
                    <tr>
                        <th class="w-40px">#</th>
                        <th class="col-xl-2">Referencia</th>
                        <th data-breakpoints="md" width="20%">Nombre</th>
                        <th data-breakpoints="md">Descripción</th>
                        <th data-breakpoints="md">Precio</th>
                        <th class="text-right" data-breakpoints="md">{{ translate('Options') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($plans as $key => $plan)
                        <tr>
                            <td>{{ $key+1 }}</td>
                            <td>
                                <a class="text-reset d-block"  href="#"
                                    target="_blank">
                                    <div class="d-flex align-items-center">
                                        <img class="size-60px size-xxl-80px mr-2"
                                            src="{{ uploaded_asset($plan->imagen1) }}" alt="Image"
                                            onerror="this.onerror=null;this.src='{{ static_asset('/assets/img/placeholder.jpg') }}';" />
                                        <span class="flex-grow-1 minw-0">
                                            <div class=" text-truncate-2 fs-12">
                                                {{ $plan->referencia }}</div>
                                        </span>
                                    </div>
                                </a>
                            </td>
                            <td>
                                {{ $plan->nombre }}
                            </td>
                            <td>
                                {{ $plan->precio }}
                            </td>
                            <td>
                                {{ $plan->id }}
                            </td>
                            <td class="text-right">

                                    <a class="btn btn-soft-primary btn-icon btn-circle btn-sm"
                                        href="{{ route('plans.show', $plan->id) }}"
                                        title="{{ translate('View') }}">
                                        <i class="las la-eye"></i>
                                    </a>

                                    <a class="btn btn-soft-info btn-icon btn-circle btn-sm"
                                        href="{{ url('admin/plans/edit/'.$plan->id) }}"
                                        title="{{ translate('Edit') }}">
                                        <i class="las la-edit"></i>
                                    </a>

                                    <a class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete"
                                        data-href="{{ url('admin/plans/destroy/'.$plan->id) }}" href="#"
                                        title="{{ translate('Delete') }}">
                                        <i class="las la-trash"></i>
                                    </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="aiz-pagination">
                {{ $plans->appends(request()->input())->links() }}
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


        function sort_products(el) {
            $('#sort_products').submit();
        }
    </script>
@endsection
