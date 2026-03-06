<?php

namespace App\Http\Controllers;

use App\Models\Tax;
use Illuminate\Http\Request;
use App\Exports\TaxExport; // Importation de la classe d'exportation
use Maatwebsite\Excel\Facades\Excel;

class TaxController extends Controller
{
    // Listes statiques pour les menus déroulants
    private $months = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
    private $descriptions = ['Déclaration mensuelle', 'TVA (18%)', 'Patente', 'Autres'];

    /**
     * Liste des impôts avec recherche globale
     */
    public function index(Request $request) {
        $query = Tax::query();
        
        if ($request->filled('searchKey')) {
            $s = $request->searchKey;
            $query->where(function($q) use ($s) {
                $q->where('number', 'like', "%$s%")
                  ->orWhere('month', 'like', "%$s%")
                  ->orWhere('description', 'like', "%$s%")
                  ->orWhere('description_other', 'like', "%$s%")
                  ->orWhere('reference', 'like', "%$s%");
            });
        }
        
        $taxes = $query->orderBy('issue_date', 'desc')->paginate(10);
        return view('admin.expenses.tax.list', compact('taxes'));
    }

    /**
     * Exportation Excel
     */
    public function exportExcel(Request $request) 
    {
        $searchKey = $request->get('searchKey');
        // On génère le fichier en passant la clé de recherche à la classe TaxExport
        return Excel::download(new TaxExport($searchKey), 'liste_impots_' . date('d_m_Y_Hi') . '.xlsx');
    }

    /**
     * Formulaire de création
     */
    public function create() {
        return view('admin.expenses.tax.create', [
            'months' => $this->months, 
            'descriptions' => $this->descriptions
        ]);
    }

    /**
     * Enregistrement avec génération auto du numéro
     */
    public function store(Request $request) {
        $data = $request->validate([
            'month' => 'required|string',
            'description' => 'required|string',
            'description_other' => 'required_if:description,Autres',
            'reference' => 'nullable|string',
            'issue_date' => 'required|date',
            'amount_fcfa' => 'required|numeric',
            'payment_mode' => 'required|string',
        ]);

        // Génération automatique du numéro TAX-001, TAX-002...
        $lastTax = Tax::orderBy('id', 'desc')->first();
        if (!$lastTax) {
            $data['number'] = 'TAX-001';
        } else {
            $lastNumber = (int) str_replace('TAX-', '', $lastTax->number);
            $data['number'] = 'TAX-' . str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        }

        Tax::create($data);

        return redirect()->route('taxList')->with('success', 'Impôt enregistré avec succès sous le numéro : ' . $data['number']);
    }

    /**
     * Formulaire de modification
     */
    public function edit($id) {
        $tax = Tax::findOrFail($id);
        return view('admin.expenses.tax.edit', [
            'tax' => $tax, 
            'months' => $this->months, 
            'descriptions' => $this->descriptions
        ]);
    }

    /**
     * Mise à jour des informations
     */
    public function update(Request $request, $id) {
        $tax = Tax::findOrFail($id);
        
        $data = $request->validate([
            'month' => 'required|string',
            'description' => 'required|string',
            'description_other' => 'required_if:description,Autres',
            'reference' => 'nullable|string',
            'issue_date' => 'required|date',
            'amount_fcfa' => 'required|numeric',
            'payment_mode' => 'required|string',
        ]);

        $tax->update($data);

        return redirect()->route('taxList')->with('success', 'L\'impôt a été mis à jour avec succès.');
    }

    /**
     * Affichage des détails
     */
    public function show($id) {
        $tax = Tax::findOrFail($id);
        return view('admin.expenses.tax.details', compact('tax'));
    }

    /**
     * Suppression
     */
    public function destroy($id) {
        try {
            $tax = Tax::findOrFail($id);
            $tax->delete();
            return back()->with('success', 'L\'impôt a été supprimé de la liste.');
        } catch (\Exception $e) {
            return back()->with('error', 'Une erreur est survenue lors de la suppression.');
        }
    }
}