<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariantPrice extends Model {
    public function pvo() {
        return $this->belongsTo(ProductVariant::class, 'product_variant_one', 'id');
    }

    public function pvt() {
        return $this->belongsTo(ProductVariant::class, 'product_variant_two', 'id');
    }

    public function pvth() {
        return $this->belongsTo(ProductVariant::class, 'product_variant_three', 'id');
    }
}
