<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Devis extends Model
{
    use HasFactory;

    /**
     * Nom de la table
     */
    protected $table = 'devis';

    /**
     * Champs autorisés pour l’assignation de masse
     */
    protected $fillable = [
        'devis_number',
        'date_devis',
        'client',
        'client_address',
        'code_client',
        'delivery_terms',
        'payment_terms',
        'delivery_location',
        'validity',
        'tax_rate',
        'discount', // Ce champ stocke désormais le pourcentage (ex: 10 pour 10%)
        'total_ht',
        'total_htva',
        'total_tva',
        'total_ttc',
        'status',
    ];

    /**
     * Casts des champs
     */
    protected $casts = [
        'date_devis' => 'date',
        'tax_rate'   => 'float',
        'discount'   => 'float',
        'total_ht'   => 'float',
        'total_htva' => 'float',
        'total_tva'  => 'float',
        'total_ttc'  => 'float',
    ];

    /**
     * Valeur par défaut du statut
     */
    protected $attributes = [
        'status' => 'sent', 
    ];

    //---------------------------------------------------------
    // RELATIONS
    //---------------------------------------------------------

    public function lines(): HasMany
    {
        return $this->hasMany(DevisLine::class, 'devis_id');
    }

    public function invoice(): HasOne
    {
        return $this->hasOne(Invoice::class, 'devis_id');
    }

    //---------------------------------------------------------
    // STATUTS
    //---------------------------------------------------------

    private const STATUS_MAP = [
        'Envoyé'     => 'sent',
        'Accepté'    => 'accepted',
        'Refusé'     => 'rejected',
        'Abandonné'  => 'abandoned',
        'Facturé'    => 'invoiced',
    ];

    public static function getValidStatuses(): array
    {
        return array_values(array_filter(self::STATUS_MAP, fn($v) => $v !== 'invoiced'));
    }

    public function markAsInvoiced(): bool
    {
        if ($this->status !== 'invoiced') {
            return $this->update(['status' => 'invoiced']);
        }
        return true;
    }

    public function markAsAccepted(): bool
    {
        return $this->update(['status' => 'accepted']);
    }

    public function markAsAbandoned(): bool
    {
        return $this->update(['status' => 'abandoned']);
    }

    //---------------------------------------------------------
    // CALCUL DES TOTAUX
    //---------------------------------------------------------

    /**
     * Calcule et met à jour les totaux du devis
     */
    public function calculateTotals(): void
    {
        // 1️⃣ Total HT Brut (Somme des lignes)
        $totalHT = $this->lines()->sum('total_ht');

        // 2️⃣ Calcul de la remise en POURCENTAGE
        // On récupère la valeur numérique de discount (ex: 5.00 pour 5%)
        $discountPercentage = $this->discount ?? 0;
        $discountAmount = ($totalHT * $discountPercentage) / 100;

        // 3️⃣ Total HT après remise (C'est la base de calcul pour la TVA)
        // On s'assure que le montant ne descend pas en dessous de 0
        $totalHTVA = max(0, $totalHT - $discountAmount);

        // 4️⃣ Calcul de la TVA
        // tax_rate est stocké sous forme décimale (ex: 0.18 pour 18%)
        $taxRate = $this->tax_rate ?? 0.18;
        $totalTVA = $totalHTVA * $taxRate;

        // 5️⃣ Total TTC final
        $totalTTC = $totalHTVA + $totalTVA;

        // 6️⃣ Mise à jour en base de données
        $this->update([
            'total_ht'   => $totalHT,     // Montant avant remise
            'discount'   => $discountPercentage, // On conserve le pourcentage
            'total_htva' => $totalHTVA,   // Montant après remise (Base HT)
            'total_tva'  => $totalTVA,    // Valeur de la taxe
            'total_ttc'  => $totalTTC,    // Montant final à payer
        ]);
    }
}