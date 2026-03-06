<?php

namespace App\Http\Controllers;

use App\Models\OtherExpense;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\OtherExpenseExport;

class OtherExpenseController extends Controller
{
    /**
     * Liste des dépenses avec recherche globale adaptée
     */
    public function index(Request $request) {
        $query = OtherExpense::query();
        if ($request->filled('searchKey')) {
            $s = $request->searchKey;
            $query->where(function($q) use ($s) {
                $q->where('number', 'like', "%$s%")
                  ->orWhere('full_name', 'like', "%$s%")
                  ->orWhere('payment_reason', 'like', "%$s%")
                  ->orWhere('designation', 'like', "%$s%"); // Recherche par désignation
            });
        }
        $expenses = $query->orderBy('date', 'desc')->paginate(10);
        return view('admin.expenses.other.list', compact('expenses'));
    }

    /**
     * Formulaire de création
     */
    public function create() {
        return view('admin.expenses.other.create');
    }

    /**
     * Enregistrement d'une nouvelle dépense
     */
    public function store(Request $request) {
        $data = $request->validate([
            'full_name'            => 'required|string|max:255',
            'amount_fcfa'          => 'required|numeric',
            'payment_reason'       => 'required|string',
            'payment_reason_other' => 'required_if:payment_reason,Autres',
            'designation'          => 'nullable|string', // Nouveau champ de saisie simple
            'additional_details'   => 'nullable|string',
            'payment_mode'         => 'required|string',
            'date'                 => 'required|date'
        ]);

        // Génération automatique du numéro OTH-XXX
        $last = OtherExpense::orderBy('id', 'desc')->first();
        $nextNum = $last ? ((int) str_replace('OTH-', '', $last->number)) + 1 : 1;
        $data['number'] = 'OTH-' . str_pad($nextNum, 3, '0', STR_PAD_LEFT);

        OtherExpense::create($data);

        return redirect()->route('otherExpenseList')->with('success', 'Dépense enregistrée : ' . $data['number']);
    }

    /**
     * Affichage des détails
     */
    public function show($id) {
        $expense = OtherExpense::findOrFail($id);
        return view('admin.expenses.other.details', compact('expense'));
    }

    /**
     * Formulaire de modification
     */
    public function edit($id) {
        $expense = OtherExpense::findOrFail($id);
        return view('admin.expenses.other.edit', compact('expense'));
    }

    /**
     * Mise à jour de la dépense
     */
    public function update(Request $request, $id) {
        $expense = OtherExpense::findOrFail($id);

        $data = $request->validate([
            'full_name'            => 'required|string|max:255',
            'amount_fcfa'          => 'required|numeric',
            'payment_reason'       => 'required|string',
            'payment_reason_other' => 'required_if:payment_reason,Autres',
            'designation'          => 'nullable|string', // Nouveau champ de saisie simple
            'additional_details'   => 'nullable|string',
            'payment_mode'         => 'required|string',
            'date'                 => 'required|date'
        ]);

        $expense->update($data);

        return redirect()->route('otherExpenseList')->with('success', 'Mise à jour réussie.');
    }

    /**
     * Suppression
     */
    public function destroy($id) {
        try {
            $expense = OtherExpense::findOrFail($id);
            $expense->delete();
            return back()->with('success', 'La dépense a été supprimée.');
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de la suppression.');
        }
    }

    /**
     * Exportation Excel
     */
    public function exportExcel(Request $request) {
        return Excel::download(new OtherExpenseExport($request->searchKey), 'autres_depenses_'.date('d_m_Y').'.xlsx');
    }
}