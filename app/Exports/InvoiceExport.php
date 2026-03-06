<?php

namespace App\Exports;

use App\Models\Invoice;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class InvoiceExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $searchKey;

    public function __construct($searchKey = null)
    {
        $this->searchKey = $searchKey;
    }

    public function collection()
    {
        $query = Invoice::query();

        if ($this->searchKey) {
            $search = $this->searchKey;
            $query->where(function($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhere('client', 'like', "%{$search}%");
            });
        }

        return $query->orderBy('date_invoice', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'N° Facture', 
            'Client', 
            'Total HTVA (FCFA)', 
            'Total TTC (FCFA)', 
            'Date', 
            'État'
        ];
    }

    public function map($invoice): array
    {
        $statusMap = [
            'pending'            => 'En attente',
            'sent'               => 'Envoyée',
            'paid'               => 'Payée',
            'partial'            => 'Payée partiellement',
            'partially_paid'     => 'Payée partiellement',
            'partially paid'     => 'Payée partiellement',
            'cancelled'          => 'Annulée',
            'canceled'           => 'Annulée',
            'draft'              => 'Brouillon',
            'overdue'            => 'En retard',
        ];

        $rawStatus = strtolower(trim($invoice->status ?? ''));
        $statusLabel = $statusMap[$rawStatus] ?? ucfirst($rawStatus);

        return [
            $invoice->invoice_number,
            $invoice->client,
            round($invoice->total_htva),
            round($invoice->total_ttc),
            \Carbon\Carbon::parse($invoice->date_invoice)->format('d/m/Y'),
            $statusLabel,
        ];
    }
}