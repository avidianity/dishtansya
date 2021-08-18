<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = ['quantity'];

    protected static function booted()
    {
        static::created(function (self $order) {
            $order->product->available_stock -= $order->quantity;
            $order->product->save();
        });
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
