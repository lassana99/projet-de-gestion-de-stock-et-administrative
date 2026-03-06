<?php

namespace App\Exports;

use App\Models\Tax;
use Maatwebsite\Excel\Concerns\{FromCollection, WithHeadings, WithMapping, ShouldAutoSize};

class TaxExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $searchKey;

    public function __construct($searchKey = null) {
        $this->searchKey = $searchKey;
    }

    public function collection() {
        $query = Tax::query();
        if ($this->searchKey) {
            $s = $this->searchKey;
            $query->where('number', 'like', "%$s%")
                  ->orWhere('month', 'like', "%$s%")
                  ->orWhere('description', 'like', "%$s%")
                  ->orWhere('reference', 'like', "%$s%");
        }
        return $query->orderBy('issue_date', 'desc')->get();
    }

    public function headings(): array {
        return ['Numéro', 'Mois', 'Description', 'Référence', 'Date émission', 'Montant (FCFA)', 'Mode Paiement'];
    }

    public function map($tax): array {
        return [
            $tax->number,
            $tax->month,
            $tax->description === 'Autres' ? $tax->description_other : $tax->description,
            $tax->reference,
            $tax->issue_date->format('d/m/Y'),
            round($tax->amount_fcfa),
            $tax->payment_mode
        ];
    }
}