<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'domain',
        'contact',        // On le conserve pour la compatibilité avec la table actuelle
        'email',
        'payment_deadline',
        'payment_method',
        'nif',
        'rccm',
        'code_client',
        'date',
    ];

    /**
     * Conversion automatique des types.
     * Cela permet de manipuler la date avec Carbon (ex: $customer->date->format('d/m/Y'))
     */
    protected $casts = [
        'date' => 'date',
    ];

    /**
     * Obtenir les personnes à contacter associées au client.
     * Relation Un à Plusieurs (One-to-Many)
     */
    public function contacts()
    {
        return $this->hasMany(CustomerContact::class, 'customer_id');
    }
}