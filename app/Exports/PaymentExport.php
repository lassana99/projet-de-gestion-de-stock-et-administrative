<?php

namespace App\Exports;

use App\Models\Payment;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class PaymentExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $searchKey;

    public function __construct($searchKey = null)
    {
        $this->searchKey = $searchKey;
    }

    public function collection()
    {
        $query = Payment::query();

        if ($this->searchKey) {
            $search = $this->searchKey;
            $query->where(function($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhere('client_name', 'like', "%{$search}%");
            });
        }

        return $query->latest('payment_date')->get();
    }

    public function headings(): array
    {
        return [
            'N° Facture',
            'Client',
            'Montant HTVA (FCFA)',
            'Mode de paiement',
            'Date de paiement'
        ];
    }

    public function map($payment): array
    {
        return [
            $payment->invoice_number,
            $payment->client_name,
            round($payment->amount_htva),
            $payment->payment_method,
            $payment->payment_date->format('d/m/Y'),
        ];
    }
}