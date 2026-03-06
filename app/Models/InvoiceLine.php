<?php

// app/Models/InvoiceLine.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceLine extends Model
{
    /**
     * En utilisant $guarded = ['id'], tous les autres champs (y compris les nouveaux
     * champs 'reference' et 'image' ajoutés via la migration) sont autorisés 
     * pour l'assignation de masse.
     */
    protected $guarded = ['id'];
    
    protected $table = 'invoice_lines';

    /**
     * Une ligne de facture appartient à une facture.
     */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }
}