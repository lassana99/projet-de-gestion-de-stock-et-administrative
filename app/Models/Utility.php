<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Utility extends Model
{
    use HasFactory;

    protected $fillable = [
        'number', 'month', 'description', 'description_other', 
        'reference', 'issue_date', 'amount_fcfa', 'payment_mode'
    ];

    protected $casts = [
        'issue_date' => 'date',
        'amount_fcfa' => 'decimal:2',
    ];

    // Helper pour afficher le montant formaté
    public function formattedAmount(): string {
        return number_format($this->amount_fcfa, 0, ',', ' ');
    }

    // Helper pour afficher la description réelle
    public function displayDescription(): string {
        return $this->description === 'Autres' ? $this->description_other : $this->description;
    }
}