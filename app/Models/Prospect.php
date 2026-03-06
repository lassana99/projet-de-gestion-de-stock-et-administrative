<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prospect extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'domain',
        'contact',      // Conservé pour la compatibilité avec la table actuelle
        'email',
        'website',
        'need',
        'comment',
        'statut_achat',
        'date',
    ];

    /**
     * Conversion automatique des types.
     * Permet de manipuler la date avec Carbon (ex: $prospect->date->format('d/m/Y'))
     */
    protected $casts = [
        'date' => 'date',
    ];

    /**
     * Obtenir les personnes à contacter associées au prospect.
     * Relation Un à Plusieurs (One-to-Many)
     */
    public function contacts()
    {
        return $this->hasMany(ProspectContact::class, 'prospect_id');
    }
}