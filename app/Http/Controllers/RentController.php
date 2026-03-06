<?php

namespace App\Http\Controllers;

use App\Models\Rent;
use Illuminate\Http\Request;
use App\Exports\RentExport;
use Maatwebsite\Excel\Facades\Excel;

class RentController extends Controller
{
    /**
     * Liste des loyers avec recherche globale
     */
    public function index(Request $request) {
        $query = Rent::query();
        if ($request->filled('searchKey')) {
            $s = $request->searchKey;
            $query->where(function($q) use ($s) {
                $q->where('number', 'like', "%$s%")
                  ->orWhere('month', 'like', "%$s%")
                  ->orWhere('structure', 'like', "%$s%")
                  ->orWhere('reference', 'like', "%$s%")
                  ->orWhere('status', 'like', "%$s%");
            });
        }
        $rents = $query->orderBy('issue_date', 'desc')->paginate(10);
        return view('admin.expenses.rent.list', compact('rents'));
    }

    /**
     * Formulaire de création
     */
    public function create() {
        // Liste des mois pour la liste déroulante
        $months = [
            'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 
            'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'
        ];
        
        return view('admin.expenses.rent.create', compact('months'));
    }

    /**
     * Enregistrement avec génération automatique du numéro
     */
    public function store(Request $request) {
        $data = $request->validate([
            'month' => 'required|string',
            'structure' => 'required|string',
            'reference' => 'nullable|string', // Référence ajoutée
            'issue_date' => 'required|date',
            'amount_fcfa' => 'required|numeric',
            'payment_mode' => 'required',
            'payment_mode_other' => 'required_if:payment_mode,Autres',
            'status' => 'required'
        ]);

        // --- GÉNÉRATION AUTOMATIQUE DU NUMÉRO ---
        // On récupère le dernier enregistrement pour incrémenter
        $lastRent = Rent::orderBy('id', 'desc')->first();
        if (!$lastRent) {
            $data['number'] = 'LYR-001';
        } else {
            // On extrait le nombre (ex: de LYR-005 on tire 5)
            $lastNumber = (int) str_replace('LYR-', '', $lastRent->number);
            $nextNumber = $lastNumber + 1;
            // On reformate avec des zéros devant (ex: 006)
            $data['number'] = 'LYR-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
        }

        Rent::create($data);

        return redirect()->route('rentList')->with('success', 'Loyer enregistré avec le numéro ' . $data['number']);
    }

    /**
     * Formulaire de modification
     */
    public function edit($id) {
        $rent = Rent::findOrFail($id);
        $months = [
            'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 
            'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'
        ];
        
        return view('admin.expenses.rent.edit', compact('rent', 'months'));
    }

    /**
     * Mise à jour
     */
    public function update(Request $request, $id) {
        $rent = Rent::findOrFail($id);
        
        $data = $request->validate([
            'month' => 'required|string',
            'structure' => 'required|string',
            'reference' => 'nullable|string',
            'issue_date' => 'required|date',
            'amount_fcfa' => 'required|numeric',
            'payment_mode' => 'required',
            'payment_mode_other' => 'required_if:payment_mode,Autres',
            'status' => 'required'
        ]);

        // On ne modifie pas le numéro lors de l'update pour garder la traçabilité
        $rent->update($data);

        return redirect()->route('rentList')->with('success', 'Loyer mis à jour avec succès.');
    }

    /**
     * Détails
     */
    public function show($id) {
        $rent = Rent::findOrFail($id);
        return view('admin.expenses.rent.details', compact('rent'));
    }

    /**
     * Suppression
     */
    public function destroy($id) {
        try {
            $rent = Rent::findOrFail($id);
            $rent->delete();
            return back()->with('success', 'Le loyer a été supprimé.');
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de la suppression.');
        }
    }

    /**
     * Export Excel
     */
    public function exportExcel(Request $request) {
        return Excel::download(new RentExport($request->searchKey), 'loyers_'.date('d_m_Y').'.xlsx');
    }
}