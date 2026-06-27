@extends('backend.layouts.app')

@section('content')
    <div class="aiz-titlebar text-left mt-2 mb-3">
        <h1 class="mb-0 h6">{{ translate('Edit Product') }}</h1>
    </div>
    <form
        class="form form-horizontal mar-top"
        action="{{ route('product.update', $product->id) }}"
        method="POST"
        enctype="multipart/form-data"
        id="product_form"
    >
        @csrf
        <input type="hidden" name="id" value="{{ $product->id }}">
        <input type="hidden" name="lang" value="{{ $lang }}">
        
        <ul class="nav nav-tabs nav-fill border-light">
            @foreach (\App\Models\Language::where('status', 1)->get() as $key => $language)
                <li class="nav-item">
                    <a class="nav-link text-reset @if ($language->code == $lang) active @else bg-soft-dark border-light border-left-0 @endif py-3" href="{{ route('product.edit', ['id' => $product->id, 'lang' => $language->code]) }}">
                        <img src="{{ static_asset('assets/img/flags/' . $language->flag . '.png') }}" height="11" class="mr-1">
                        <span>{{ $language->name }}</span>
                    </a>
                </li>
            @endforeach
        </ul>

        <div class="row gutters-5">
            <div class="col-lg">

                <!-- Product Basic Information -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0 h6">{{ translate('Product Information') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group row">
                            <label class="col-md-3 col-from-label">{{ translate('Reference') }} <span class="text-danger">*</span></label>
                            <div class="col-md-8">
                                <input class="form-control" name="reference" type="text" value="{{ $product->reference }}" placeholder="{{ translate('Reference') }}" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-from-label">{{ translate('Product Name') }} <span class="text-danger">*</span></label>
                            <div class="col-md-8">
                                <input type="text" class="form-control" name="name" value="{{ $product->name }}" placeholder="{{ translate('Product Name') }}" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-from-label">{{ translate('Brand') }}</label>
                            <div class="col-md-8">
                                <select class="form-control aiz-selectpicker" name="brand_id" data-live-search="true" title="{{ translate('Select Brand') }}" data-selected="{{ $product->brand_id }}">
                                    @foreach (\App\Models\Brand::all() as $brand)
                                        <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-from-label">{{ translate('Unit') }}</label>
                            <div class="col-md-8">
                                <input type="text" class="form-control" name="unit" value="{{ $product->getTranslation('unit', $lang) }}" placeholder="{{ translate('Unit') }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-from-label">{{ translate('Minimum Purchase Qty') }} <span class="text-danger">*</span></label>
                            <div class="col-md-8">
                                <input type="number" class="form-control" name="min_qty" min="1" value="{{ $product->min_qty }}" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-from-label">{{ translate('Maximum Purchase Qty') }}</label>
                            <div class="col-md-8">
                                <input type="number" class="form-control" name="max_qty" value="{{ $product->max_qty }}" min="0">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-from-label">{{ translate('Categories') }} <span class="text-danger">*</span></label>
                            <div class="col-md-8">
                                <select class="form-control aiz-selectpicker" name="category_ids[]" multiple data-live-search="true" data-selected="{{ json_encode($product->categories->pluck('id')->toArray()) }}" required>
                                    @foreach (\App\Models\Category::all() as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Product Images -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0 h6">{{ translate('Product Images') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">{{ translate('Thumbnail Image') }} <small>(300x300)</small></label>
                            <div class="col-md-8">
                                <div class="input-group" data-toggle="aizuploader" data-type="image">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse') }}</div>
                                    </div>
                                    <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                    <input type="hidden" name="thumbnail_img" class="selected-files" value="{{ $product->thumbnail_img }}">
                                </div>
                                <div class="file-preview box sm"></div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">{{ translate('Gallery Images') }} <small>(600x600)</small></label>
                            <div class="col-md-8">
                                <div class="input-group" data-toggle="aizuploader" data-type="image" data-multiple="true">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse') }}</div>
                                    </div>
                                    <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                    <input type="hidden" name="photos" class="selected-files" value="{{ $product->photos }}">
                                </div>
                                <div class="file-preview box sm"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pricing & Stock -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0 h6">{{ translate('Pricing & Stock') }}</h5>
                        <div class="d-flex mt-2">
                            <label class="mb-0 mr-3 ml-0">{{ translate('Variant Product') }}</label>
                            <label class="aiz-switch aiz-switch-success mb-0">
                                <input type="checkbox" name="is_variant" onchange="is_variant_product(this)" @if ($product->is_variant) checked @endif>
                                <span></span>
                            </label>
                        </div>
                    </div>
                    <div class="card-body">
                        @php
                            $first_variation = $product->variations->first();
                            $price = (!$product->is_variant && $first_variation) ? $first_variation->price : 0;
                            $sku = (!$product->is_variant && $first_variation) ? $first_variation->sku : null;
                            $stock = (!$product->is_variant && $first_variation) ? $first_variation->stock : 1;
                        @endphp
                        <div class="form-group row">
                            <label class="col-md-3 col-from-label">{{ translate('Currency') }}</label>
                            <div class="col-md-8">
                                <input class="form-control" name="currency" type="text" value="{{ $product->currency }}" placeholder="{{ translate('Currency') }}">
                            </div>
                        </div>
                        <div class="no_product_variant" @if ($product->is_variant) style="display:none;" @endif>
                            <div class="form-group row">
                                <label class="col-md-3 col-from-label">{{ translate('Regular price') }} <span class="text-danger">*</span></label>
                                <div class="col-md-8">
                                    <input type="number" step="0.01" min="0" value="{{ $product->highest_price }}" name="highest_price" class="form-control" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 col-from-label">{{ translate('SKU') }}</label>
                                <div class="col-md-8">
                                    <input type="text" value="{{ $sku }}" name="sku" class="form-control" placeholder="{{ translate('SKU') }}">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 col-from-label">{{ translate('Stock') }} <span class="text-danger">*</span></label>
                                <div class="col-md-8">
                                    <select class="form-control aiz-selectpicker" name="stock" data-selected="{{ $stock }}">
                                        <option value="1">{{ translate('In stock') }}</option>
                                        <option value="0">{{ translate('Out of stock') }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="has_product_variant" @if (!$product->is_variant) style="display:none;" @endif>
                            <div class="alert alert-info">{{ translate('Select options and their values. Max 3 options') }}</div>
                            <div class="customer_choice_options">
                                @forelse (generate_variation_options($product->variation_combinations) as $key => $combination)
                                    <div class="form-group row gutters-10">
                                        <div class="col-xxl-3 col-xl-4 col-md-5">
                                            <select class="form-control aiz-selectpicker" name="product_options[]" onchange="get_option_choices(this)" data-live-search="true" title="{{ translate('Select option') }}" data-selected="{{ $combination['id'] }}">
                                                @foreach ($all_attributes as $attribute)
                                                    <option value="{{ $attribute->id }}">{{ $attribute->getTranslation('name') }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col">
                                            @php
                                                $attribute_values = \App\Models\AttributeValue::where('attribute_id', $combination['id'])->get();
                                                $old_val = array_map(function ($val) { return $val['id']; }, $combination['values']);
                                            @endphp
                                            <select class="form-control aiz-selectpicker" name="option_{{ $combination['id'] }}_choices[]" multiple data-live-search="true" onchange="update_sku()" data-selected="{{ json_encode($old_val) }}">
                                                @foreach ($attribute_values as $attribute_value)
                                                    <option value="{{ $attribute_value->id }}">{{ $attribute_value->getTranslation('name') }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @if ($key == 0)
                                            <div class="col-auto">
                                                <button type="button" class="btn btn-icon btn-soft-secondary" onclick="add_new_option()">
                                                    <i class="la-plus las opacity-70"></i>
                                                </button>
                                            </div>
                                        @else
                                            <div class="col-auto">
                                                <button type="button" data-toggle="remove-parent" class="btn btn-icon p-0" data-parent=".row" onclick="update_sku()">
                                                    <i class="la-2x la-trash las opacity-70"></i>
                                                </button>
                                            </div>
                                        @endif
                                    </div>
                                @empty
                                    <div class="form-group row gutters-10">
                                        <div class="col-xxl-3 col-xl-4 col-md-5">
                                            <select class="form-control aiz-selectpicker" name="product_options[]" onchange="get_option_choices(this)" data-live-search="true" title="{{ translate('Select option') }}">
                                                @foreach ($all_attributes as $attribute)
                                                    <option value="{{ $attribute->id }}">{{ $attribute->getTranslation('name') }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col">
                                            <div class="form-control"><span>{{ translate('Select an option') }}</span></div>
                                        </div>
                                        <div class="col-auto">
                                            <button type="button" class="btn btn-icon btn-soft-secondary" onclick="add_new_option()">
                                                <i class="la-plus las opacity-70"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endforelse
                            </div>
                            <div class="sku_combination" id="sku_combination">
                                @if ($product->is_variant)
                                    @include('backend.product.products.sku_combinations_edit', ['variations' => $product->variations])
                                @endif
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-from-label">{{ translate('Warranty Period') }} <span class="text-danger">*</span></label>
                            <div class="col-md-8">
                                <input class="form-control" name="warranty_text" type="text" value="{{ $product->warranty_text }}" placeholder="{{ translate('e.g. 1 Year') }}" required>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Discount -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0 h6">{{ translate('Discount') }}</h5>
                    </div>
                    <div class="card-body">
                        @php
                            if ($product->discount_start_date) {
                                $start_date = date('d-m-Y H:i:s', $product->discount_start_date);
                                $end_date = date('d-m-Y H:i:s', $product->discount_end_date);
                                $discount_date = $start_date . ' to ' . $end_date;
                            } else {
                                $discount_date = '';
                            }
                        @endphp
                        <div class="form-group row">
                            <label class="col-sm-3 control-label">{{ translate('Discount Date Range') }}</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control aiz-date-range" name="date_range" placeholder="Select Date" data-time-picker="true" data-format="DD-MM-Y HH:mm:ss" data-separator=" to " value="{{ $discount_date }}" autocomplete="off">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-from-label">{{ translate('Discount') }} <span class="text-danger">*</span></label>
                            <div class="col-md-6">
                                <input type="number" lang="en" min="0" value="{{ $product->discount }}" step="0.01" name="discount" class="form-control" required>
                            </div>
                            <div class="col-md-3">
                                <select class="form-control aiz-selectpicker" name="discount_type" data-selected="{{ $product->discount_type }}">
                                    <option value="flat">{{ translate('Flat') }}</option>
                                    <option value="percent">{{ translate('Percent') }}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                @if (get_setting('club_point'))
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0 h6">{{ translate('Club Point') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="form-group row">
                                <label class="col-sm-3 control-label">{{ translate('Points') }}</label>
                                <div class="col-sm-9">
                                    <input type="number" lang="en" min="0" value="{{ $product->earn_point }}" step="1" name="earn_point" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Shipping -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0 h6">{{ translate('Shipping Information') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group row">
                            <label class="col-md-3 col-from-label">{{ translate('Standard Delivery Time') }}</label>
                            <div class="col-md-8">
                                <div class="input-group">
                                    <input type="number" step="0.01" class="form-control" name="standard_delivery_time" min="0" value="{{ $product->standard_delivery_time }}" required>
                                    <div class="input-group-append"><span class="input-group-text">hr(s)</span></div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-from-label">{{ translate('Express Delivery Time') }}</label>
                            <div class="col-md-8">
                                <div class="input-group">
                                    <input type="number" step="0.01" class="form-control" name="express_delivery_time" min="0" value="{{ $product->express_delivery_time }}" required>
                                    <div class="input-group-append"><span class="input-group-text">hr(s)</span></div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-from-label">{{ translate('Weight') }}</label>
                            <div class="col-md-8">
                                <div class="input-group">
                                    <input type="number" step="0.01" class="form-control" name="weight" min="0" value="{{ $product->weight }}" required>
                                    <div class="input-group-append"><span class="input-group-text">{{ get_setting('weight_unit') }}</span></div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-from-label">{{ translate('Height') }}</label>
                            <div class="col-md-8">
                                <div class="input-group">
                                    <input type="number" step="0.01" class="form-control" name="height" min="0" value="{{ $product->height }}" required>
                                    <div class="input-group-append"><span class="input-group-text">{{ get_setting('dimension_unit') }}</span></div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-from-label">{{ translate('Length') }}</label>
                            <div class="col-md-8">
                                <div class="input-group">
                                    <input type="number" step="0.01" class="form-control" name="length" min="0" value="{{ $product->length }}" required>
                                    <div class="input-group-append"><span class="input-group-text">{{ get_setting('dimension_unit') }}</span></div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-from-label">{{ translate('Width') }}</label>
                            <div class="col-md-8">
                                <div class="input-group">
                                    <input type="number" step="0.01" class="form-control" name="width" min="0" value="{{ $product->width }}" required>
                                    <div class="input-group-append"><span class="input-group-text">{{ get_setting('dimension_unit') }}</span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Description -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0 h6">{{ translate('Description') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group row">
                            <label class="col-md-3 col-from-label">{{ translate('Description') }}</label>
                            <div class="col-md-8">
                                <textarea class="aiz-text-editor" name="description">{!! $product->getTranslation('description', $lang) !!}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SEO -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0 h6">{{ translate('SEO Information') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group row">
                            <label class="col-md-3 col-from-label">{{ translate('Meta Title') }}</label>
                            <div class="col-md-8">
                                <input type="text" class="form-control" name="meta_title" value="{{ $product->meta_title }}" placeholder="{{ translate('Meta Title') }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-from-label">{{ translate('Meta Description') }}</label>
                            <div class="col-md-8">
                                <textarea class="form-control" name="meta_description" placeholder="{{ translate('Meta Description') }}">{{ $product->meta_description }}</textarea>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">{{ translate('Meta Image') }}</label>
                            <div class="col-md-8">
                                <div class="input-group" data-toggle="aizuploader" data-type="image">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse') }}</div>
                                    </div>
                                    <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                    <input type="hidden" name="meta_image" class="selected-files" value="{{ $product->meta_image }}">
                                </div>
                                <div class="file-preview box sm"></div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="col-lg-3">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0 h6">{{ translate('Publish') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label>{{ translate('Status') }}</label>
                            <select class="form-control aiz-selectpicker" name="status" data-selected="{{ $product->published }}">
                                <option value="0">{{ translate('Draft') }}</option>
                                <option value="1">{{ translate('Published') }}</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <button type="submit" class="btn btn-primary btn-block">{{ translate('Update Product') }}</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection
