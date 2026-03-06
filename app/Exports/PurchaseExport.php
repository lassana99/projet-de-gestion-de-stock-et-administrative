<?php

namespace App\Exports;

use App\Models\Purchase;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class PurchaseExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $searchKey;

    public function __construct($searchKey = null)
    {
        $this->searchKey = $searchKey;
    }

    public function collection()
    {
        $query = Purchase::query();

        if ($this->searchKey) {
            $search = $this->searchKey;
            $query->where(function($q) use ($search) {
                $q->where('reference', 'like', "%{$search}%")
                  ->orWhere('purchasename', 'like', "%{$search}%")
                  ->orWhere('country', 'like', "%{$search}%")
                  ->orWhere('incoterm', 'like', "%{$search}%");
            });
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'Référence', 'Produit', 'Pays', 'Incoterm', 
            'Prix d\'achat unité', 'Devise', 'Prix d\'achat (FCFA)', 
            'Quantité', 'Date d\'achat'
        ];
    }

    public function map($purchase): array
    {
        return [
            $purchase->reference,
            $purchase->purchasename,
            $purchase->country ?? '-',
            $purchase->incoterm ?? '-',
            round($purchase->purchaseprice),
            $purchase->currency ?? 'FCFA',
            round($purchase->purchase_price_fcfa),
            $purchase->quantity,
            $purchase->date_purchase ? $purchase->date_purchase->format('d/m/Y') : '-',
        ];
    }
}