<?php

namespace App\Exports;

use App\Models\Rent;
use Maatwebsite\Excel\Concerns\{FromCollection, WithHeadings, WithMapping, ShouldAutoSize};

class RentExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $searchKey;

    public function __construct($searchKey = null) {
        $this->searchKey = $searchKey;
    }

    public function collection() {
        $query = Rent::query();
        if ($this->searchKey) {
            $s = $this->searchKey;
            $query->where('number', 'like', "%$s%")
                  ->orWhere('month', 'like', "%$s%")
                  ->orWhere('structure', 'like', "%$s%")
                  ->orWhere('status', 'like', "%$s%");
        }
        return $query->orderBy('issue_date', 'desc')->get();
    }

    public function headings(): array {
        return ['Numéro', 'Mois', 'Structure', 'Référence', 'Date émission', 'Montant (FCFA)', 'Mode Paiement', 'Statut'];
    }

    public function map($rent): array {
        return [
            $rent->number,
            $rent->month,
            $rent->structure,
            $rent->reference,
            $rent->issue_date->format('d/m/Y'),
            round($rent->amount_fcfa),
            $rent->payment_mode === 'Autres' ? $rent->payment_mode_other : $rent->payment_mode,
            $rent->status
        ];
    }
}