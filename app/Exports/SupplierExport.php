<?php

namespace App\Exports;

use App\Models\Supplier;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class SupplierExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $searchKey;

    public function __construct($searchKey = null)
    {
        $this->searchKey = $searchKey;
    }

    public function collection()
    {
        $query = Supplier::with('contacts');

        if ($this->searchKey) {
            $query->where('company_name', 'like', '%' . $this->searchKey . '%');
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'Nom de la société',
            'Pays d\'origine',
            'Personne à contacter',
            'Poste',
            'Contact (Tel)',
            'Date d\'enregistrement'
        ];
    }

    public function map($supplier): array
    {
        $firstContact = $supplier->contacts->first();

        return [
            $supplier->company_name,
            $supplier->country_origin ?? '-',
            $firstContact ? $firstContact->name : 'Aucun contact',
            $firstContact ? ($firstContact->position ?? '-') : '-',
            $firstContact ? ($firstContact->phone ?? '-') : '-',
            $supplier->date ? $supplier->date->format('d/m/Y') : '-',
        ];
    }
}