<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tax extends Model
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

    // Helper pour afficher la description complète
    public function getFullDescriptionAttribute() {
        return $this->description === 'Autres' ? $this->description_other : $this->description;
    }
}