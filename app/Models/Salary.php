<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Salary extends Model
{
    use HasFactory;

    protected $fillable = [
        'number', 'full_name', 'position', 'id_number', 'id_type', 
        'id_type_other', 'amount_fcfa', 'payment_date', 'payment_mode', 'additional_details'
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount_fcfa' => 'decimal:2',
    ];

    public function formattedAmount() {
        return number_format($this->amount_fcfa, 0, ',', ' ');
    }

    public function getRealIdType() {
        return $this->id_type === 'Autres' ? $this->id_type_other : $this->id_type;
    }
}