<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    /**
     * Champs autorisés pour l'assignation de masse
     */
    protected $fillable = [
        'matricule', 
        'full_name', 
        'phone',             // Nouveau : Numéro de téléphone
        'email',             // Nouveau : Adresse mail
        'position', 
        'diploma', 
        'specialty', 
        'marital_status',    // Nouveau : Situation matrimoniale
        'children_count',    // Nouveau : Nombre d'enfants
        'experience_years', 
        'id_number', 
        'id_type', 
        'id_type_other', 
        'emergency_contact', // Nouveau : Contact d'urgence
        'photo'
    ];

    /**
     * Relation avec les congés : Un employé peut avoir plusieurs congés
     */
    public function leaves() {
        return $this->hasMany(Leave::class);
    }

    /**
     * Helper pour obtenir le type de pièce réel
     * (Si "Autres" est choisi, retourne la valeur personnalisée)
     */
    public function getRealIdType() {
        return $this->id_type === 'Autres' ? $this->id_type_other : $this->id_type;
    }
}