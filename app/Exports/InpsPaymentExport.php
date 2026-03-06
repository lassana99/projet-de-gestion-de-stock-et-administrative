<?php

namespace App\Exports;
use App\Models\InpsPayment;
use Maatwebsite\Excel\Concerns\{FromCollection, WithHeadings, WithMapping, ShouldAutoSize};

class InpsPaymentExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize {
    public function collection() {
        return InpsPayment::with('employees')->get();
    }
    public function headings(): array {
        return ['Numéro', 'Début', 'Fin', 'Montant', 'Date Paiement', 'Employés'];
    }
    public function map($p): array {
        return [
            $p->number, $p->start_date->format('d/m/Y'), $p->end_date->format('d/m/Y'),
            $p->amount_fcfa, $p->payment_date->format('d/m/Y'),
            $p->employees->pluck('full_name')->implode(', ')
        ];
    }
}