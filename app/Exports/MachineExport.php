<?php

namespace App\Exports;

use App\Models\Machine; // Vérifiez le nom de votre modèle
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class MachineExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $searchKey;

    public function __construct($searchKey = null)
    {
        $this->searchKey = $searchKey;
    }

    public function collection()
    {
        $query = Machine::query();

        if ($this->searchKey) {
            $query->where('purchase_reference', 'like', '%' . $this->searchKey . '%');
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'Référence',
            'Poids (KG)',
            'Prix achat unité',
            'Marge (%)',
            'Bénéfice (FCFA)',
            'Prix de vente HT (FCFA)',
            'Prix de vente TTC (FCFA)',
            'Date'
        ];
    }

    public function map($machine): array
    {
        return [
            $machine->purchase_reference,
            round($machine->weight ?? 0),
            round($machine->unit_purchase_price ?? 0),
            round($machine->margin ?? 0) . '%',
            round($machine->profit ?? 0),
            round($machine->selling_price ?? 0),
            round($machine->selling_price_ttc ?? 0),
            $machine->date_profitability ? $machine->date_profitability->format('d/m/Y') : '-',
        ];
    }
}