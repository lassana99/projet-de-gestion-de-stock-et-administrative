<?php

namespace App\Exports;

use App\Models\Salary;
use Maatwebsite\Excel\Concerns\{FromCollection, WithHeadings, WithMapping, ShouldAutoSize};

class SalaryExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $searchKey;

    public function __construct($searchKey = null) {
        $this->searchKey = $searchKey;
    }

    public function collection() {
        $query = Salary::query();
        if ($this->searchKey) {
            $s = $this->searchKey;
            $query->where('number', 'like', "%$s%")
                  ->orWhere('full_name', 'like', "%$s%")
                  ->orWhere('position', 'like', "%$s%");
        }
        return $query->orderBy('payment_date', 'desc')->get();
    }

    public function headings(): array {
        return [
            'N° Enregistrement', 'Date de Paiement', 'Nom & Prénom', 
            'Fonction', 'Type de Pièce', 'N° de Pièce', 
            'Montant (FCFA)', 'Modalité'
        ];
    }

    public function map($sal): array {
        return [
            $sal->number,
            $sal->payment_date->format('d/m/Y'),
            $sal->full_name,
            $sal->position,
            $sal->getRealIdType(),
            $sal->id_number,
            round($sal->amount_fcfa),
            $sal->payment_mode
        ];
    }
}