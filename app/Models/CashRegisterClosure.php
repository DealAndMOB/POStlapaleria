<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashRegisterClosure extends Model
{
    use HasFactory;

    protected $fillable = [
        'start_time', 'end_time', 'initial_cash', 'final_cash',
        'total_sales', 'expected_cash', 'difference', 'notes', 'user_id'
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }
}