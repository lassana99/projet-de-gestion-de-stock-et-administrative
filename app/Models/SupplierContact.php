<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierContact extends Model
{
    use HasFactory;

    // Ajout de 'position' pour autoriser l'assignation de masse
    protected $fillable = [
        'supplier_id', 
        'name', 
        'position', 
        'phone', 
        'email'
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}