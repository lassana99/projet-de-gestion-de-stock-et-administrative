<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Delivery extends Model
{
    use HasFactory;

    protected $casts = [
        'delivery_date' => 'datetime', // Conversion automatique en Carbon
    ];

    protected $fillable = [
        'invoice_number',
        'customer_name',
        'customer_address',
        'delivery_status',
        'quantity',
        'delivery_date',
        'product_id',
        'image'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
