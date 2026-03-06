<?php

namespace App\Exports;

use App\Models\Prospect;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ProspectExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $searchKey;

    public function __construct($searchKey = null)
    {
        $this->searchKey = $searchKey;
    }

    public function collection()
    {
        $query = Prospect::with('contacts');

        if ($this->searchKey) {
            $search = $this->searchKey;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%")
                  ->orWhere('need', 'like', "%{$search}%");
            });
        }

        return $query->latest('date')->get();
    }

    public function headings(): array
    {
        return [
            'Nom du prospect',
            'Adresse',
            'Personne à contacter',
            'Téléphone',
            'Opportunité (Besoin)',
            'Statut d\'achat',
            'Date d\'enregistrement'
        ];
    }

    public function map($prospect): array
    {
        $firstContact = $prospect->contacts->first();

        return [
            $prospect->name,
            $prospect->address,
            $firstContact ? $firstContact->name : 'Aucun contact',
            $firstContact ? ($firstContact->phone ?? '-') : '-',
            $prospect->need ?? '-',
            $prospect->statut_achat,
            $prospect->date ? $prospect->date->format('d/m/Y') : '-',
        ];
    }
}