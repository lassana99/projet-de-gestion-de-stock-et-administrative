<?php

namespace App\Exports;

use App\Models\Customer;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class CustomerExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $searchKey;

    public function __construct($searchKey = null)
    {
        $this->searchKey = $searchKey;
    }

    public function collection()
    {
        $query = Customer::with('contacts');

        if ($this->searchKey) {
            $query->where('name', 'like', '%' . $this->searchKey . '%')
                  ->orWhere('code_client', 'like', '%' . $this->searchKey . '%');
        }

        return $query->latest('date')->get();
    }

    public function headings(): array
    {
        return [
            'Code client',
            'Nom du client',
            'Personne à contacter',
            'Téléphone',
            'Date d\'enregistrement'
        ];
    }

    public function map($customer): array
    {
        $firstContact = $customer->contacts->first();

        return [
            $customer->code_client,
            $customer->name,
            $firstContact ? $firstContact->name : 'Aucun contact',
            $firstContact ? ($firstContact->phone ?? '-') : '-',
            $customer->date ? $customer->date->format('d/m/Y') : '-',
        ];
    }
}