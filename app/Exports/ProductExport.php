<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ProductExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $searchKey;

    public function __construct($searchKey = null)
    {
        $this->searchKey = $searchKey;
    }

    public function collection()
    {
        $query = Product::query();

        if ($this->searchKey) {
            $search = $this->searchKey;
            $query->where(function($q) use ($search) {
                $q->where('reference', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%")
                  ->orWhere('status', 'like', "%{$search}%");
            });
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'Référence', 
            'Nom du produit', 
            'Marque', 
            'État', 
            'Prix de vente (FCFA)', 
            'Quantité', 
            'Date'
        ];
    }

    public function map($product): array
    {
        return [
            $product->reference,
            $product->name,
            $product->brand,
            $product->status,
            round($product->price),
            $product->count,
            $product->date_product ? $product->date_product->format('d/m/Y') : '-',
        ];
    }
}