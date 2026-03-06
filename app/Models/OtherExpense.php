<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OtherExpense extends Model
{
    use HasFactory;

    /**
     * Champs autorisés pour l'assignation de masse
     */
    protected $fillable = [
        'number', 
        'full_name', 
        'amount_fcfa', 
        'payment_reason', 
        'payment_reason_other', 
        'designation',        // Remplaçant de description
        'additional_details', 
        'payment_mode', 
        'date'
    ];

    /**
     * Conversion automatique des types
     */
    protected $casts = [
        'date' => 'date', 
        'amount_fcfa' => 'decimal:2'
    ];

    /**
     * Helper pour afficher le montant formaté (ex: 150 000)
     */
    public function formattedAmount() {
        return number_format($this->amount_fcfa, 0, ',', ' ');
    }

    /**
     * Helper pour afficher le motif réel
     * Si "Autres" est choisi, retourne la valeur saisie manuellement
     */
    public function getRealReason() { 
        return $this->payment_reason === 'Autres' ? $this->payment_reason_other : $this->payment_reason; 
    }

    /**
     * Note : Le champ 'designation' étant désormais un champ de saisie directe,
     * il n'a plus besoin de méthode helper "getRealDescription".
     * On accède directement à $expense->designation.
     */
}