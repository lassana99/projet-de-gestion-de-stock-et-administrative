<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rent extends Model
{
    use HasFactory;

    /**
     * Nom de la table (optionnel si le nom est le pluriel du modèle)
     */
    protected $table = 'rents';

    /**
     * Champs autorisés pour l'assignation de masse
     */
    protected $fillable = [
        'number', 
        'month', 
        'structure', 
        'reference', 
        'issue_date', 
        'amount_fcfa', 
        'payment_mode', 
        'payment_mode_other', 
        'status'
    ];

    /**
     * Conversion automatique des types de données
     */
    protected $casts = [
        'issue_date' => 'date',
        'amount_fcfa' => 'decimal:2',
    ];

    /**
     * Helper : Afficher le montant formaté (ex: 500 000)
     */
    public function formattedAmountFcfa(): string
    {
        return number_format((float) $this->amount_fcfa, 0, ',', ' ');
    }

    /**
     * Helper : Afficher la date d'émission formatée (ex: 12/05/2024)
     */
    public function formattedIssueDate(): string
    {
        return $this->issue_date ? $this->issue_date->format('d/m/Y') : '-';
    }

    /**
     * Helper : Afficher le mode de paiement réel
     * Si le mode est "Autres", on affiche la précision saisie
     */
    public function displayPaymentMode(): string 
    {
        if ($this->payment_mode === 'Autres') {
            return $this->payment_mode_other ?? 'Autres';
        }
        return $this->payment_mode;
    }

    /**
     * Accessor (Optionnel) : Pour utiliser $rent->full_payment_mode
     */
    public function getFullPaymentModeAttribute(): string 
    {
        return $this->displayPaymentMode();
    }
}