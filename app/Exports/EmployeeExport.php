<?php

namespace App\Exports;

use App\Models\Employee;
use Maatwebsite\Excel\Concerns\{FromCollection, WithHeadings, WithMapping, ShouldAutoSize};

class EmployeeExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $searchKey;

    /**
     * On reçoit la clé de recherche depuis le contrôleur
     */
    public function __construct($searchKey = null) {
        $this->searchKey = $searchKey;
    }

    /**
     * Récupération de la collection avec filtrage
     */
    public function collection() {
        $query = Employee::query();
        
        if ($this->searchKey) {
            $s = $this->searchKey;
            $query->where(function($q) use ($s) {
                $q->where('full_name', 'like', "%$s%")
                  ->orWhere('matricule', 'like', "%$s%")
                  ->orWhere('position', 'like', "%$s%")
                  ->orWhere('specialty', 'like', "%$s%")
                  ->orWhere('diploma', 'like', "%$s%")
                  ->orWhere('email', 'like', "%$s%") // Inclus dans la recherche
                  ->orWhere('phone', 'like', "%$s%"); // Inclus dans la recherche
            });
        }
        
        return $query->orderBy('matricule', 'asc')->get();
    }

    /**
     * En-têtes du fichier Excel
     */
    public function headings(): array {
        return [
            'Matricule', 
            'Nom & Prénom', 
            'Téléphone',             // Nouveau
            'Email',                 // Nouveau
            'Fonction / Service', 
            'Diplôme', 
            'Spécialité', 
            'Situation Matrimoniale', // Nouveau
            'Nombre d\'enfants',      // Nouveau
            'Années d\'expérience', 
            'Type de Pièce', 
            'N° de Pièce',
            'Contact d\'Urgence'      // Nouveau
        ];
    }

    /**
     * Mapping des données pour chaque ligne
     */
    public function map($emp): array {
        return [
            $emp->matricule,
            $emp->full_name,
            $emp->phone,              // Nouveau
            $emp->email,              // Nouveau
            $emp->position,
            $emp->diploma,
            $emp->specialty,
            $emp->marital_status,     // Nouveau
            $emp->children_count,     // Nouveau
            $emp->experience_years,
            $emp->getRealIdType(),
            $emp->id_number,
            $emp->emergency_contact   // Nouveau
        ];
    }
}