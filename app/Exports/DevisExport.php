<?php

namespace App\Exports;

use App\Models\Devis;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class DevisExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $searchKey;

    public function __construct($searchKey = null)
    {
        $this->searchKey = $searchKey;
    }

    public function collection()
    {
        $query = Devis::query();

        if ($this->searchKey) {
            $search = $this->searchKey;
            $query->where(function($q) use ($search) {
                $q->where('devis_number', 'like', "%{$search}%")
                  ->orWhere('client', 'like', "%{$search}%");
            });
        }

        return $query->orderBy('date_devis', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'N° Devis', 
            'Client', 
            'Total HTVA (FCFA)', 
            'Total TTC (FCFA)', 
            'Date', 
            'État'
        ];
    }

    public function map($devis): array
    {
        $statusMap = [
            'pending'   => 'En attente',
            'sent'      => 'Envoyé',
            'accepted'  => 'Accepté',
            'rejected'  => 'Annulé',
            'draft'     => 'Brouillon',
            'abandoned' => 'Abandonné',
            'invoiced'  => 'Facturé',
        ];

        return [
            $devis->devis_number,
            $devis->client,
            round($devis->total_htva),
            round($devis->total_ttc),
            \Carbon\Carbon::parse($devis->date_devis)->format('d/m/Y'),
            $statusMap[strtolower($devis->status)] ?? $devis->status,
        ];
    }
}