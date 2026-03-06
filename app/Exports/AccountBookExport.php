<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class AccountBookExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $entries;
    protected $runningBalance = 0;

    public function __construct($entries)
    {
        $this->entries = $entries;
    }

    public function collection()
    {
        return $this->entries;
    }

    public function headings(): array
    {
        return [
            'Date',
            'Libellé / Transaction',
            'Crédit (+)',
            'Débit (-)',
            'Solde Progressif'
        ];
    }

    public function map($entry): array
    {
        $this->runningBalance += ($entry['credit'] - $entry['debit']);

        return [
            \Carbon\Carbon::parse($entry['date'])->format('d/m/Y'),
            $entry['label'],
            $entry['credit'] > 0 ? $entry['credit'] : 0,
            $entry['debit'] > 0 ? $entry['debit'] : 0,
            $this->runningBalance
        ];
    }
}