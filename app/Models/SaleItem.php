<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaleItem extends Model
{
    protected $fillable = [
        'sale_id',
        'product_id',
        'external_product_name', // Agregar este campo
        'quantity',
        'price'
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Agregar un accessor para obtener el nombre del producto
    public function getProductNameAttribute()
    {
        return $this->product ? $this->product->name : $this->external_product_name;
    }
}