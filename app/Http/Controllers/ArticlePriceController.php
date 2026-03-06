<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\ArticlePrice;
use Illuminate\Http\Request;
// Importations pour l'exportation Excel
use App\Exports\ArticlePriceExport;
use Maatwebsite\Excel\Facades\Excel;

class ArticlePriceController extends Controller
{
    /**
     * Liste des prix articles avec RECHERCHE GLOBALE et TRI sur tous les champs
     */
    public function index(Request $request)
    {
        $query = ArticlePrice::query();

        // Application des filtres de recherche globale
        $this->applySearchFilters($query, $request->searchKey);

        // 2. TRI DYNAMIQUE
        $sort = $request->get('sort', 'created_at'); // Tri par défaut
        $direction = $request->get('direction', 'desc'); // Direction par défaut

        // Liste blanche des colonnes autorisées pour le tri
        $allowedSorts = [
            'reference', 'designation', 'unit_price', 'currency', 
            'type', 'date', 'incoterm', 'country', 'created_at'
        ];

        if (!in_array($sort, $allowedSorts)) {
            $sort = 'created_at';
        }

        if (!in_array(strtolower($direction), ['asc', 'desc'])) {
            $direction = 'desc';
        }

        $articlePrices = $query->orderBy($sort, $direction)->paginate(10);
        
        return view('admin.article_prices.list', compact('articlePrices'));
    }

    /**
     * Méthode privée pour centraliser la logique de recherche
     * afin qu'elle soit identique entre la liste et l'export Excel
     */
    private function applySearchFilters($query, $search)
    {
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('reference', 'like', "%{$search}%")
                  ->orWhere('designation', 'like', "%{$search}%")
                  ->orWhere('unit_price', 'like', "%{$search}%")
                  ->orWhere('currency', 'like', "%{$search}%")
                  ->orWhere('type', 'like', "%{$search}%")
                  ->orWhere('supplier_name', 'like', "%{$search}%")
                  ->orWhere('incoterm', 'like', "%{$search}%")
                  ->orWhere('country', 'like', "%{$search}%")
                  ->orWhere('date', 'like', "%{$search}%")
                  // Recherche également par le nom de l'entreprise si liée à un fournisseur
                  ->orWhereHas('supplier', function($sq) use ($search) {
                      $sq->where('company_name', 'like', "%{$search}%");
                  });
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
        // On passe la clé de recherche à la classe Export pour filtrer le contenu du fichier Excel
        return Excel::download(new ArticlePriceExport($searchKey), 'prix_fournisseurs_' . date('d_m_Y_Hi') . '.xlsx');
    }

    /**
     * Formulaire de création
     */
    public function create()
    {
        $suppliers = Supplier::orderBy('company_name')->get();
        return view('admin.article_prices.create', compact('suppliers'));
    }

    /**
     * Enregistrement en base de données
     */
    public function store(Request $request)
    {
        $request->validate([
            'designation'   => 'required|string|max:255',
            'unit_price'    => 'required|numeric',
            'currency'      => 'required|string',
            'type'          => 'required|in:Originale,Aftermarket',
            'supplier_id'   => 'nullable',
            'supplier_name' => 'nullable|string|max:255',
            'date'          => 'required|date',
            'incoterm'      => 'nullable|string|max:100',
            'country'       => 'nullable|string|max:255',
        ]);

        $data = $request->all();

        if ($request->supplier_id != 'other') {
            $data['supplier_name'] = null;
        } else {
            $data['supplier_id'] = null;
        }

        ArticlePrice::create($data);

        return redirect()->route('articlePriceList')->with('success', 'Le prix fournisseur a été ajouté avec succès.');
    }

    /**
     * Formulaire de modification
     */
    public function edit($id)
    {
        $articlePrice = ArticlePrice::findOrFail($id);
        $suppliers = Supplier::orderBy('company_name')->get();
        return view('admin.article_prices.edit', compact('articlePrice', 'suppliers'));
    }

    /**
     * Mise à jour en base de données
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'designation'   => 'required|string|max:255',
            'unit_price'    => 'required|numeric',
            'currency'      => 'required|string',
            'type'          => 'required|in:Originale,Aftermarket',
            'supplier_id'   => 'nullable',
            'supplier_name' => 'nullable|string|max:255',
            'date'          => 'required|date',
            'incoterm'      => 'nullable|string|max:100',
            'country'       => 'nullable|string|max:255',
        ]);

        $articlePrice = ArticlePrice::findOrFail($id);
        $data = $request->all();

        if ($request->supplier_id != 'other') {
            $data['supplier_name'] = null;
        } else {
            $data['supplier_id'] = null;
        }

        $articlePrice->update($data);

        return redirect()->route('articlePriceList')->with('success', 'Le prix fournisseur a été mis à jour avec succès.');
    }

    /**
     * Affichage des détails
     */
    public function show($id)
    {
        $articlePrice = ArticlePrice::findOrFail($id);
        return view('admin.article_prices.details', compact('articlePrice'));
    }

    /**
     * Suppression
     */
    public function destroy($id)
    {
        $articlePrice = ArticlePrice::findOrFail($id);
        $articlePrice->delete();

        return redirect()->route('articlePriceList')->with('success', 'Le prix fournisseur a été supprimé avec succès.');
    }
}