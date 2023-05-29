@extends('layouts.app')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Product</h1>
    </div>
    <form action="{{ route('product.update', $product) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <section>
            <div class="row">
                <div class="col-md-6">
                    <!--                    Product-->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold text-primary">Product</h6>
                        </div>
                        <div class="card-body border">
                            <div class="form-group">
                                <label for="product_name">Product Name</label>
                                <input type="text" name="product_name" id="product_name" required
                                    placeholder="Product Name" class="form-control" value="{{ $product->title }}">
                            </div>
                            <div class="form-group">
                                <label for="product_sku">Product SKU</label>
                                <input type="text" name="product_sku" id="product_sku" required
                                    placeholder="Product Name" class="form-control" value="{{ $product->sku }}">
                            </div>
                            <div class="form-group mb-0">
                                <label for="product_description">Description</label>
                                <textarea name="product_description" id="product_description" required rows="4" class="form-control">{{ $product->description }}</textarea>
                            </div>
                        </div>
                    </div>
                    <!--                    Media-->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold text-primary">Media</h6>
                        </div>
                        <div class="card-body border">
                            <div id="file-upload" class="dropzone dz-clickable">
                                <div class="dz-default dz-message"><span>Drop files here to upload</span></div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--                Variants-->

                <div class="col-md-6">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Variants</h6>
                        </div>
                        <div class="card-body pb-0" id="variant-sections">
                            @php
                                $count = 0;
                            @endphp
                            @foreach ($variants as $key => $variant)
                                @if ($variant->productVariants->where('product_id', $product->id)->count() > 0)
                                    @php
                                        $count++;
                                    @endphp
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="">Option</label>
                                                <select id="select2-option-{{ $key }}"
                                                    data-index="{{ $key }}"
                                                    name="product_variant[{{ $key }}][option]"
                                                    class="form-control custom-select select2 select2-option">
                                                    <option value="1"
                                                        @if ($variant->id == 1) {{ 'selected' }} @endif>
                                                        Color
                                                    </option>
                                                    <option value="2"
                                                        @if ($variant->id == 2) {{ 'selected' }} @endif>
                                                        Size
                                                    </option>
                                                    <option value="6"
                                                        @if ($variant->id == 6) {{ 'selected' }} @endif>
                                                        Style
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-8">
                                            <div class="form-group">
                                                <label class="d-flex justify-content-between">
                                                    <span>Value</span>
                                                    <a href="#" class="remove-btn" data-index="{{ $key }}"
                                                        onclick="removeVariant(event, this);">Remove</a>
                                                </label>
                                                <select id="select2-value-{{ $key }}"
                                                    data-index="{{ $key }}"
                                                    name="product_variant[{{ $key }}][value][]"
                                                    class="select2 select2-value form-control custom-select js-example-tags"
                                                    multiple="multiple">
                                                    @foreach ($variant->productVariants->where('product_id', $product->id) as $vp_item)
                                                        <option value="{{ $vp_item->variant }}" selected>
                                                            {{ $vp_item->variant }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                        <input type="hidden" id="cv" value="{{ $count }}">
                        @if ($count >= 3)
                        @else
                            <div class="card-footer bg-white border-top-0" id="add-btn">
                                <div class="row d-flex justify-content-center">
                                    <button class="btn btn-primary add-btn" onclick="addVariant(event);">
                                        Add another option
                                    </button>
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="card shadow">
                        <div class="card-header text-uppercase">Preview</div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr class="text-center">
                                            <th width="33%">Variant</th>
                                            <th>Price</th>
                                            <th>Stock</th>
                                        </tr>
                                    </thead>
                                    <tbody id="variant-previews">
                                        @foreach ($variant_price as $j => $price)
                                            <tr>
                                                <th>
                                                    <input type="hidden"
                                                        name="product_preview[{{ $j }}}][variant]"
                                                        value="{{ $price->pvo->variant . '/' . $price->pvt->variant . '/' . $price->pvt->variant }}">
                                                    <span
                                                        class="font-weight-bold">{{ $price->pvo->variant . '/' . $price->pvt->variant . '/' . $price->pvt->variant }}</span>
                                                </th>
                                                <td>
                                                    <input type="text" class="form-control" value="{{ $price->price }}"
                                                        name="product_preview[{{ $j }}}][price]" required>
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control" value="{{ $price->stock }}"
                                                        name="product_preview[{{ $j }}}][stock]">
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
            <button type="submit" class="btn btn-lg btn-primary">Save</button>
            <button type="button" class="btn btn-secondary btn-lg">Cancel</button>
        </section>
    </form>
@endsection

@push('page_js')
    <script>
        $(".js-example-tags").select2({
            tags: true
        });
    </script>
    <script type="text/javascript" src="{{ asset('js/products.js') }}"></script>
@endpush
