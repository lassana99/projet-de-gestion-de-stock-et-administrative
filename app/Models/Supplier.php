<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $table = 'suppliers';

    protected $fillable = [
        'company_name',
        'country_origin',
        'contact_person',
        'specialty',
        'brand',
        'contact',
        'email',
        'website',
        'payment_deadline',
        'nif',
        'date', // <--- Ajouté ici
    ];

    /**
     * Les attributs qui doivent être castés (convertis).
     */
    protected $casts = [
        'date' => 'date', // Permet de manipuler $supplier->date comme une instance Carbon
    ];

    /**
     * Obtenir les contacts associés au fournisseur.
     */
    public function contacts()
    {
        return $this->hasMany(SupplierContact::class, 'supplier_id');
    }
}