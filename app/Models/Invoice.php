<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Log;

class Invoice extends Model
{
    use HasFactory;

    protected $table = 'invoices';
    protected $guarded = ['id'];

    protected $fillable = [
        'devis_id',
        'invoice_number',
        'date_invoice',
        'code_client',
        'client',
        'client_address',
        'total_ht',
        'discount',      // Pourcentage (ex: 5)
        'total_htva',    // Montant Net HT (Base TVA)
        'total_tva',
        'total_ttc',
        'tax_rate',
        'delivery_terms',
        'payment_terms',
        'delivery_location',
        'signatory_name',
        'status',
    ];

    protected $casts = [
        'date_invoice' => 'date',
        'total_ht'      => 'float',
        'discount'      => 'float',
        'total_htva'    => 'float',
        'total_tva'     => 'float',
        'total_ttc'     => 'float',
        'tax_rate'      => 'float',
    ];

    protected static function boot()
    {
        parent::boot();

        static::created(function (Invoice $invoice) {
            if ($invoice->devis_id) {
                $devis = $invoice->devis;
                if ($devis) {
                    $devis->markAsInvoiced();
                    Log::info("Invoice ID {$invoice->id} created. Devis status updated.");
                }
            }
        });
    }

    public function devis(): BelongsTo
    {
        return $this->belongsTo(Devis::class);
    }

    public function lines(): HasMany
    {
        return $this->hasMany(InvoiceLine::class);
    }

    // ------------------------------------------------------------------
    // SÉCURITÉ D'AFFICHAGE (ACCESSOR)
    // ------------------------------------------------------------------
    /**
     * Si total_htva est 0 en base, cette fonction le calcule automatiquement 
     * pour l'affichage (ex: {{ $invoice->total_htva }})
     */
    public function getTotalHtvaAttribute($value)
    {
        if ($value <= 0 && $this->total_ht > 0) {
            $discountAmount = ($this->total_ht * ($this->discount ?? 0)) / 100;
            return $this->total_ht - $discountAmount;
        }
        return $value;
    }

    // ------------------------------------------------------------------
    // CALCUL ET SAUVEGARDE DES TOTAUX
    // ------------------------------------------------------------------

    public function calculateTotals(): void
    {
        // 1️⃣ Récupérer le Total HT brut (somme des lignes)
        $totalHT = (float) $this->lines()->sum('total_ht');

        // 2️⃣ Calcul de la remise
        $discountPercentage = (float) ($this->discount ?? 0);
        $discountAmount = ($totalHT * $discountPercentage) / 100;

        // 3️⃣ Calcul du Net HT (total_htva)
        $totalHTVA = max(0, $totalHT - $discountAmount);

        // 4️⃣ Calcul de la TVA
        $taxRate = (float) ($this->tax_rate ?? 0.18);
        $totalTVA = $totalHTVA * $taxRate;

        // 5️⃣ Calcul du TTC
        $totalTTC = $totalHTVA + $totalTVA;

        // 6️⃣ Forcer la mise à jour sans passer par les événements pour éviter les boucles
        $this->updateQuietly([
            'total_ht'   => $totalHT,
            'total_htva' => $totalHTVA,
            'total_tva'  => $totalTVA,
            'total_ttc'  => $totalTTC,
        ]);
    }
}