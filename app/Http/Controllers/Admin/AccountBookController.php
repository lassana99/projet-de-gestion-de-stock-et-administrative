<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\Rent;
use App\Models\Tax;
use App\Models\Utility;
use App\Models\Salary;
use App\Models\InpsPayment;
use App\Models\OtherExpense;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AccountBookExport; // Assurez-vous de créer cette classe d'export

class AccountBookController extends Controller
{
    /**
     * Centralise la récupération et le filtrage des données
     * pour l'index et l'export Excel.
     */
    private function getEntries(Request $request)
    {
        // CONFIGURATION LOCALE
        Carbon::setLocale('fr');

        $filterMode = $request->get('filter_mode', 'month');
        $selectedMonth = $request->get('month', Carbon::now()->month);
        $selectedYear = $request->get('year', Carbon::now()->year);
        
        $dateStart = $request->get('date_start');
        $dateEnd = $request->get('date_end');

        // FONCTION DE FILTRAGE GÉNÉRIQUE
        $applyFilter = function ($query, $column) use ($filterMode, $selectedMonth, $selectedYear, $dateStart, $dateEnd) {
            return $query->when($filterMode === 'month', function ($q) use ($column, $selectedMonth, $selectedYear) {
                return $q->whereMonth($column, (int)$selectedMonth)
                         ->whereYear($column, (int)$selectedYear);
            })->when($filterMode === 'all', function ($q) use ($column, $selectedYear) {
                return $q->whereYear($column, (int)$selectedYear);
            })->when($filterMode === 'range', function ($q) use ($column, $dateStart, $dateEnd) {
                if ($dateStart && $dateEnd) {
                    return $q->whereBetween($column, [$dateStart, $dateEnd]);
                }
                return $q;
            });
        };

        // RÉCUPÉRATION DES CRÉDITS (Factures payées)
        $credits = $applyFilter(Invoice::where('status', 'paid'), 'date_invoice')
            ->get()
            ->map(function ($i) {
                return [
                    'date'   => $i->date_invoice,
                    'label'  => "Facture Payée : " . $i->invoice_number . " (" . $i->client . ")",
                    'credit' => $i->total_htva,
                    'debit'  => 0
                ];
            });

        // RÉCUPÉRATION DES DÉBITS (Toutes les dépenses)
        $rents = $applyFilter(Rent::query(), 'issue_date')->get()->map(fn($r) => [
            'date' => $r->issue_date, 'label' => "Loyer : " . $r->month . " (" . $r->structure . ")", 'credit' => 0, 'debit' => $r->amount_fcfa
        ]);

        $taxes = $applyFilter(Tax::query(), 'issue_date')->get()->map(fn($t) => [
            'date' => $t->issue_date, 'label' => "Impôt : " . $t->description . " (" . $t->month . ")", 'credit' => 0, 'debit' => $t->amount_fcfa
        ]);

        $utilities = $applyFilter(Utility::query(), 'issue_date')->get()->map(fn($u) => [
            'date' => $u->issue_date, 'label' => "Eau/Élec/Internet : " . $u->description . " (" . $u->month . ")", 'credit' => 0, 'debit' => $u->amount_fcfa
        ]);

        $salaries = $applyFilter(Salary::query(), 'payment_date')->get()->map(fn($s) => [
            'date' => $s->payment_date, 'label' => "Salaire : " . $s->full_name, 'credit' => 0, 'debit' => $s->amount_fcfa
        ]);

        $inps = $applyFilter(InpsPayment::query(), 'payment_date')->get()->map(fn($p) => [
            'date' => $p->payment_date, 'label' => "Charges Sociales (INPS) : " . $p->number, 'credit' => 0, 'debit' => $p->amount_fcfa
        ]);

        $others = $applyFilter(OtherExpense::query(), 'date')->get()->map(fn($o) => [
            'date' => $o->date, 'label' => "Autre dépense : " . ($o->designation ?? $o->payment_reason) . " (" . $o->full_name . ")", 'credit' => 0, 'debit' => $o->amount_fcfa
        ]);

        // FUSION ET TRI FINAL
        return collect($credits)
            ->concat($rents)
            ->concat($taxes)
            ->concat($utilities)
            ->concat($salaries)
            ->concat($inps)
            ->concat($others)
            ->sortBy('date');
    }

    /**
     * Affiche la vue Web du Livre de comptes
     */
    public function index(Request $request)
    {
        $allEntries = $this->getEntries($request);

        // Paramètres pour maintenir l'état des filtres dans la vue
        $filterMode = $request->get('filter_mode', 'month');
        $month = $request->get('month', Carbon::now()->month);
        $year = $request->get('year', Carbon::now()->year);
        $dateStart = $request->get('date_start');
        $dateEnd = $request->get('date_end');

        return view('admin.finance.account_book', compact(
            'allEntries', 
            'month', 
            'year', 
            'filterMode', 
            'dateStart', 
            'dateEnd'
        ));
    }

    /**
     * Gère l'exportation Excel
     */
    public function exportExcel(Request $request)
    {
        $allEntries = $this->getEntries($request);

        // On génère le fichier Excel en utilisant une classe d'export dédiée
        return Excel::download(
            new AccountBookExport($allEntries), 
            'livre_de_comptes_' . date('d_m_Y_Hi') . '.xlsx'
        );
    }
}