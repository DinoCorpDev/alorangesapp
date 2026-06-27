@extends('backend.layouts.app')

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-lg-auto">
                    <img class="size-200px" src="{{ uploaded_asset($collection->thumbnail_img) }}">
                </div>
                <div class="col-lg">
                    <h1 class="h5 fw-700">
                        {{ $collection->name }}
                        <a class="btn btn-success mb-0 float-right" for="openCSV" href="{{ route('collection.add', $collection->id) }}">
                            <span id="span-btn-excel">{{ translate('Add products') }}</span>
                        </a>
                    </h1>

                    <br>
                    <br>

                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th class="w-40px">#</th>
                                <th>{{ translate('Name') }}</th>
                                <th class="text-right">{{ translate('Price') }}</th>
                                <th class="text-right">{{ translate('Options') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($products as $product)
                                @php
                                    $pr = \App\Models\Product::find($product->id_product);
                                @endphp
                                <tr>
                                    <td>
                                        {{ $pr->id }}
                                    </td>
                                    <td>
                                        {{ $pr->name }}
                                    </td>
                                    <td class="text-right">
                                        {{ $pr->lowest_price }} {{ $pr->currency }}
                                    </td>
                                    <td>
                                        <a class="float-right" href="{{ route('collection.destroyProduct', $product->id) }}" title="{{ translate('Add') }}">
                                            <span>Quitar</span>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>

                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-auto w-lg-320px">
            <div class="card">
                <div class="card-body">
                    <div class="mb-3 d-flex justify-content-between">
                        <span class="mr-2 ml-0">{{ translate('Reference') }}:</span>
                        <span>{{ $collection->reference }}</span>
                    </div>
                    <div class="mb-3 d-flex justify-content-between">
                        <span class="mr-2 ml-0">{{ translate('Status') }}:</span>
                        @if ($collection->published)
                            <span class="badge badge-inline badge-success">{{ translate('Published') }}</span>
                        @else
                            <span class="badge badge-inline badge-secondary">{{ translate('Draft') }}</span>
                        @endif
                    </div>
                    <div class="mb-3 d-flex justify-content-between">
                        <span class="mr-2 ml-0">{{ translate('Brand') }}:</span>
                        @if ($collection->brand)
                            <div class="h-30px w-100px d-flex align-items-center justify-content-end">
                                <img src="{{ uploaded_asset($collection->brand->logo) }}" alt="{{ translate('Brand') }}" class="mw-100 mh-100">
                            </div>
                        @else
                            <span>{{ translate('No brand') }}</span>
                        @endif
                    </div>
                    <div class="mb-3 d-flex justify-content-between">
                        <span class="mr-2 ml-0">{{ translate('Category') }}:</span>
                        <span class="text-right">
                            @foreach ($collection->categories as $category)
                                <span class="badge badge-inline badge-md bg-soft-dark mb-1">
                                    {{ $category->name }}
                                </span>
                            @endforeach
                        </span>
                    </div>
                    <div class="mb-3 d-flex justify-content-between">
                        <span class="mr-2 ml-0">{{ translate('Warranty') }}:</span>

                        @if ($collection->has_warranty)
                            <span class="badge badge-inline badge-success">{{ translate('Has warranty') }}</span>
                        @else
                            <span class="badge badge-inline badge-secondary">{{ translate('No warranty') }}</span>
                        @endif
                    </div>
                    <div class="mb-3 d-flex justify-content-between">
                        <span class="mr-2 ml-0"></span>
                        <span>{{ $collection->warranty_text }}</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg">
            <div class="card">
                <div class="card-header mb-0 h6">{{ translate('Collection price, stock') }}</div>
                <div class="card-body">
                    <div class="form-group row">
                        <label class="col-md-3 col-from-label">{{ translate('Currency') }}</label>
                        <div class="col-md-8">
                            <input class="form-control" name="width" type="text" value="{{ $collection->currenc }}" min="0" required readonly />
                        </div>
                    </div>
                    <table class="table table-bordered table-responsive-xl">
                        <thead>
                            <tr>
                                @if ($collection->is_variant)
                                    <td class="">
                                        <label for="" class="control-label">{{ translate('Variant') }}</label>
                                    </td>
                                @endif
                                <td class="">
                                    <label for="" class="control-label">{{ translate('Price') }}</label>
                                </td>
                                <td class="">
                                    <label for="" class="control-label">{{ translate('Stock') }}</label>
                                </td>
                                <td class="">
                                    <label for="" class="control-label">{{ translate('SKU') }}</label>
                                </td>
                                @if ($collection->is_variant)
                                    <td class="">
                                        <label for="" class="control-label">{{ translate('Image') }}</label>
                                    </td>
                                @endif
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($collection->variations as $key => $variation)
                                <tr class="variant">
                                    @if ($collection->is_variant)
                                        <td>
                                            <div>{{ $name }}</div>
                                        </td>
                                    @endif
                                    <td>
                                        <div>{{ format_price($variation->price) }}</div>
                                    </td>
                                    <td>
                                        <div>
                                            @if ($variation->stock == '1')
                                                {{ translate('In stock') }}
                                            @else
                                                {{ translate('Out of stock') }}
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            {{ $variation->sku }}
                                        </div>
                                    </td>
                                    @if ($collection->is_variant)
                                        <td>
                                            <img src="{{ uploaded_asset($variation->img) }}" class="size-50px">
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0 h6">{{ translate('Gallery') }}</h5>
                </div>
                <div class="card-body">
                    <div class="aiz-carousel gutters-10 half-outside-arrow" data-items="5" data-xl-items="5" data-lg-items="3" data-md-items="2" data-sm-items="1">
                        @foreach (explode(',', $collection->photos) as $key => $image)
                            <div class="carousel-box">
                                <img src="{{ uploaded_asset($image) }}" class="img-fluid">
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0 h6">{{ translate('Shipping Information') }}</h5>
                </div>
                <div class="card-body">
                    <div class="form-group row">
                        <label class="col-md-3 col-from-label">{{ translate('Shipping') }}</label>
                        <div class="col-md-8">
                            <input class="form-control" name="width" type="text" value="{{ $collection->envio }}" min="0" required readonly />
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0 h6">{{ translate('Collection Description') }}</h5>
                </div>
                <div class="card-body">
                    {!! $collection->description !!}

                    <div class="form-group row mt-5">
                        <label class="col-md-3 col-from-label">{{ translate('material') }}</label>
                        <div class="col-md-8">
                            <input class="form-control" name="width" type="text" value="{{ $collection->material }}" min="0" required readonly />
                        </div>
                    </div>

                    <div class="form-group row mt-5">
                        <label class="col-md-3 col-from-label">{{ translate('medida_de_proyecto') }}</label>
                        <div class="col-md-8">
                            <input class="form-control" name="width" type="text" value="{{ $collection->medida_de_proyecto }}" min="0" required readonly />
                        </div>
                    </div>

                    <div class="form-group row mt-5">
                        <label class="col-md-3 col-from-label">{{ translate('si1') }}</label>
                        <div class="col-md-8">
                            <input class="form-control" name="width" type="text" value="{{ $collection->si1 }}" min="0" required readonly />
                        </div>
                    </div>

                    <div class="form-group row mt-5">
                        <label class="col-md-3 col-from-label">{{ translate('aforo') }}</label>
                        <div class="col-md-8">
                            <input class="form-control" name="width" type="text" value="{{ $collection->aforo }}" min="0" required readonly />
                        </div>
                    </div>

                    <div class="form-group row mt-5">
                        <label class="col-md-3 col-from-label">{{ translate('postventa') }}</label>
                        <div class="col-md-8">
                            <input class="form-control" name="width" type="text" value="{{ $collection->postventa }}" min="0" required readonly />
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">{{ translate('Imagen 1') }}</label>
                        <div class="col-md-8">
                            <div class="input-group" data-toggle="aizuploader" data-type="image">
                                <div class="input-group-prepend">
                                    <div class="input-group-text bg-soft-secondary font-weight-medium">
                                        {{ translate('Browse') }}</div>
                                </div>
                                <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                <input class="selected-files" name="imagen1" type="hidden" value="{{ $collection->imagen1 }}" required readonly>
                            </div>
                            <div class="file-preview box sm">
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">{{ translate('Imagen 2') }}</label>
                        <div class="col-md-8">
                            <div class="input-group" data-toggle="aizuploader" data-type="image">
                                <div class="input-group-prepend">
                                    <div class="input-group-text bg-soft-secondary font-weight-medium">
                                        {{ translate('Browse') }}
                                    </div>
                                </div>
                                <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                <input class="selected-files" name="imagen2" type="hidden" value="{{ $collection->imagen2 }}" required readonly>
                            </div>
                            <div class="file-preview box sm">
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">{{ translate('Imagen 3') }}</label>
                        <div class="col-md-8">
                            <div class="input-group" data-toggle="aizuploader" data-type="image">
                                <div class="input-group-prepend">
                                    <div class="input-group-text bg-soft-secondary font-weight-medium">
                                        {{ translate('Browse') }}
                                    </div>
                                </div>
                                <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                <input class="selected-files" name="imagen3" type="hidden" value="{{ $collection->imagen3 }}" required readonly>
                            </div>
                            <div class="file-preview box sm">
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">{{ translate('Imagen 4') }}</label>
                        <div class="col-md-8">
                            <div class="input-group" data-toggle="aizuploader" data-type="image">
                                <div class="input-group-prepend">
                                    <div class="input-group-text bg-soft-secondary font-weight-medium">
                                        {{ translate('Browse') }}
                                    </div>
                                </div>
                                <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                <input class="selected-files" name="imagen4" type="hidden" value="{{ $collection->imagen4 }}" required readonly>
                            </div>
                            <div class="file-preview box sm">
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">{{ translate('Video') }}</label>
                        <div class="col-md-8">
                            <div class="input-group" data-toggle="aizuploader" data-type="video">
                                <div class="input-group-prepend">
                                    <div class="input-group-text bg-soft-secondary font-weight-medium">
                                        {{ translate('Browse') }}
                                    </div>
                                </div>
                                <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                <input class="selected-files" name="video1" type="hidden" value="{{ $collection->video1 }}" required readonly>
                            </div>
                            <div class="file-preview box sm">
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">{{ translate('Video 2') }}</label>
                        <div class="col-md-8">
                            <div class="input-group" data-toggle="aizuploader" data-type="video">
                                <div class="input-group-prepend">
                                    <div class="input-group-text bg-soft-secondary font-weight-medium">
                                        {{ translate('Browse') }}
                                    </div>
                                </div>
                                <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                <input class="selected-files" name="video2" type="hidden" value="{{ $collection->video2 }}" required readonly>
                            </div>
                            <div class="file-preview box sm">
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">{{ translate('Video 3') }}</label>
                        <div class="col-md-8">
                            <div class="input-group" data-toggle="aizuploader" data-type="video">
                                <div class="input-group-prepend">
                                    <div class="input-group-text bg-soft-secondary font-weight-medium">
                                        {{ translate('Browse') }}
                                    </div>
                                </div>
                                <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                <input class="selected-files" name="video3" type="hidden" value="{{ $collection->video3 }}" required readonly>
                            </div>
                            <div class="file-preview box sm">
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">{{ translate('Video 4') }}</label>
                        <div class="col-md-8">
                            <div class="input-group" data-toggle="aizuploader" data-type="video">
                                <div class="input-group-prepend">
                                    <div class="input-group-text bg-soft-secondary font-weight-medium">
                                        {{ translate('Browse') }}
                                    </div>
                                </div>
                                <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                <input class="selected-files" name="video4" type="hidden" value="{{ $collection->video4 }}" required readonly>
                            </div>
                            <div class="file-preview box sm">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
