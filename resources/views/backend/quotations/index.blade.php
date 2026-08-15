@extends('backend.layouts.app')

@section('content')
    <div class="row">
        <div class="col-lg-12 mx-auto">
            <div class="d-flex flex-wrap align-items-center justify-content-between mb-3" style="gap: 12px">
                <h1 class="h3 mb-0">{{ translate('Cotizaciones') }}</h1>
                <a href="{{ route('quotations.create') }}" class="btn btn-primary">
                    <i class="las la-plus"></i> {{ translate('Nueva cotización') }}
                </a>
            </div>

            <div class="card">
                <div class="card-header">
                    <form method="GET" action="{{ route('quotations.index') }}" class="w-100">
                        <div class="input-group" style="max-width: 420px">
                            <input type="text" class="form-control" name="busqueda" value="{{ $busqueda }}"
                                placeholder="{{ translate('Buscar por cliente, NIT o número') }}">
                            <div class="input-group-append">
                                <button class="btn btn-soft-primary" type="submit">
                                    <i class="las la-search"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table aiz-table mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 90px">{{ translate('N.º') }}</th>
                                    <th>{{ translate('Cliente') }}</th>
                                    <th style="width: 130px">{{ translate('NIT') }}</th>
                                    <th style="width: 120px">{{ translate('Expedición') }}</th>
                                    <th style="width: 120px">{{ translate('Vence') }}</th>
                                    <th class="text-right" style="width: 140px">{{ translate('Total') }}</th>
                                    <th class="text-right" style="width: 150px">{{ translate('Opciones') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($cotizaciones as $c)
                                    <tr>
                                        <td><strong>{{ $c->number }}</strong></td>
                                        <td>
                                            {{ $c->customer_name }}
                                            @if ($c->created_by_name)
                                                <small class="d-block text-muted">{{ $c->created_by_name }}</small>
                                            @endif
                                        </td>
                                        <td>{{ $c->customer_document ?: '—' }}</td>
                                        <td>{{ optional($c->issue_date)->format('d/m/Y') }}</td>
                                        <td>{{ $c->expiry_date ? $c->expiry_date->format('d/m/Y') : '—' }}</td>
                                        <td class="text-right">
                                            <strong>${{ number_format($c->total, 0, ',', '.') }}</strong>
                                        </td>
                                        <td class="text-right">
                                            <a href="{{ route('quotations.pdf', $c->id) }}" target="_blank"
                                                class="btn btn-soft-danger btn-icon btn-circle btn-sm"
                                                title="{{ translate('Descargar PDF') }}">
                                                <i class="las la-file-pdf"></i>
                                            </a>
                                            <a href="{{ route('quotations.edit', $c->id) }}"
                                                class="btn btn-soft-primary btn-icon btn-circle btn-sm"
                                                title="{{ translate('Editar') }}">
                                                <i class="las la-edit"></i>
                                            </a>
                                            <form action="{{ route('quotations.destroy', $c->id) }}" method="POST"
                                                class="d-inline"
                                                onsubmit="return confirm('{{ translate('¿Eliminar esta cotización?') }}')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="btn btn-soft-danger btn-icon btn-circle btn-sm"
                                                    title="{{ translate('Eliminar') }}">
                                                    <i class="las la-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-5 text-muted">
                                            <i class="las la-file-invoice-dollar" style="font-size: 42px; opacity: .35"></i>
                                            <div class="mt-2">{{ translate('Todavía no hay cotizaciones') }}</div>
                                            <a href="{{ route('quotations.create') }}" class="btn btn-primary btn-sm mt-3">
                                                {{ translate('Crear la primera') }}
                                            </a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="aiz-pagination mt-3">
                        {{ $cotizaciones->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
