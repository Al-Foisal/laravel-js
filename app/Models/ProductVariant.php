<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model {
    public function varient() {
        return $this->belongsTo(Variant::class, 'variant_id', 'id');
    }
    public function _varient() {
        return $this->belongsTo(Variant::class, 'variant_id', 'id');
    }
}
