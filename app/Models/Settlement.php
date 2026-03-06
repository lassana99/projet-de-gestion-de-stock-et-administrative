<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Settlement extends Model
{
    // Champs assignables en masse (incluant les nouveaux champs de devise et conversion)
    protected $fillable = [
        'type', 
        'entity_name', 
        'address', 
        'phone', 
        'email', 
        'amount', 
        'currency', 
        'amount_fcfa', 
        'issue_date', 
        'due_date', 
        'status'
    ];

    // Conversion automatique des dates
    protected $casts = [
        'issue_date' => 'date', 
        'due_date' => 'date'
    ];

    /**
     * Calculer les jours restants (ou de retard) avant l'échéance.
     * Un nombre négatif indique un retard.
     */
    public function getDaysRemainingAttribute()
    {
        return Carbon::now()->startOfDay()->diffInDays($this->due_date->startOfDay(), false);
    }

    /**
     * Vérifier si on doit afficher l'alerte.
     * L'alerte est désormais permanente tant que le statut est 'pending' (en attente),
     * peu importe le nombre de jours restants.
     */
    public function getShouldAlertAttribute()
    {
        return $this->status === 'pending';
    }
}