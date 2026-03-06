<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon; // Ajout de l'import Carbon pour le PHPDoc

/**
 * @property int $id
 * @property int $invoice_id
 * @property string $delivery_note_number
 * @property string $client_name
 * @property string $client_address
 * @property string $code_client
 * @property string|null $purchase_order_number
 * @property string $delivery_location
 * @property string|null $status
 * @property Carbon $date_delivery // 👈 INDICATION POUR L'IDE (Carbon au lieu de string)
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * * @property-read string $date_delivery_formatted
 * @property-read \App\Models\Invoice $invoice
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\DeliveryNoteLine[] $lines
 */
class DeliveryNote extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * Les attributs qui doivent être castés en types natifs.
     * On utilise 'date' pour forcer la conversion de la date de livraison
     * en objet Carbon. C'est la bonne pratique Laravel.
     *
     * @var array
     */
    protected $casts = [
        'date_delivery' => 'date', 
    ];

    // Un BL appartient à une Facture (Invoice)
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    // Un BL a plusieurs lignes d'articles livrés
    public function lines()
    {
        return $this->hasMany(DeliveryNoteLine::class);
    }

    /**
     * Accesseur pour formater la date au format J/M/A (Optionnel, mais utile)
     */
    public function getDateDeliveryFormattedAttribute()
    {
        // $this->date_delivery est un objet Carbon grâce au $casts
        return $this->date_delivery ? $this->date_delivery->format('d/m/Y') : null;
    }
}