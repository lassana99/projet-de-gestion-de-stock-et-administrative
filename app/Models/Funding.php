<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Funding extends Model
{
    protected $fillable = [
        'motif',
        'nom_de_banque',
        'montant_emprunte',
        'nombre_de_jours',
        'taux',
        'montant_a_payer',
        'montant_a_payer_par_item',  // ajout de la nouvelle colonne ici
        'nombre_d_items',
        'date',
    ];
}
