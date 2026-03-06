<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArticlePrice extends Model
{
    use HasFactory;

    // Nom de la table
    protected $table = 'article_prices';

    // Champs autorisés pour l’assignation de masse (Mass Assignment)
    protected $fillable = [
        'reference',
        'designation',
        'unit_price',
        'currency',
        'incoterm',    // Ajouté
        'country',     // Ajouté
        'type',
        'supplier_id',
        'supplier_name',
        'date',
    ];

    /**
     * CASTS
     * Conversion automatique des types de données
     */
    protected $casts = [
        'date' => 'date', 
        'unit_price' => 'decimal:2',
    ];

    /**
     * Relation avec le fournisseur
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    /**
     * Helper pour afficher le prix formaté (ex: 1 500,00)
     */
    public function formattedUnitPrice(): string
    {
        return number_format((float) $this->unit_price, 2, ',', ' ');
    }

    /**
     * Helper pour afficher la date formatée (ex: 31/12/2023)
     */
    public function formattedDate(): string
    {
        return $this->date ? $this->date->format('d/m/Y') : '-';
    }

    /**
     * Helper pour afficher le nom du fournisseur
     */
    public function supplierDisplay(): string
    {
        // Priorité au nom saisi manuellement, sinon on utilise la relation
        return $this->supplier_name ?? ($this->supplier?->company_name ?? '-');
    }
}