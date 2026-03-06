<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DevisLine extends Model
{
    use HasFactory;

    /**
     * Nom de la table associée.
     */
    protected $table = 'devis_lines';

    /**
     * Les attributs qui sont assignables en masse.
     * Ajout de 'reference' et 'image'.
     */
    protected $fillable = [
        'devis_id',
        'product_name',
        'reference',     // Nouveau champ
        'image',           // Nouveau champ (stockera le chemin du fichier)
        'unit_price_ht',
        'quantity',
        'total_ht',
    ];

    /**
     * Relation : Une ligne de devis appartient à un devis.
     */
    public function devis()
    {
        return $this->belongsTo(Devis::class, 'devis_id');
    }
}