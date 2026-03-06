<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Devis;
use App\Models\Invoice;
use App\Models\Supplier;
use App\Models\Customer;
use App\Models\Prospect;
// Nouveaux modèles pour les dépenses
use App\Models\Rent;
use App\Models\Tax;
use App\Models\Utility;
use App\Models\OtherExpense;
// Modèles pour le Personnel (Ajoutés)
use App\Models\Salary;
use App\Models\InpsPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class AdminDashboardController extends Controller
{
    public function index(Request $request)
    {
        // 1. CONFIGURATION LOCALE
        Carbon::setLocale('fr');

        // 2. RÉCUPÉRATION DES PARAMÈTRES DE FILTRE
        $filterMode = $request->get('filter_mode', 'month');
        $selectedMonth = $request->get('month', Carbon::now()->month);
        $selectedYear = $request->get('year', Carbon::now()->year);
        
        // Paramètres pour la période personnalisée
        $dateStart = $request->get('date_start');
        $dateEnd = $request->get('date_end');

        $currentMonth = $selectedMonth;
        $currentYear = $selectedYear;

        // ------------------------------------------------------------------
        // 3. FONCTION DE FILTRAGE GÉNÉRIQUE ADAPTÉE
        // ------------------------------------------------------------------
        $applyFilter = function ($query, $column) use ($filterMode, $selectedMonth, $selectedYear, $dateStart, $dateEnd) {
            return $query->when($filterMode === 'month', function ($q) use ($column, $selectedMonth, $selectedYear) {
                return $q->whereMonth($column, $selectedMonth)
                         ->whereYear($column, $selectedYear);
            })->when($filterMode === 'all', function ($q) use ($column, $selectedYear) {
                return $q->whereYear($column, $selectedYear);
            })->when($filterMode === 'range', function ($q) use ($column, $dateStart, $dateEnd) {
                if ($dateStart && $dateEnd) {
                    // Filtrage entre deux dates (inclusif)
                    return $q->whereBetween($column, [$dateStart, $dateEnd]);
                }
                return $q;
            });
        };

        // ------------------------------------------------------------------
        // 4. RÉCUPÉRATION DES COMPTEURS (CARTES)
        // ------------------------------------------------------------------
        $total_payments_amt = $applyFilter(Payment::query(), 'payment_date')->sum('amount_htva');
        $totalPurchases = $applyFilter(Purchase::query(), 'date_purchase')->count();
        $totalProducts = $applyFilter(Product::query(), 'date_product')->count();
        $devisCount = $applyFilter(Devis::query(), 'date_devis')->count();
        $invoiceCount = $applyFilter(Invoice::query(), 'date_invoice')->count();
        $customerCount = $applyFilter(Customer::query(), 'date')->count();
        $prospectCount = $applyFilter(Prospect::query(), 'date')->count();
        $supplierCount = $applyFilter(Supplier::query(), 'date')->count();

        // ------------------------------------------------------------------
        // 5. CALCUL DES DÉPENSES (DÉBIT)
        // ------------------------------------------------------------------
        $totalRent = $applyFilter(Rent::query(), 'issue_date')->sum('amount_fcfa');
        $totalTaxes = $applyFilter(Tax::query(), 'issue_date')->sum('amount_fcfa');
        $totalUtilities = $applyFilter(Utility::query(), 'issue_date')->sum('amount_fcfa');
        $totalOthers = $applyFilter(OtherExpense::query(), 'date')->sum('amount_fcfa');
        $totalSalaries = $applyFilter(Salary::query(), 'payment_date')->sum('amount_fcfa');
        $totalInps = $applyFilter(InpsPayment::query(), 'payment_date')->sum('amount_fcfa');

        $calculDebits = $totalRent + $totalTaxes + $totalUtilities + $totalOthers + $totalSalaries + $totalInps;

        // ------------------------------------------------------------------
        // 6. CALCUL CRÉDIT ET BILAN (PAYÉES VS IMPAYÉES)
        // ------------------------------------------------------------------
        $profitSubQuery = DB::table('invoice_lines')
            ->select('invoice_id', DB::raw("SUM(invoice_lines.quantity * machines_profitabilities.profit) as total_line_profit"))
            ->join('products', 'invoice_lines.product_name', '=', 'products.name')
            ->join('machines_profitabilities', 'products.reference', '=', 'machines_profitabilities.purchase_reference')
            ->groupBy('invoice_id');

        // Statistiques des factures PAYÉES
        $paidQuery = Invoice::leftJoinSub($profitSubQuery, 'profit_table', 'invoices.id', '=', 'profit_table.invoice_id')
            ->where('invoices.status', 'paid');
        $paidStats = $applyFilter($paidQuery, 'invoices.date_invoice')
            ->select(
                DB::raw("SUM(invoices.total_htva) as total_ca"),
                DB::raw("SUM(profit_table.total_line_profit) as total_profit")
            )->first();

        $totalPaidCA = $paidStats->total_ca ?? 0;
        $totalPaidProfit = $paidStats->total_profit ?? 0;

        // Statistiques des factures IMPAYÉES
        $unpaidQuery = Invoice::leftJoinSub($profitSubQuery, 'profit_table', 'invoices.id', '=', 'profit_table.invoice_id')
            ->where('invoices.status', '!=', 'paid');
        $unpaidStats = $applyFilter($unpaidQuery, 'invoices.date_invoice')
            ->select(
                DB::raw("SUM(invoices.total_htva) as total_ca"),
                DB::raw("SUM(profit_table.total_line_profit) as total_profit")
            )->first();

        $totalUnpaidCA = $unpaidStats->total_ca ?? 0;
        $totalUnpaidProfit = $unpaidStats->total_profit ?? 0;

        // ------------------------------------------------------------------
        // 7. PERFORMANCE : CA, BÉNÉFICE (POUR L'HISTOGRAMME)
        // ------------------------------------------------------------------
        if ($filterMode === 'month') {
            $selectLabel = DB::raw("DATE_FORMAT(invoices.date_invoice, '%Y-%m') as month_label");
            $groupBy = "month_label";
        } elseif ($filterMode === 'range') {
            // Pour une période personnalisée, on groupe par mois pour voir l'évolution sur la période
            $selectLabel = DB::raw("DATE_FORMAT(invoices.date_invoice, '%d/%m/%Y') as month_label");
            $groupBy = "month_label";
        } else {
            $selectLabel = DB::raw("CONCAT('Année ', YEAR(invoices.date_invoice)) as month_label");
            $groupBy = DB::raw("YEAR(invoices.date_invoice)");
        }

        $invoiceOverviewQuery = Invoice::select(
                $selectLabel,
                DB::raw("SUM(invoices.total_htva) as total_ht_after_discount"),
                DB::raw("COUNT(invoices.id) as invoice_count"),
                DB::raw("SUM(profit_table.total_line_profit) as total_profit")
            )
            ->leftJoinSub($profitSubQuery, 'profit_table', 'invoices.id', '=', 'profit_table.invoice_id');

        // Application manuelle du filtre pour la requête d'aperçu
        if ($filterMode === 'month') {
            $invoiceOverviewQuery->whereMonth('invoices.date_invoice', $selectedMonth)
                                 ->whereYear('invoices.date_invoice', $selectedYear);
        } elseif ($filterMode === 'range') {
            if ($dateStart && $dateEnd) {
                $invoiceOverviewQuery->whereBetween('invoices.date_invoice', [$dateStart, $dateEnd]);
            }
        } else {
            $invoiceOverviewQuery->whereYear('invoices.date_invoice', $selectedYear);
        }

        $invoiceOverview = $invoiceOverviewQuery->groupBy($groupBy)->get();

        // ------------------------------------------------------------------
        // 8. ÉTAT DES DEVIS (PIE CHART)
        // ------------------------------------------------------------------
        $devisStats = $applyFilter(Devis::select('status', DB::raw('count(*) as total')), 'date_devis')
                        ->groupBy('status')
                        ->get();

        // ------------------------------------------------------------------
        // 9. AUTRES STATISTIQUES (STOCK, TOP PRODUITS, ETC.)
        // ------------------------------------------------------------------
        $adminCount = $applyFilter(User::whereIn('role', ['admin', 'superadmin']), 'created_at')->count();

        $orderPending = $applyFilter(Order::where('status', 0), 'created_at')
                             ->select('order_code')->groupBy('order_code')->get()->count();

        $orderSuccess = $applyFilter(Order::where('status', 1), 'created_at')
                             ->select('order_code')->groupBy('order_code')->get()->count();

        $topProductsQuery = Order::select('product_id', 'products.name as product_name', DB::raw('SUM(orders.count) as total_sold'))
                            ->leftJoin('products', 'orders.product_id', '=', 'products.id')
                            ->groupBy('product_id', 'products.name')
                            ->orderByDesc('total_sold')
                            ->limit(3);
        $topProducts = $applyFilter($topProductsQuery, 'orders.created_at')->get();

        $stock = DB::table('products')
                   ->select('products.name', DB::raw('products.count - COALESCE(SUM(orders.count), 0) as stock'))
                   ->leftJoin('orders', function ($join) {
                        $join->on('orders.product_id', '=', 'products.id')->where('orders.status', 1);
                   })
                   ->groupBy('products.id', 'products.name', 'products.count')
                   ->get();

        $outofstock = $stock->filter(fn ($item) => $item->stock < 5);

        // ------------------------------------------------------------------
        // 10. ENVOI DES DONNÉES À LA VUE
        // ------------------------------------------------------------------
        return view('admin.home', compact(
            'total_payments_amt',
            'customerCount',
            'prospectCount',
            'adminCount',
            'orderPending',
            'orderSuccess',
            'totalProducts',
            'totalPurchases',
            'devisCount',
            'invoiceCount',
            'supplierCount',
            'invoiceOverview', 
            'devisStats',      
            'topProducts',
            'outofstock',
            'filterMode',
            'currentMonth', 
            'currentYear',
            'dateStart',      // Ajouté pour la vue
            'dateEnd',        // Ajouté pour la vue
            'totalRent',
            'totalTaxes',
            'totalUtilities',
            'totalOthers',
            'totalSalaries',
            'totalInps',
            'calculDebits',
            'totalPaidProfit',
            'totalPaidCA',
            'totalUnpaidCA',
            'totalUnpaidProfit'
        ));
    }
}