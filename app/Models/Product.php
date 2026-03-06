<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    /**
     * Les colonnes autorisées pour l'assignation de masse.
     */
    protected $fillable = [
        'name',
        'price',
        'reference',
        'suppliername',
        'description',
        'count',
        'image',
        'status',
        'brand',
        'date_product', // AJOUTÉ : Pour le filtrage précis par mois dans le Dashboard
    ];

    /**
     * Conversion automatique des types.
     */
    protected $casts = [
        'date_product' => 'date', // AJOUTÉ : Permet de manipuler ce champ comme une date Carbon
        'price'        => 'integer',
        'count'        => 'integer',
        'created_at'   => 'datetime',
        'updated_at'   => 'datetime',
    ];
}