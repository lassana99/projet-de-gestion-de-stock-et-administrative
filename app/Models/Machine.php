<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Machine extends Model
{
    use HasFactory;

    protected $table = 'machines_profitabilities';

    /**
     * Les attributs qui peuvent être assignés en masse.
     */
    protected $fillable = [
        'purchase_reference',
        'unit_purchase_price',
        'weight',
        'brand',
        'status',
        'conversion_rate',
        'urban_transport',
        'concierge',
        'transport_source_bko',
        'customs',
        'land_transport',
        'margin',
        'date_profitability', // AJOUTÉ : Pour le filtrage précis par mois
        'selling_price',
        'tva',
        'selling_price_ttc',
        'profit',
        'funding',
        'suppliername',
        'request',
        'global_urbain_transport',
        'quantity',
    ];

    /**
     * Relation avec le modèle Purchase.
     */
    public function purchase()
    {
        return $this->belongsTo(Purchase::class, 'purchase_reference', 'reference');
    }

    /**
     * Conversion automatique des types.
     */
    protected $casts = [
        'date_profitability' => 'date', // AJOUTÉ : Pour manipuler la date avec Carbon
        'selling_price'      => 'decimal:2',
        'tva'                => 'decimal:2',
        'selling_price_ttc'  => 'decimal:2',
        'profit'             => 'decimal:2',
    ];
}