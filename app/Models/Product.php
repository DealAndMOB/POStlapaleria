<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'barcode',
        'description',
        'cost',
        'profit_percentage',
        'price',
        'stock',
        'category_id'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }


    public function calculatePrice()
    {
        return $this->cost * (1 + $this->profit_percentage / 100);
    }

   
    public function setPriceAttribute($value)
    {
        $this->attributes['price'] = $value;
        if ($this->cost > 0) {
            $this->attributes['profit_percentage'] = (($value / $this->cost) - 1) * 100;
        }
    }
}