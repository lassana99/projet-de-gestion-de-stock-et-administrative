<?php

namespace App\Exports;

use App\Models\OtherExpense;
use Maatwebsite\Excel\Concerns\{FromCollection, WithHeadings, WithMapping, ShouldAutoSize};

class OtherExpenseExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $searchKey;

    public function __construct($searchKey = null) {
        $this->searchKey = $searchKey;
    }

    public function collection() {
        $query = OtherExpense::query();
        if ($this->searchKey) {
            $s = $this->searchKey;
            $query->where('number', 'like', "%$s%")
                  ->orWhere('full_name', 'like', "%$s%")
                  ->orWhere('payment_reason', 'like', "%$s%");
        }
        return $query->orderBy('date', 'desc')->get();
    }

    public function headings(): array {
        return [
            'N° Enregistrement', 'Date', 'Nom & Prénom', 'Fonction', 
            'Pièce d\'identité', 'Montant (FCFA)', 'Motif', 
            'Description', 'Modalité'
        ];
    }

    public function map($exp): array {
        return [
            $exp->number,
            $exp->date->format('d/m/Y'),
            $exp->full_name,
            $exp->position,
            $exp->getRealIdType() . ' (' . $exp->id_number . ')',
            round($exp->amount_fcfa),
            $exp->getRealReason(),
            $exp->getRealDescription(),
            $exp->payment_mode
        ];
    }
}