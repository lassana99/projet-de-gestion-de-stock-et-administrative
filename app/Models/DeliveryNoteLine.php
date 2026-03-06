<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryNoteLine extends Model
{
    use HasFactory;

    /**
     * En utilisant $guarded = [], vous autorisez l'assignation de masse pour tous les champs.
     * Les nouveaux champs 'reference' et 'image' seront donc automatiquement acceptés
     * lors de la création ou de la mise à jour via le contrôleur.
     */
    protected $guarded = [];

    protected $table = 'delivery_note_lines';

    /**
     * Relation : Une ligne appartient à un Bordereau de Livraison.
     */
    public function deliveryNote()
    {
        return $this->belongsTo(DeliveryNote::class);
    }
}