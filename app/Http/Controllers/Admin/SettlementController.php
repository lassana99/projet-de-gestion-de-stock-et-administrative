<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Settlement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use RealRashid\SweetAlert\Facades\Alert;
use Carbon\Carbon;
// Importations pour l'exportation Excel
use App\Exports\SettlementExport; 
use Maatwebsite\Excel\Facades\Excel;

class SettlementController extends Controller
{
    /**
     * Taux de conversion fixes
     */
    protected $exchangeRates = [
        'FCFA'           => 1,
        'Euro'           => 656, 
        'Dollar'         => 610,    
        'Livre Sterling' => 780     
    ];

    /**
     * Centralisation de la logique de recherche globale (Web + Excel)
     */
    private function applySearchFilters($query, $searchKey)
    {
        if ($searchKey) {
            $query->where(function($q) use ($searchKey) {
                $q->where('entity_name', 'like', "%{$searchKey}%")
                  ->orWhere('type', 'like', "%{$searchKey}%")
                  ->orWhere('status', 'like', "%{$searchKey}%")
                  ->orWhere('currency', 'like', "%{$searchKey}%")
                  ->orWhere('amount', 'like', "%{$searchKey}%")
                  ->orWhere('amount_fcfa', 'like', "%{$searchKey}%")
                  ->orWhere('due_date', 'like', "%{$searchKey}%")
                  ->orWhere('issue_date', 'like', "%{$searchKey}%")
                  ->orWhere('phone', 'like', "%{$searchKey}%")
                  ->orWhere('email', 'like', "%{$searchKey}%");
            });
        }
        return $query;
    }

    /**
     * Nettoyage des montants (retrait des espaces)
     */
    protected function cleanAmount($value)
    {
        if (!$value) return 0;
        return str_replace(' ', '', $value);
    }

    /**
     * Formule de calcul de conversion
     */
    protected function calculateFcfa($amount, $currency)
    {
        $rate = $this->exchangeRates[$currency] ?? 1;
        return $amount * $rate;
    }

    /**
     * Liste des règlements avec RECHERCHE ET TRI
     */
    public function index(Request $request)
    {
        $searchKey = $request->input('searchKey');
        $query = Settlement::query();

        // 1. Application de la recherche centralisée
        $this->applySearchFilters($query, $searchKey);

        // 2. Gestion du Tri Dynamique
        $sort = $request->get('sort', 'due_date');
        $direction = $request->get('direction', 'asc');

        $allowedSorts = ['type', 'entity_name', 'amount', 'currency', 'amount_fcfa', 'due_date', 'issue_date', 'status'];
        if (!in_array($sort, $allowedSorts)) { $sort = 'due_date'; }
        if (!in_array(strtolower($direction), ['asc', 'desc'])) { $direction = 'asc'; }

        $settlements = $query->orderBy($sort, $direction)->paginate(10);

        return view('admin.settlement.list', compact('settlements'));
    }

    /**
     * EXPORT EXCEL UNIQUEMENT (via Maatwebsite/Excel)
     */
    public function exportExcel(Request $request) 
    {
        $searchKey = $request->get('searchKey');
        return Excel::download(new SettlementExport($searchKey), 'liste_reglements_' . date('d_m_Y_Hi') . '.xlsx');
    }

    /**
     * Formulaire de création
     */
    public function create()
    {
        return view('admin.settlement.create');
    }

    /**
     * Enregistrement d'un nouveau règlement
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'type'        => 'required|in:debt,credit',
            'entity_name' => 'required|string|max:255',
            'amount'      => 'required',
            'currency'    => 'required|in:FCFA,Dollar,Euro,Livre Sterling',
            'issue_date'  => 'required|date',
            'due_date'    => 'required|date',
            'phone'       => 'nullable|string|max:255',
            'email'       => 'nullable|email|max:255',
            'address'     => 'nullable|string|max:255',
        ]);

        $cleanAmount = (float) $this->cleanAmount($request->amount);
        $amountFcfa = $this->calculateFcfa($cleanAmount, $request->currency);

        $data['amount']      = $cleanAmount;
        $data['amount_fcfa'] = $amountFcfa;
        $data['status']      = 'pending';

        Settlement::create($data);

        Alert::success('Enregistré', 'Le règlement et son alerte automatique ont été créés.');
        return redirect()->route('settlementList');
    }

    /**
     * Détails d'un règlement
     */
    public function show($id)
    {
        $settlement = Settlement::findOrFail($id);
        return view('admin.settlement.details', compact('settlement'));
    }

    /**
     * Formulaire de modification
     */
    public function edit($id)
    {
        $settlement = Settlement::findOrFail($id);
        return view('admin.settlement.edit', compact('settlement'));
    }

    /**
     * Mise à jour d'un règlement
     */
    public function update(Request $request, $id)
    {
        $settlement = Settlement::findOrFail($id);

        $data = $request->validate([
            'type'        => 'required|in:debt,credit',
            'entity_name' => 'required|string|max:255',
            'amount'      => 'required',
            'currency'    => 'required|in:FCFA,Dollar,Euro,Livre Sterling',
            'issue_date'  => 'required|date',
            'due_date'    => 'required|date',
            'status'      => 'required|in:pending,paid',
            'phone'       => 'nullable|string|max:255',
            'email'       => 'nullable|email|max:255',
            'address'     => 'nullable|string|max:255',
        ]);

        $cleanAmount = (float) $this->cleanAmount($request->amount);
        $amountFcfa = $this->calculateFcfa($cleanAmount, $request->currency);

        $data['amount']      = $cleanAmount;
        $data['amount_fcfa'] = $amountFcfa;

        $settlement->update($data);

        Alert::success('Mis à jour', 'Les informations ont été modifiées avec succès.');
        return redirect()->route('settlementList');
    }

    /**
     * Action rapide : Marquer comme payé
     */
    public function updateStatus($id)
    {
        $settlement = Settlement::findOrFail($id);
        $settlement->update(['status' => 'paid']);

        Alert::success('Réglé', 'Le statut a été mis à jour. L\'alerte est désormais désactivée.');
        return back();
    }

    /**
     * Suppression d'un règlement
     */
    public function destroy($id)
    {
        try {
            $settlement = Settlement::findOrFail($id);
            $settlement->delete();
            Alert::success('Supprimé', 'L\'enregistrement a été retiré de la liste.');
            return redirect()->route('settlementList');
        } catch (\Exception $e) {
            Alert::error('Erreur', $e->getMessage());
            return redirect()->route('settlementList');
        }
    }
}