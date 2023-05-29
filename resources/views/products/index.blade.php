@extends('layouts.app')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Products</h1>
    </div>


    <div class="card">
        <form action="{{ route('product.index') }}" method="get" class="card-header">
            <div class="form-row justify-content-between">
                <div class="col-md-2">
                    <input type="text" name="title" placeholder="Product Title" class="form-control"
                        value="{{ $s_title ?? '' }}">
                </div>
                <div class="col-md-2">
                    <select name="variant" id="" class="form-control js-example-templating">
                        <option value="">--Select A Variant--</option>
                        @foreach ($variants as $variant_list)
                            <optgroup label="{{ $variant_list->title }}">
                                @foreach ($variant_list->productVariants as $vp_list)
                                    <option value="{{ $vp_list->variant_id }}"
                                        @if ($vp_list->variant_id == $s_variant) {{ 'selected' }} @endif>
                                        {{ ucfirst($vp_list->variant) }}</option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Price Range</span>
                        </div>
                        <input type="text" name="price_from" aria-label="First name" placeholder="From"
                            class="form-control" value="{{ $s_price_from ?? '' }}">
                        <input type="text" name="price_to" aria-label="Last name" placeholder="To" class="form-control"
                            value="{{ $s_price_to ?? '' }}">
                    </div>
                </div>
                <div class="col-md-2">
                    <input type="date" name="date" placeholder="Date" class="form-control"
                        value="{{ $s_date ?? '' }}">
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-primary float-right"><i class="fa fa-search"></i></button>
                </div>
            </div>
        </form>

        <div class="card-body">
            <div class="table-response">
                <table class="table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Title</th>
                            <th>Description</th>
                            <th>Variant</th>
                            <th width="150px">Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @php
                            $start = $products->currentPage();
                            $end = $products->lastPage();
                            $total = $products->total();
                            $per = $products->perPage();
                            
                            if ($end - $start != 0) {
                                $start = $start * $per - $per + 1;
                                $end = $start + $per - 1;
                            } else {
                                $start = $start * $per - $per + 1;
                                $end = $start + ($total - $start);
                            }
                        @endphp
                        @foreach ($products as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $item->title }} <br> Created at : {{ $item->created_at->format('d-F-Y') }}</td>
                                <td>{{ $item->description }}</td>
                                <td>
                                    <dl class="row mb-0" style="height: 80px; overflow: hidden" id="variant">

                                        <dt class="col-sm-3 pb-0">
                                            @foreach ($item->productVariants->groupBy('variant_id') as $pv_item)
                                                @foreach ($pv_item as $variant)
                                                    {{ $variant['variant'] }}@if (!$loop->last)
                                                        {{ '/ ' }}
                                                    @endif
                                                @endforeach
                                                <br>
                                            @endforeach
                                        </dt>
                                        <dd class="col-sm-9">
                                            <dl class="row mb-0">
                                                @foreach ($item->productVariantPrice as $pvp_item)
                                                    <dt class="col-sm-4 pb-0">Price :
                                                        {{ number_format($pvp_item->price, 2) }}
                                                    </dt>
                                                    <dd class="col-sm-8 pb-0">InStock :
                                                        {{ number_format($pvp_item->stock, 2) }}
                                                    </dd>
                                                @endforeach
                                            </dl>
                                        </dd>
                                    </dl>
                                    {{-- <button onclick="$('#variant').toggleClass('h-auto')" class="btn btn-sm btn-link">Show
                                        more</button> --}}
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('product.edit', $item->id) }}" class="btn btn-success">Edit</a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>

                </table>
            </div>

        </div>

        <div class="card-footer">
            <div class="row justify-content-between">
                <div class="col-md-6">
                    <p>Showing {{ $products->count() > 0 ? $start : 0 }} to {{ $end }} out of
                        {{ $products->total() }}
                    </p>
                </div>
                <div class="col-md-2">
                    {{ $products->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
@push('page_js')
    <script>
        $(".js-example-templating").select2({
            templateResult: formatState
        });
    </script>
@endpush
