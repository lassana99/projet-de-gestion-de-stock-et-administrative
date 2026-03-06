<?php

namespace App\Exports;

use App\Models\Leave;
use Maatwebsite\Excel\Concerns\{FromCollection, WithHeadings, WithMapping, ShouldAutoSize};

class LeaveExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $searchKey;
    public function __construct($searchKey = null) { $this->searchKey = $searchKey; }

    public function collection() {
        $query = Leave::with('employee');
        if ($this->searchKey) {
            $s = $this->searchKey;
            $query->whereHas('employee', function($q) use ($s) {
                $q->where('full_name', 'like', "%$s%");
            });
        }
        return $query->get();
    }

    public function headings(): array {
        return ['Employé', 'Type', 'Début', 'Fin', 'Jours', 'Statut'];
    }

    public function map($leave): array {
        return [
            $leave->employee->full_name,
            $leave->leave_type,
            $leave->start_date->format('d/m/Y'),
            $leave->end_date->format('d/m/Y'),
            $leave->days_count,
            $leave->status
        ];
    }
}