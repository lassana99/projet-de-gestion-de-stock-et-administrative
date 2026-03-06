<?php

namespace App\Http\Controllers;

use App\Models\Funding;
use Illuminate\Http\Request;
// Importations pour l'exportation Excel
use App\Exports\FundingExport;
use Maatwebsite\Excel\Facades\Excel;

class FundingController extends Controller
{
    /**
     * Liste - correspond à funding.list avec filtrage
     */
    public function index(Request $request)
    {
        $query = Funding::query();

        // Application des filtres (Logique centralisée)
        $this->applyFilters($query, $request->input('searchKey'));

        $fundings = $query->paginate(10);

        return view('admin.funding.list', compact('fundings'));
    }

    /**
     * Méthode privée pour centraliser la logique de recherche
     * utilisée par l'index et par l'export Excel
     */
    private function applyFilters($query, $search)
    {
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('motif', 'like', '%' . $search . '%')
                  ->orWhere('nom_de_banque', 'like', '%' . $search . '%');
            });
        }
        return $query;
    }

    /**
     * EXPORT EXCEL UNIQUEMENT
     */
    public function exportExcel(Request $request) 
    {
        $searchKey = $request->get('searchKey');
        // Génération du fichier Excel en passant la clé de recherche à la classe Export
        return Excel::download(new FundingExport($searchKey), 'liste_prets_bancaires_' . date('d_m_Y_Hi') . '.xlsx');
    }

    /**
     * Formulaire création - funding.create
     */
    public function create()
    {
        return view('admin.funding.create');
    }

    /**
     * Enregistrement création - funding.store
     */
    public function store(Request $request)
    {
        if ($request->has('montant_emprunte')) {
            $montantRaw = $request->input('montant_emprunte');
            $request->merge([
                'montant_emprunte' => str_replace(' ', '', $montantRaw),
            ]);
        }

        $request->validate([
            'motif' => 'required|string|max:255',
            'nom_de_banque' => 'required|string|max:255',
            'other_banque' => 'nullable|string|max:255',
            'montant_emprunte' => 'required|numeric|min:0',
            'nombre_de_jours' => 'required|integer|min:1',
            'taux' => 'required|numeric|min:0',
            'nombre_d_items' => 'required|integer|min:1',
            'date' => 'required|date',
        ]);

        $nomBanque = $request->nom_de_banque === 'Autres' ? $request->other_banque : $request->nom_de_banque;

        $valeurDuTaux = $request->taux / 100;
        $montantAPayer = (($request->montant_emprunte * $request->nombre_de_jours * $valeurDuTaux) / 36000) * $request->nombre_de_jours;
        $montantAPayerParItem = $montantAPayer / $request->nombre_d_items;

        Funding::create([
            'motif' => $request->motif,
            'nom_de_banque' => $nomBanque,
            'montant_emprunte' => $request->montant_emprunte,
            'nombre_de_jours' => $request->nombre_de_jours,
            'taux' => $request->taux,
            'montant_a_payer' => $montantAPayer,
            'montant_a_payer_par_item' => $montantAPayerParItem,
            'nombre_d_items' => $request->nombre_d_items,
            'date' => $request->date,
        ]);

        return redirect()->route('funding.list')->with('success', 'Funding créé avec succès.');
    }

    /**
     * Affichage details - funding.details
     */
    public function show(Funding $funding)
    {
        return view('admin.funding.details', compact('funding'));
    }

    /**
     * Formulaire édition - funding.edit
     */
    public function edit(Funding $funding)
    {
        return view('admin.funding.edit', compact('funding'));
    }

    /**
     * Mise à jour - funding.update
     */
    public function update(Request $request, Funding $funding)
    {
        if ($request->has('montant_emprunte')) {
            $montantRaw = $request->input('montant_emprunte');
            $request->merge([
                'montant_emprunte' => str_replace(' ', '', $montantRaw),
            ]);
        }

        $request->validate([
            'motif' => 'required|string|max:255',
            'nom_de_banque' => 'required|string|max:255',
            'other_banque' => 'nullable|string|max:255',
            'montant_emprunte' => 'required|numeric|min:0',
            'nombre_de_jours' => 'required|integer|min:1',
            'taux' => 'required|numeric|min:0',
            'nombre_d_items' => 'required|integer|min:1',
            'date' => 'required|date',
        ]);

        $nomBanque = $request->nom_de_banque === 'Autres' ? $request->other_banque : $request->nom_de_banque;

        $valeurDuTaux = $request->taux / 100;
        $montantAPayer = (($request->montant_emprunte * $request->nombre_de_jours * $valeurDuTaux) / 36000) * $request->nombre_de_jours;
        $montantAPayerParItem = $montantAPayer / $request->nombre_d_items;

        $funding->update([
            'motif' => $request->motif,
            'nom_de_banque' => $nomBanque,
            'montant_emprunte' => $request->montant_emprunte,
            'nombre_de_jours' => $request->nombre_de_jours,
            'taux' => $request->taux,
            'montant_a_payer' => $montantAPayer,
            'montant_a_payer_par_item' => $montantAPayerParItem,
            'nombre_d_items' => $request->nombre_d_items,
            'date' => $request->date,
        ]);

        return redirect()->route('funding.list')->with('success', 'Funding mis à jour avec succès.');
    }

    /**
     * Suppression - funding.destroy
     */
    public function destroy(Funding $funding)
    {
        $funding->delete();
        return redirect()->route('funding.list')->with('success', 'Funding supprimé avec succès.');
    }
}