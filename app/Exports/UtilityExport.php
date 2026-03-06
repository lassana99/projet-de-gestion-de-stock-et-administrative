<?php

namespace App\Exports;

use App\Models\Utility;
use Maatwebsite\Excel\Concerns\{FromCollection, WithHeadings, WithMapping, ShouldAutoSize};

class UtilityExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $searchKey;

    public function __construct($searchKey = null) {
        $this->searchKey = $searchKey;
    }

    public function collection() {
        $query = Utility::query();
        if ($this->searchKey) {
            $s = $this->searchKey;
            $query->where('number', 'like', "%$s%")->orWhere('month', 'like', "%$s%")
                  ->orWhere('description', 'like', "%$s%")->orWhere('reference', 'like', "%$s%");
        }
        return $query->orderBy('issue_date', 'desc')->get();
    }

    public function headings(): array {
        return ['Numéro', 'Mois', 'Description', 'Référence', 'Date émission', 'Montant (FCFA)', 'Mode Paiement'];
    }

    public function map($util): array {
        return [
            $util->number,
            $util->month,
            $util->displayDescription(),
            $util->reference,
            $util->issue_date->format('d/m/Y'),
            round($util->amount_fcfa),
            $util->payment_mode
        ];
    }
}