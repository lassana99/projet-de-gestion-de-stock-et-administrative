<?php

namespace App\Exports;

use App\Models\Funding; // Vérifiez le nom de votre modèle
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class FundingExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $searchKey;

    public function __construct($searchKey = null)
    {
        $this->searchKey = $searchKey;
    }

    public function collection()
    {
        $query = Funding::query();

        if ($this->searchKey) {
            $search = $this->searchKey;
            $query->where(function($q) use ($search) {
                $q->where('motif', 'like', "%{$search}%")
                  ->orWhere('nom_de_banque', 'like', "%{$search}%");
            });
        }

        return $query->orderBy('date', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'Request (Motif)', 
            'Banque', 
            'Montant emprunté', 
            'Nombre de jours', 
            'Taux (%)', 
            'Montant à payer', 
            'Date'
        ];
    }

    public function map($funding): array
    {
        return [
            $funding->motif,
            $funding->nom_de_banque,
            round($funding->montant_emprunte),
            $funding->nombre_de_jours,
            intval($funding->taux) . '%',
            round($funding->montant_a_payer),
            \Carbon\Carbon::parse($funding->date)->format('d/m/Y'),
        ];
    }
}