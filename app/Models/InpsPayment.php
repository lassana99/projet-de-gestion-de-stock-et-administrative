<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InpsPayment extends Model {
    use HasFactory;

    protected $fillable = ['number', 'start_date', 'end_date', 'amount_fcfa', 'payment_date', 'payment_mode', 'additional_details'];
    protected $casts = ['start_date' => 'date', 'end_date' => 'date', 'payment_date' => 'date', 'amount_fcfa' => 'decimal:2'];

    // Relation avec les employés
    public function employees() {
        return $this->belongsToMany(Employee::class, 'employee_inps_payment');
    }

    public function formattedAmount() {
        return number_format($this->amount_fcfa, 0, ',', ' ');
    }
}