@extends('backend.layouts.app')

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-lg-auto">
                    <img
                        src="{{ uploaded_asset($product->thumbnail_img) }}"
                        class="size-200px"
                    >
                </div>
                <div class="col-lg">
                    <h1 class="h5 fw-700">{{ $product->getTranslation('name') }}</h1>
                    <div class="d-flex align-items-center mb-3">
                        <span class="rating">
                            {{ renderStarRating($product->rating) }}
                        </span>
                        <span class="ml-1 mr-0 opacity-50">({{ number_format($product->rating, 2) }})</span>
                    </div>
                    <div class="d-flex flex-wrap">
                        <div class="border border-dotted rounded py-2 px-3 mr-3 ml-0">
                            <div class="h3 mb-0 fw-700">{{ $product->reviews_count }}</div>
                            <div class="opacity-60 fs-12">{{ translate('Reviews') }}</div>
                        </div>
                        <div class="border border-dotted rounded py-2 px-3 mr-3 ml-0">
                            <div class="h3 mb-0 fw-700">{{ $product->wishlists_count }}</div>
                            <div class="opacity-60 fs-12">{{ translate('In wishlist') }}</div>
                        </div>
                        <div class="border border-dotted rounded py-2 px-3 mr-3 ml-0">
                            <div class="h3 mb-0 fw-700">{{ $product->carts_count }}</div>
                            <div class="opacity-60 fs-12">{{ translate('In cart') }}</div>
                        </div>
                        <div class="border border-dotted rounded py-2 px-3 mr-3 ml-0">
                            <div class="h3 mb-0 fw-700">{{ $product->num_of_sale }}</div>
                            <div class="opacity-60 fs-12">{{ translate('Times sold') }}</div>
                        </div>
                        <div class="border border-dotted rounded py-2 px-3 mr-3 ml-0">
                            <div class="h3 mb-0 fw-700">{{ format_price($product->orderDetails()->sum('price')) }}</div>
                            <div class="opacity-60 fs-12">{{ translate('Amount sold') }}</div>
                        </div>
                        @if ($product->discount > 0)
                            <div class="border border-dotted rounded py-2 px-3 mr-3 ml-0 bg-danger text-white">
                                @if ($product->discount_type == 'flat')
                                    <span class="h3 mb-0 fw-700">{{ format_price($product->discount) }}</span>
                                @else
                                    <span class="h3 mb-0 fw-700">{{ $product->discount }}%</span>
                                @endif
                                {{ translate('Off') }}
                                <div class="opacity-60 fs-12">
                                    {{ date('Y/m/d H:i:s', $product->discount_start_date) }} -
                                    {{ date('Y/m/d H:i:s', $product->discount_end_date) }}
                                </div>
                            </div>
                        @endif
                    </div>
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
                        <span>{{ $product->reference }}</span>
                    </div>
                    <div class="mb-3 d-flex justify-content-between">
                        <span class="mr-2 ml-0">{{ translate('Status') }}:</span>
                        @if ($product->published)
                            <span class="badge badge-inline badge-success">{{ translate('Published') }}</span>
                        @else
                            <span class="badge badge-inline badge-secondary">{{ translate('Draft') }}</span>
                        @endif
                    </div>
                    <div class="mb-3 d-flex justify-content-between">
                        <span class="mr-2 ml-0">{{ translate('Brand') }}:</span>
                        @if ($product->brand)
                            <div class="h-30px w-100px d-flex align-items-center justify-content-end">
                                <img
                                    src="{{ uploaded_asset($product->brand->logo) }}"
                                    alt="{{ translate('Brand') }}"
                                    class="mw-100 mh-100"
                                >
                            </div>
                        @else
                            <span>{{ translate('No brand') }}</span>
                        @endif
                    </div>
                    <div class="mb-3 d-flex justify-content-between">
                        <span class="mr-2 ml-0">{{ translate('Category') }}:</span>
                        <span class="text-right">
                            @foreach ($product->categories as $category)
                                @php
                                    $ancestors = [];
                                    $curr = $category;
                                    while ($curr) {
                                        $ancestors[] = $curr->getTranslation('name');
                                        $curr = $curr->parentCategory;
                                    }
                                    $ancestors = array_reverse($ancestors);
                                    $path = implode(' > ', $ancestors);
                                    $prodCount = $category->product_categories()->count();
                                @endphp
                                <span class="badge badge-inline badge-md bg-soft-dark mb-1">{{ $path }} <small class="text-muted">({{ $prodCount }})</small></span>
                            @endforeach
                        </span>
                    </div>
                    <div class="mb-3 d-flex justify-content-between">
                        <span class="mr-2 ml-0">{{ translate('Tags') }}:</span>
                        <span class="text-right">
                            {{ $product->tags }}
                        </span>
                    </div>
                    <div class="mb-3 d-flex justify-content-between">
                        <span class="mr-2 ml-0">{{ translate('Warranty') }}:</span>

                        @if ($product->has_warranty)
                            <span class="badge badge-inline badge-success">{{ translate('Has warranty') }}</span>
                        @else
                            <span class="badge badge-inline badge-secondary">{{ translate('No warranty') }}</span>
                        @endif
                    </div>
                    <div class="mb-3 d-flex justify-content-between">
                        <span class="mr-2 ml-0"></span>
                        <span>{{ $product->warranty_text }}</span>
                    </div>
                    <div class="mb-3 d-flex justify-content-between">
                        <span class="mr-2 ml-0">{{ translate('Minimum Purchase Qty ') }}:</span>
                        <span>{{ $product->min_qty }}</span>
                    </div>
                    <div class="mb-3 d-flex justify-content-between">
                        <span class="mr-2 ml-0">{{ translate('Maximum Purchase Qty ') }}:</span>
                        <span>{{ $product->max_qty }}</span>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0 h6">{{ translate('VAT & Tax') }}</h5>
                </div>
                <div class="card-body">
                    @foreach (\App\Models\Tax::all() as $tax)
                        @php
                            $tax_amount = 0;
                            $tax_type = 'flat';
                            foreach ($product->taxes as $row) {
                                if ($row->tax_id == $tax->id) {
                                    $tax_amount = $row->tax;
                                    $tax_type = $row->tax_type;
                                }
                            }
                        @endphp

                        <div class="form-row">
                            <div class="form-group col-6">
                                <label for="name">
                                    {{ $tax->name }}
                                    <input
                                        type="hidden"
                                        value="{{ $tax->id }}"
                                        name="tax_ids[]"
                                    >
                                </label>
                            </div>
                            <div class="form-group col-6">
                                @if ($tax_type == 'flat')
                                    <span class="">{{ format_price($tax_amount) }}</span>
                                @else
                                    <span class="">{{ $tax_amount }}%</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="col-lg">
            <div class="card">
                <div class="card-header mb-0 h6">{{ translate('Product price, stock') }}</div>
                <div class="card-body">
                    <div class="form-group row">
                        <label class="col-md-3 col-from-label">{{ translate('Currency') }}</label>
                        <div class="col-md-8">
                            <input
                                class="form-control"
                                name="width"
                                type="text"
                                value="{{ $product->currency }}"
                                min="0"
                                required
                                readonly
                            />
                        </div>
                    </div>
                    <table class="table table-bordered table-responsive-xl">
                        <thead>
                            <tr>
                                @if ($product->is_variant)
                                    <td class="">
                                        <label
                                            for=""
                                            class="control-label"
                                        >{{ translate('Variant') }}</label>
                                    </td>
                                @endif
                                <td class="">
                                    <label
                                        for=""
                                        class="control-label"
                                    >{{ translate('Price') }}</label>
                                </td>
                                <td class="">
                                    <label
                                        for=""
                                        class="control-label"
                                    >{{ translate('Stock') }}</label>
                                </td>
                                <td class="">
                                    <label
                                        for=""
                                        class="control-label"
                                    >{{ translate('SKU') }}</label>
                                </td>
                                @if ($product->is_variant)
                                    <td class="">
                                        <label
                                            for=""
                                            class="control-label"
                                        >{{ translate('Image') }}</label>
                                    </td>
                                @endif
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($product->variations as $key => $variation)
                                @php
                                    $name = '';
                                    $code_array = array_filter(explode('/', $variation->code));
                                    $lstKey = array_key_last($code_array);
                                    
                                    foreach ($code_array as $j => $comb) {
                                        $comb = explode(':', $comb);
                                    
                                        $option_name = \App\Models\Attribute::find($comb[0])->getTranslation('name');
                                        $choice_name = \App\Models\AttributeValue::find($comb[1])->getTranslation('name');
                                    
                                        $name .= $option_name . ': ' . $choice_name;
                                    
                                        if ($lstKey != $j) {
                                            $name .= ' / ';
                                        }
                                    }
                                @endphp
                                <tr class="variant">
                                    @if ($product->is_variant)
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
                                    @if ($product->is_variant)
                                        <td>
                                            <img
                                                src="{{ uploaded_asset($variation->img) }}"
                                                class="size-50px"
                                            >
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
                    <div
                        class="aiz-carousel gutters-10 half-outside-arrow"
                        data-items="5"
                        data-xl-items="5"
                        data-lg-items="3"
                        data-md-items="2"
                        data-sm-items="1"
                    >
                        @foreach (explode(',', $product->photos) as $key => $image)
                            <div class="carousel-box">
                                <img
                                    src="{{ uploaded_asset($image) }}"
                                    class="img-fluid"
                                >
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
                            <input
                                class="form-control"
                                name="width"
                                type="text"
                                value="{{ $product->shipping }}"
                                min="0"
                                required
                                readonly
                            />
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-from-label">{{ translate('Standard delivery time') }}</label>
                        <div class="col-md-8">
                            <div class="input-group">
                                <input
                                    type="number"
                                    class="form-control"
                                    name="standard_delivery_time"
                                    min="0"
                                    value="{{ $product->standard_delivery_time }}"
                                    required
                                    readonly
                                >
                                <div class="input-group-append"><span class="input-group-text">hr(s)</span></div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-from-label">{{ translate('Express delivery time') }}</label>
                        <div class="col-md-8">
                            <div class="input-group">
                                <input
                                    type="number"
                                    class="form-control"
                                    name="express_delivery_time"
                                    min="0"
                                    value="{{ $product->express_delivery_time }}"
                                    required
                                    readonly
                                >
                                <div class="input-group-append"><span class="input-group-text">hr(s)</span></div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-from-label">{{ translate('Weight') }}</label>
                        <div class="col-md-8">
                            <div class="input-group">
                                <input
                                    type="number"
                                    class="form-control"
                                    name="weight"
                                    min="0"
                                    value="{{ $product->weight }}"
                                    required
                                    readonly
                                >
                                <div class="input-group-append"><span
                                        class="input-group-text">{{ get_setting('weight_unit') }}</span></div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-from-label">{{ translate('Height') }}</label>
                        <div class="col-md-8">
                            <div class="input-group">
                                <input
                                    type="number"
                                    class="form-control"
                                    name="height"
                                    min="0"
                                    value="{{ $product->height }}"
                                    required
                                    readonly
                                >
                                <div class="input-group-append"><span
                                        class="input-group-text">{{ get_setting('dimension_unit') }}</span></div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-from-label">{{ translate('Length') }}</label>
                        <div class="col-md-8">
                            <div class="input-group">
                                <input
                                    type="number"
                                    class="form-control"
                                    name="length"
                                    min="0"
                                    value="{{ $product->length }}"
                                    required
                                    readonly
                                >
                                <div class="input-group-append"><span
                                        class="input-group-text">{{ get_setting('dimension_unit') }}</span></div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-from-label">{{ translate('Width') }}</label>
                        <div class="col-md-8">
                            <div class="input-group">
                                <input
                                    type="number"
                                    class="form-control"
                                    name="width"
                                    min="0"
                                    value="{{ $product->width }}"
                                    required
                                    readonly
                                >
                                <div class="input-group-append">
                                    <span class="input-group-text">
                                        {{ get_setting('dimension_unit') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0 h6">{{ translate('Product Description') }}</h5>
                </div>
                <div class="card-body">
                    {!! $product->getTranslation('description') !!}

                    <div class="form-group row mt-5">
                        <label class="col-md-3 col-from-label">{{ translate('Intake') }}</label>
                        <div class="col-md-8">
                            <input
                                class="form-control"
                                name="width"
                                type="text"
                                value="{{ $product->intake }}"
                                min="0"
                                required
                                readonly
                            />
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-from-label">{{ translate('Material') }}</label>
                        <div class="col-md-8">
                            <input
                                class="form-control"
                                name="width"
                                type="text"
                                value="{{ $product->material }}"
                                min="0"
                                required
                                readonly
                            />
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-from-label">{{ translate('SI1') }}</label>
                        <div class="col-md-8">
                            <input
                                class="form-control"
                                name="width"
                                type="text"
                                value="{{ $product->unit_metering }}"
                                min="0"
                                required
                                readonly
                            />
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-from-label">{{ translate('Crimp size') }}</label>
                        <div class="col-md-8">
                            <input
                                class="form-control"
                                name="width"
                                type="text"
                                value="{{ $product->engaste }}"
                                min="0"
                                required
                                readonly
                            />
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-from-label">{{ translate('si2') }}</label>
                        <div class="col-md-8">
                            <div class="input-group">
                                <input
                                    class="form-control"
                                    name="si2"
                                    type="text"
                                    value="{{ $product->si2 }}"
                                    required
                                    readonly
                                    placeholder="{{ translate('si2') }}"
                                >
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-from-label">{{ translate('Medidas embalaje') }}</label>
                        <div class="col-md-8">
                            <div class="input-group">
                                <input
                                    class="form-control"
                                    name="medidas_de_embalaje"
                                    type="text"
                                    value="{{ $product->medidas_de_embalaje }}"
                                    required
                                    readonly
                                    placeholder="{{ translate('Medidas embalaje') }}"
                                >
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-from-label">{{ translate('si3') }}</label>
                        <div class="col-md-8">
                            <div class="input-group">
                                <input
                                    class="form-control"
                                    name="si3"
                                    type="text"
                                    value="{{ $product->si3 }}"
                                    required
                                    readonly
                                    placeholder="{{ translate('si3') }}"
                                >
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-from-label">{{ translate('Peso de producto') }}</label>
                        <div class="col-md-8">
                            <div class="input-group">
                                <input
                                    class="form-control"
                                    name="peso_de_producto"
                                    type="text"
                                    value="{{ $product->peso_de_producto }}"
                                    required
                                    readonly
                                    placeholder="{{ translate('Peso de producto') }}"
                                >
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-from-label">{{ translate('si4') }}</label>
                        <div class="col-md-8">
                            <div class="input-group">
                                <input
                                    class="form-control"
                                    name="si4"
                                    type="text"
                                    value="{{ $product->si4 }}"
                                    required
                                    readonly
                                    placeholder="{{ translate('si4') }}"
                                >
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-from-label">{{ translate('Peso de envio') }}</label>
                        <div class="col-md-8">
                            <div class="input-group">
                                <input
                                    class="form-control"
                                    name="peso_de_envio"
                                    type="text"
                                    value="{{ $product->peso_de_envio }}"
                                    required
                                    readonly
                                    placeholder="{{ translate('Peso de envio') }}"
                                >
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-from-label">{{ translate('Tipo de conexion') }}</label>
                        <div class="col-md-8">
                            <div class="input-group">
                                <input
                                    class="form-control"
                                    name="tipo_de_coneccion"
                                    type="text"
                                    value="{{ $product->tipo_de_coneccion }}"
                                    required
                                    readonly
                                    placeholder="{{ translate('Tipo de conexion') }}"
                                >
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-from-label">{{ translate('Eficiencia') }}</label>
                        <div class="col-md-8">
                            <div class="input-group">
                                <input
                                    class="form-control"
                                    name="eficiencia"
                                    type="text"
                                    value="{{ $product->eficiencia }}"
                                    required
                                    readonly
                                    placeholder="{{ translate('Eficiencia') }}"
                                >
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-from-label">{{ translate('Caracteristica 1') }}</label>
                        <div class="col-md-8">
                            <div class="input-group">
                                <input
                                    class="form-control"
                                    name="caracteristica1"
                                    type="text"
                                    value="{{ $product->caracteristica1 }}"
                                    required
                                    readonly
                                    placeholder="{{ translate('Caracteristica 1') }}"
                                >
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-from-label">{{ translate('Caracteristica 2') }}</label>
                        <div class="col-md-8">
                            <div class="input-group">
                                <input
                                    class="form-control"
                                    name="caracteristica2"
                                    type="text"
                                    value="{{ $product->caracteristica2 }}"
                                    required
                                    readonly
                                    placeholder="{{ translate('Caracteristica 2') }}"
                                >
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-from-label">{{ translate('Caracteristica 3') }}</label>
                        <div class="col-md-8">
                            <div class="input-group">
                                <input
                                    class="form-control"
                                    name="caracteristica3"
                                    type="text"
                                    value="{{ $product->caracteristica3 }}"
                                    required
                                    readonly
                                    placeholder="{{ translate('Caracteristica 3') }}"
                                >
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-from-label">{{ translate('Caracteristica 4') }}</label>
                        <div class="col-md-8">
                            <div class="input-group">
                                <input
                                    class="form-control"
                                    name="caracteristica4"
                                    type="text"
                                    value="{{ $product->caracteristica4 }}"
                                    required
                                    readonly
                                    placeholder="{{ translate('Caracteristica 4') }}"
                                >
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-from-label">{{ translate('Caracteristica 5') }}</label>
                        <div class="col-md-8">
                            <div class="input-group">
                                <input
                                    class="form-control"
                                    name="caracteristica5"
                                    type="text"
                                    value="{{ $product->caracteristica5 }}"
                                    required
                                    readonly
                                    placeholder="{{ translate('Caracteristica 5') }}"
                                >
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-from-label">{{ translate('Caracteristica 6') }}</label>
                        <div class="col-md-8">
                            <div class="input-group">
                                <input
                                    class="form-control"
                                    name="caracteristica6"
                                    type="text"
                                    value="{{ $product->caracteristica6 }}"
                                    required
                                    readonly
                                    placeholder="{{ translate('Caracteristica 6') }}"
                                >
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-from-label">{{ translate('Caracteristica 7') }}</label>
                        <div class="col-md-8">
                            <div class="input-group">
                                <input
                                    class="form-control"
                                    name="caracteristica7"
                                    type="text"
                                    value="{{ $product->caracteristica7 }}"
                                    required
                                    readonly
                                    placeholder="{{ translate('Caracteristica 7') }}"
                                >
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-from-label">{{ translate('Caracteristica 8') }}</label>
                        <div class="col-md-8">
                            <div class="input-group">
                                <input
                                    class="form-control"
                                    name="caracteristica8"
                                    type="text"
                                    value="{{ $product->caracteristica8 }}"
                                    required
                                    readonly
                                    placeholder="{{ translate('Caracteristica 8') }}"
                                >
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-from-label">{{ translate('Beneficio 1') }}</label>
                        <div class="col-md-8">
                            <div class="input-group">
                                <input
                                    class="form-control"
                                    name="beneficio1"
                                    type="text"
                                    value="{{ $product->beneficio1 }}"
                                    required
                                    readonly
                                    placeholder="{{ translate('Beneficio 1') }}"
                                >
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-from-label">{{ translate('Beneficio 2') }}</label>
                        <div class="col-md-8">
                            <div class="input-group">
                                <input
                                    class="form-control"
                                    name="beneficio2"
                                    type="text"
                                    value="{{ $product->beneficio2 }}"
                                    required
                                    readonly
                                    placeholder="{{ translate('Beneficio 2') }}"
                                >
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-from-label">{{ translate('Beneficio 3') }}</label>
                        <div class="col-md-8">
                            <div class="input-group">
                                <input
                                    class="form-control"
                                    name="beneficio3"
                                    type="text"
                                    value="{{ $product->beneficio3 }}"
                                    required
                                    readonly
                                    placeholder="{{ translate('Beneficio 3') }}"
                                >
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-from-label">{{ translate('Beneficio 4') }}</label>
                        <div class="col-md-8">
                            <div class="input-group">
                                <input
                                    class="form-control"
                                    name="beneficio4"
                                    type="text"
                                    value="{{ $product->beneficio4 }}"
                                    required
                                    readonly
                                    placeholder="{{ translate('Beneficio 4') }}"
                                >
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-from-label">{{ translate('Beneficio 5') }}</label>
                        <div class="col-md-8">
                            <div class="input-group">
                                <input
                                    class="form-control"
                                    name="beneficio5"
                                    type="text"
                                    value="{{ $product->beneficio5 }}"
                                    required
                                    readonly
                                    placeholder="{{ translate('Beneficio 5') }}"
                                >
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-from-label">{{ translate('Postventa') }}</label>
                        <div class="col-md-8">
                            <div class="input-group">
                                <input
                                    class="form-control"
                                    name="postventa"
                                    type="text"
                                    value="{{ $product->postventa }}"
                                    required
                                    readonly
                                    placeholder="{{ translate('Postventa') }}"
                                >
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">{{ translate('Imagen 1') }}</label>
                        <div class="col-md-8">
                            <div
                                class="input-group"
                                data-toggle="aizuploader"
                                data-type="image"
                            >
                                <div class="input-group-prepend">
                                    <div class="input-group-text bg-soft-secondary font-weight-medium">
                                        {{ translate('Browse') }}</div>
                                </div>
                                <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                <input
                                    class="selected-files"
                                    name="imagen1"
                                    type="hidden"
                                    value="{{ $product->imagen1 }}"
                                    required
                                    readonly
                                >
                            </div>
                            <div class="file-preview box sm">
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">{{ translate('Imagen 2') }}</label>
                        <div class="col-md-8">
                            <div
                                class="input-group"
                                data-toggle="aizuploader"
                                data-type="image"
                            >
                                <div class="input-group-prepend">
                                    <div class="input-group-text bg-soft-secondary font-weight-medium">
                                        {{ translate('Browse') }}</div>
                                </div>
                                <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                <input
                                    class="selected-files"
                                    name="imagen2"
                                    type="hidden"
                                    value="{{ $product->imagen2 }}"
                                    required
                                    readonly
                                >
                            </div>
                            <div class="file-preview box sm">
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">{{ translate('Imagen 3') }}</label>
                        <div class="col-md-8">
                            <div
                                class="input-group"
                                data-toggle="aizuploader"
                                data-type="image"
                            >
                                <div class="input-group-prepend">
                                    <div class="input-group-text bg-soft-secondary font-weight-medium">
                                        {{ translate('Browse') }}</div>
                                </div>
                                <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                <input
                                    class="selected-files"
                                    name="imagen3"
                                    type="hidden"
                                    value="{{ $product->imagen3 }}"
                                    required
                                    readonly
                                >
                            </div>
                            <div class="file-preview box sm">
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">{{ translate('Imagen 4') }}</label>
                        <div class="col-md-8">
                            <div
                                class="input-group"
                                data-toggle="aizuploader"
                                data-type="image"
                            >
                                <div class="input-group-prepend">
                                    <div class="input-group-text bg-soft-secondary font-weight-medium">
                                        {{ translate('Browse') }}</div>
                                </div>
                                <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                <input
                                    class="selected-files"
                                    name="imagen4"
                                    type="hidden"
                                    value="{{ $product->imagen4 }}"
                                    required
                                    readonly
                                >
                            </div>
                            <div class="file-preview box sm">
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">{{ translate('Video') }}</label>
                        <div class="col-md-8">
                            <div
                                class="input-group"
                                data-toggle="aizuploader"
                                data-type="video"
                            >
                                <div class="input-group-prepend">
                                    <div class="input-group-text bg-soft-secondary font-weight-medium">
                                        {{ translate('Browse') }}</div>
                                </div>
                                <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                <input
                                    class="selected-files"
                                    name="video"
                                    type="hidden"
                                    value="{{ $product->video }}"
                                    required
                                    readonly
                                >
                            </div>
                            <div class="file-preview box sm">
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">{{ translate('Video 2') }}</label>
                        <div class="col-md-8">
                            <div
                                class="input-group"
                                data-toggle="aizuploader"
                                data-type="video"
                            >
                                <div class="input-group-prepend">
                                    <div class="input-group-text bg-soft-secondary font-weight-medium">
                                        {{ translate('Browse') }}</div>
                                </div>
                                <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                <input
                                    class="selected-files"
                                    name="video2"
                                    type="hidden"
                                    value="{{ $product->video2 }}"
                                    required
                                    readonly
                                >
                            </div>
                            <div class="file-preview box sm">
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">{{ translate('Video 3') }}</label>
                        <div class="col-md-8">
                            <div
                                class="input-group"
                                data-toggle="aizuploader"
                                data-type="video"
                            >
                                <div class="input-group-prepend">
                                    <div class="input-group-text bg-soft-secondary font-weight-medium">
                                        {{ translate('Browse') }}</div>
                                </div>
                                <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                <input
                                    class="selected-files"
                                    name="video3"
                                    type="hidden"
                                    value="{{ $product->video3 }}"
                                    required
                                    readonly
                                >
                            </div>
                            <div class="file-preview box sm">
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">{{ translate('Video 4') }}</label>
                        <div class="col-md-8">
                            <div
                                class="input-group"
                                data-toggle="aizuploader"
                                data-type="video"
                            >
                                <div class="input-group-prepend">
                                    <div class="input-group-text bg-soft-secondary font-weight-medium">
                                        {{ translate('Browse') }}</div>
                                </div>
                                <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                <input
                                    class="selected-files"
                                    name="video4"
                                    type="hidden"
                                    value="{{ $product->video4 }}"
                                    required
                                    readonly
                                >
                            </div>
                            <div class="file-preview box sm">
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">{{ translate('Ficha tecnica imagen 1') }}</label>
                        <div class="col-md-8">
                            <div
                                class="input-group"
                                data-toggle="aizuploader"
                                data-type="image"
                            >
                                <div class="input-group-prepend">
                                    <div class="input-group-text bg-soft-secondary font-weight-medium">
                                        {{ translate('Browse') }}</div>
                                </div>
                                <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                <input
                                    class="selected-files"
                                    name="ficha_tecnica_imagen1"
                                    type="hidden"
                                    value="{{ $product->ficha_tecnica_imagen1 }}"
                                    required
                                    readonly
                                >
                            </div>
                            <div class="file-preview box sm">
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">{{ translate('Ficha tecnica imagen 2') }}</label>
                        <div class="col-md-8">
                            <div
                                class="input-group"
                                data-toggle="aizuploader"
                                data-type="image"
                            >
                                <div class="input-group-prepend">
                                    <div class="input-group-text bg-soft-secondary font-weight-medium">
                                        {{ translate('Browse') }}</div>
                                </div>
                                <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                <input
                                    class="selected-files"
                                    name="ficha_tecnica_imagen2"
                                    type="hidden"
                                    value="{{ $product->ficha_tecnica_imagen2 }}"
                                    required
                                    readonly
                                >
                            </div>
                            <div class="file-preview box sm">
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">{{ translate('Ficha tecnica imagen 3') }}</label>
                        <div class="col-md-8">
                            <div
                                class="input-group"
                                data-toggle="aizuploader"
                                data-type="image"
                            >
                                <div class="input-group-prepend">
                                    <div class="input-group-text bg-soft-secondary font-weight-medium">
                                        {{ translate('Browse') }}</div>
                                </div>
                                <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                <input
                                    class="selected-files"
                                    name="ficha_tecnica_imagen3"
                                    type="hidden"
                                    value="{{ $product->ficha_tecnica_imagen3 }}"
                                    required
                                    readonly
                                >
                            </div>
                            <div class="file-preview box sm">
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">{{ translate('Ficha tecnica imagen 4') }}</label>
                        <div class="col-md-8">
                            <div
                                class="input-group"
                                data-toggle="aizuploader"
                                data-type="image"
                            >
                                <div class="input-group-prepend">
                                    <div class="input-group-text bg-soft-secondary font-weight-medium">
                                        {{ translate('Browse') }}</div>
                                </div>
                                <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                <input
                                    class="selected-files"
                                    name="ficha_tecnica_imagen4"
                                    type="hidden"
                                    value="{{ $product->ficha_tecnica_imagen4 }}"
                                    required
                                    readonly
                                >
                            </div>
                            <div class="file-preview box sm">
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">{{ translate('manual_de_producto') }}</label>
                        <div class="col-md-8">
                            <div
                                class="input-group"
                                data-toggle="aizuploader"
                                data-type="image"
                            >
                                <div class="input-group-prepend">
                                    <div class="input-group-text bg-soft-secondary font-weight-medium">
                                        {{ translate('Browse') }}</div>
                                </div>
                                <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                <input
                                    class="selected-files"
                                    name="manual_de_producto"
                                    type="hidden"
                                    value="{{ $product->manual_de_producto }}"
                                    required
                                    readonly
                                >
                            </div>
                            <div class="file-preview box sm">
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">{{ translate('ficha_tecnica_del_producto') }}</label>
                        <div class="col-md-8">
                            <div
                                class="input-group"
                                data-toggle="aizuploader"
                                data-type="image"
                            >
                                <div class="input-group-prepend">
                                    <div class="input-group-text bg-soft-secondary font-weight-medium">
                                        {{ translate('Browse') }}</div>
                                </div>
                                <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                <input
                                    class="selected-files"
                                    name="ficha_tecnica_del_producto"
                                    type="hidden"
                                    value="{{ $product->ficha_tecnica_del_producto }}"
                                    required
                                    readonly
                                >
                            </div>
                            <div class="file-preview box sm">
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">{{ translate('manual_de_instalacion') }}</label>
                        <div class="col-md-8">
                            <div
                                class="input-group"
                                data-toggle="aizuploader"
                                data-type="image"
                            >
                                <div class="input-group-prepend">
                                    <div class="input-group-text bg-soft-secondary font-weight-medium">
                                        {{ translate('Browse') }}</div>
                                </div>
                                <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                <input
                                    class="selected-files"
                                    name="manual_de_instalacion"
                                    type="hidden"
                                    value="{{ $product->manual_de_instalacion }}"
                                    required
                                    readonly
                                >
                            </div>
                            <div class="file-preview box sm">
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-from-label">{{ translate('vida_util') }}</label>
                        <div class="col-md-8">
                            <div class="input-group">
                                <input
                                    class="form-control"
                                    name="vida_util"
                                    type="text"
                                    value="{{ $product->vida_util }}"
                                    required
                                    readonly
                                    placeholder="{{ translate('vida_util') }}"
                                >
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-from-label">{{ translate('plastico') }}</label>
                        <div class="col-md-8">
                            <div class="input-group">
                                <input
                                    class="form-control"
                                    name="plastico"
                                    type="text"
                                    value="{{ $product->plastico }}"
                                    required
                                    readonly
                                    placeholder="{{ translate('plastico') }}"
                                >
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-from-label">{{ translate('carton') }}</label>
                        <div class="col-md-8">
                            <div class="input-group">
                                <input
                                    class="form-control"
                                    name="carton"
                                    type="text"
                                    value="{{ $product->carton }}"
                                    required
                                    readonly
                                    placeholder="{{ translate('carton') }}"
                                >
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-from-label">{{ translate('peso_carton') }}</label>
                        <div class="col-md-8">
                            <div class="input-group">
                                <input
                                    class="form-control"
                                    name="peso_carton"
                                    type="text"
                                    value="{{ $product->peso_carton }}"
                                    required
                                    readonly
                                    placeholder="{{ translate('peso_carton') }}"
                                >
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-from-label">{{ translate('papel') }}</label>
                        <div class="col-md-8">
                            <div class="input-group">
                                <input
                                    class="form-control"
                                    name="papel"
                                    type="text"
                                    value="{{ $product->papel }}"
                                    required
                                    readonly
                                    placeholder="{{ translate('papel') }}"
                                >
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-from-label">{{ translate('peso_papel') }}</label>
                        <div class="col-md-8">
                            <div class="input-group">
                                <input
                                    class="form-control"
                                    name="peso_papel"
                                    type="text"
                                    value="{{ $product->peso_papel }}"
                                    required
                                    readonly
                                    placeholder="{{ translate('peso_papel') }}"
                                >
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-from-label">{{ translate('metal') }}</label>
                        <div class="col-md-8">
                            <div class="input-group">
                                <input
                                    class="form-control"
                                    name="metal"
                                    type="text"
                                    value="{{ $product->metal }}"
                                    required
                                    readonly
                                    placeholder="{{ translate('metal') }}"
                                >
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-from-label">{{ translate('peso_metal') }}</label>
                        <div class="col-md-8">
                            <div class="input-group">
                                <input
                                    class="form-control"
                                    name="peso_metal"
                                    type="text"
                                    value="{{ $product->peso_metal }}"
                                    required
                                    readonly
                                    placeholder="{{ translate('peso_metal') }}"
                                >
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-from-label">{{ translate('vidrio') }}</label>
                        <div class="col-md-8">
                            <div class="input-group">
                                <input
                                    class="form-control"
                                    name="vidrio"
                                    type="text"
                                    value="{{ $product->vidrio }}"
                                    required
                                    readonly
                                    placeholder="{{ translate('vidrio') }}"
                                >
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-from-label">{{ translate('peso_vidrio') }}</label>
                        <div class="col-md-8">
                            <div class="input-group">
                                <input
                                    class="form-control"
                                    name="peso_vidrio"
                                    type="text"
                                    value="{{ $product->peso_vidrio }}"
                                    required
                                    readonly
                                    placeholder="{{ translate('peso_vidrio') }}"
                                >
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-from-label">{{ translate('madera') }}</label>
                        <div class="col-md-8">
                            <div class="input-group">
                                <input
                                    class="form-control"
                                    name="madera"
                                    type="text"
                                    value="{{ $product->madera }}"
                                    required
                                    readonly
                                    placeholder="{{ translate('madera') }}"
                                >
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-from-label">{{ translate('peso_madera') }}</label>
                        <div class="col-md-8">
                            <div class="input-group">
                                <input
                                    class="form-control"
                                    name="peso_madera"
                                    type="text"
                                    value="{{ $product->peso_madera }}"
                                    required
                                    readonly
                                    placeholder="{{ translate('peso_madera') }}"
                                >
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-from-label">{{ translate('textil') }}</label>
                        <div class="col-md-8">
                            <div class="input-group">
                                <input
                                    class="form-control"
                                    name="textil"
                                    type="text"
                                    value="{{ $product->textil }}"
                                    required
                                    readonly
                                    placeholder="{{ translate('textil') }}"
                                >
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-from-label">{{ translate('peso_textil') }}</label>
                        <div class="col-md-8">
                            <div class="input-group">
                                <input
                                    class="form-control"
                                    name="peso_textil"
                                    type="text"
                                    value="{{ $product->peso_textil }}"
                                    required
                                    readonly
                                    placeholder="{{ translate('peso_textil') }}"
                                >
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-from-label">{{ translate('bateria_electrico') }}</label>
                        <div class="col-md-8">
                            <div class="input-group">
                                <input
                                    class="form-control"
                                    name="bateria_electrico"
                                    type="text"
                                    value="{{ $product->bateria_electrico }}"
                                    required
                                    readonly
                                    placeholder="{{ translate('bateria_electrico') }}"
                                >
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-from-label">{{ translate('peso_bateria_electrico') }}</label>
                        <div class="col-md-8">
                            <div class="input-group">
                                <input
                                    class="form-control"
                                    name="peso_bateria_electrico"
                                    type="text"
                                    value="{{ $product->peso_bateria_electrico }}"
                                    required
                                    readonly
                                    placeholder="{{ translate('peso_bateria_electrico') }}"
                                >
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-from-label">{{ translate('impacto_ambiental') }}</label>
                        <div class="col-md-8">
                            <div class="input-group">
                                <input
                                    class="form-control"
                                    name="impacto_ambiental"
                                    type="text"
                                    value="{{ $product->impacto_ambiental }}"
                                    required
                                    readonly
                                    placeholder="{{ translate('impacto_ambiental') }}"
                                >
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
