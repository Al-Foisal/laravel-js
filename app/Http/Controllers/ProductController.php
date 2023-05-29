<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ProductVariantPrice;
use App\Models\Variant;
use Illuminate\Http\Request;

class ProductController extends Controller {
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function index() {
        $data = [];

        $data['s_title']      = $title      = request()->title;
        $data['s_variant']    = $variant    = request()->variant;
        $data['s_price_from'] = $price_from = request()->price_from;
        $data['s_price_to']   = $price_to   = request()->price_to;
        $data['s_date']       = $date       = request()->date;

        $products = Product::query();

        if ($title) {
            $products = $products->where('title', 'LIKE', '%' . $title . '%');
        }

        if ($variant) {
            $variant  = ProductVariant::where('variant_id', $variant)->select('product_id')->distinct('product_id')->pluck('product_id')->toArray();
            $products = $products->whereIn('id', $variant);
        }

        if ($price_from && $price_to) {
            $product_price = ProductVariantPrice::whereBetween('price', [$price_from, $price_to])->select('product_id')->distinct('product_id')->pluck('product_id')->toArray();
            $products      = $products->whereIn('id', $product_price);
        }

        if ($date) {
            $products = $products->whereDate('created_at', $date);
        }

        $data['products'] = $products->with('productVariants')->paginate(5);
        $data['variants'] = Variant::with(['productVariants' => function ($query) {
            return $query->select(['variant', 'variant_id'])->distinct('variant_id');
        },
        ])->get();

        return view('products.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function create() {
        $variants = Variant::all();

        return view('products.create', compact('variants'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request) {

    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function show($product) {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product) {
        $variants      = Variant::all();
        $variant_price = ProductVariantPrice::where('product_id', $product->id)->get();

        return view('products.edit', compact('product', 'variants', 'variant_price'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product) {

        // dd($request->all());
        $product->title       = $request->product_name;
        $product->sku         = $request->product_sku;
        $product->description = $request->product_description;
        $product->save();

        $product_variant = ProductVariant::where('product_id', $product->id)->get();

//deleting all previous variant
        foreach ($product_variant as $pv) {
            $pv->delete();
        }

//storing new variant
        foreach ($request->product_variant as $key => $pv) {

            if (array_key_exists('value', $pv)) {
                $option = $pv['option'];

                foreach ($pv['value'] as $value) {
                    $pv_store             = new ProductVariant();
                    $pv_store->variant    = $value;
                    $pv_store->variant_id = $option;
                    $pv_store->product_id = $product->id;
                    $pv_store->save();
                }

            }

        }

        $variant_price = ProductVariantPrice::where('product_id', $product->id)->get();
        foreach ($variant_price as $price) {
            $price->delete();
        }

        foreach ($request->product_preview as $pp) {


            $types = explode('/', $pp['variant']);


            $product_variant_price = new ProductVariantPrice();

            foreach ($types as $type) {
                $get_product_variant = ProductVariant::where('product_id', $product->id)->where('variant', $type)->first();

                if ($get_product_variant) {

                    if ($get_product_variant->variant_id == 1) {

                        $product_variant_price->product_variant_one = $get_product_variant->id;
                    } else

                    if ($get_product_variant->variant_id == 2) {
                        $product_variant_price->product_variant_two = $get_product_variant->id;
                    } else {
                        $product_variant_price->product_variant_one = $get_product_variant->id;
                    }

                }

            }

            $product_variant_price->product_id = $product->id;
            $product_variant_price->price      = $pp['price'];
            $product_variant_price->stock      = $pp['stock'];
            $product_variant_price->save();

        }

        return redirect()->route('product.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product) {
        //
    }

}
