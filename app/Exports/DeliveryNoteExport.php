<?php

namespace App\Exports;

use App\Models\DeliveryNote;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class DeliveryNoteExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $searchKey;

    public function __construct($searchKey = null)
    {
        $this->searchKey = $searchKey;
    }

    public function collection()
    {
        $query = DeliveryNote::with('invoice');

        if ($this->searchKey) {
            $search = $this->searchKey;
            $query->where(function($q) use ($search) {
                $q->where('delivery_note_number', 'like', "%{$search}%")
                  ->orWhere('client_name', 'like', "%{$search}%")
                  ->orWhere('purchase_order_number', 'like', "%{$search}%");
            });
        }

        return $query->latest()->get();
    }

    public function headings(): array
    {
        return [
            'N° Bordereau (BL)', 
            'Client', 
            'N° Bon de Commande (BC)', 
            'Facture Associée', 
            'Date de Livraison'
        ];
    }

    public function map($bl): array
    {
        return [
            $bl->delivery_note_number,
            $bl->client_name,
            $bl->purchase_order_number ?? 'N/A',
            $bl->invoice ? $bl->invoice->invoice_number : 'Aucune',
            $bl->date_delivery->format('d/m/Y'),
        ];
    }
}