<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Purchase extends Model
{
    use HasFactory;

    protected $table = 'purchases';

    /**
     * Champs autorisés pour l'assignation de masse (Mass Assignment)
     */
    protected $fillable = [
        'reference',
        'description',
        'purchasename',
        'purchaseimage',
        'date_purchase', // AJOUTÉ : Pour le filtrage précis par mois
        'Category',
        'brand',
        'status',
        'purchaseprice',
        'purchase_price_fcfa', 
        'currency',
        'weight',         
        'incoterm',       
        'quantity',
        'suppliername',
        'country',
        'transport_mode', 
    ];

    /**
     * CASTS : Conversion automatique des types
     */
    protected $casts = [
        'purchaseprice' => 'decimal:2',
        'purchase_price_fcfa' => 'decimal:2',
        'weight' => 'decimal:2',
        'quantity' => 'integer',
        'date_purchase' => 'date', // AJOUTÉ : Transforme la valeur en objet Carbon (Date)
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}