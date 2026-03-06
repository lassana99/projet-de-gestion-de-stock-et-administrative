<?php

namespace App\Exports;

use App\Models\Settlement;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class SettlementExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $searchKey;

    public function __construct($searchKey = null)
    {
        $this->searchKey = $searchKey;
    }

    public function collection()
    {
        $query = Settlement::query();

        if ($this->searchKey) {
            $key = $this->searchKey;
            $query->where(function($q) use ($key) {
                $q->where('entity_name', 'like', "%$key%")
                  ->orWhere('amount', 'like', "%$key%")
                  ->orWhere('type', 'like', "%$key%")
                  ->orWhere('status', 'like', "%$key%")
                  ->orWhere('currency', 'like', "%$key%");
            });
        }

        return $query->orderBy('due_date', 'asc')->get();
    }

    public function headings(): array
    {
        return [
            'Type',
            'Nom du Tiers',
            'Montant',
            'Devise',
            'Montant FCFA',
            'Échéance',
            'Statut'
        ];
    }

    public function map($settlement): array
    {
        return [
            $settlement->type == 'debt' ? 'Créance' : 'Dette',
            $settlement->entity_name,
            round($settlement->amount),
            $settlement->currency,
            round($settlement->amount_fcfa),
            $settlement->due_date->format('d/m/Y'),
            $settlement->status == 'paid' ? 'Réglé' : 'En attente',
        ];
    }
}