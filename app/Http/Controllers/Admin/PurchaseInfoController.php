<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Purchase;
use App\Models\Supplier;
use RealRashid\SweetAlert\Facades\Alert;
// Importations pour l'exportation Excel
use App\Exports\PurchaseExport;
use Maatwebsite\Excel\Facades\Excel;

class PurchaseInfoController extends Controller
{
    /**
     * Liste des achats avec RECHERCHE GLOBALE et TRI DYNAMIQUE
     */
    public function index(Request $request)
    {
        $searchKey = $request->input('searchKey');
        $query = Purchase::query();

        // 1. RECHERCHE GLOBALE (Logique extraite pour être réutilisée)
        $this->applySearchFilters($query, $searchKey);

        // 2. TRI DYNAMIQUE
        $sort = $request->get('sort', 'date_purchase'); // Champ par défaut
        $direction = $request->get('direction', 'desc'); // Direction par défaut

        // Liste blanche des colonnes autorisées pour le tri
        $allowedSorts = [
            'reference', 'purchasename', 'date_purchase', 'Category', 
            'brand', 'status', 'purchaseprice', 'purchase_price_fcfa', 
            'currency', 'quantity', 'suppliername', 'country'
        ];

        if (!in_array($sort, $allowedSorts)) {
            $sort = 'date_purchase';
        }

        if (!in_array(strtolower($direction), ['asc', 'desc'])) {
            $direction = 'desc';
        }

        $purchases = $query->orderBy($sort, $direction)->paginate(15);
        
        return view('admin.purchases.list', compact('purchases'));
    }

    /**
     * Méthode privée pour centraliser la logique de recherche (Web + Excel)
     */
    private function applySearchFilters($query, $searchKey)
    {
        if ($searchKey) {
            $query->where(function($q) use ($searchKey) {
                $q->where('reference', 'like', "%{$searchKey}%")
                  ->orWhere('purchasename', 'like', "%{$searchKey}%")
                  ->orWhere('description', 'like', "%{$searchKey}%")
                  ->orWhere('Category', 'like', "%{$searchKey}%")
                  ->orWhere('brand', 'like', "%{$searchKey}%")
                  ->orWhere('status', 'like', "%{$searchKey}%")
                  ->orWhere('suppliername', 'like', "%{$searchKey}%")
                  ->orWhere('country', 'like', "%{$searchKey}%")
                  ->orWhere('transport_mode', 'like', "%{$searchKey}%")
                  ->orWhere('incoterm', 'like', "%{$searchKey}%")
                  ->orWhere('currency', 'like', "%{$searchKey}%")
                  ->orWhere('purchaseprice', 'like', "%{$searchKey}%")
                  ->orWhere('purchase_price_fcfa', 'like', "%{$searchKey}%")
                  ->orWhere('date_purchase', 'like', "%{$searchKey}%");
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
        // On génère le fichier Excel en passant la clé de recherche
        return Excel::download(new PurchaseExport($searchKey), 'liste_achats_' . date('d_m_Y_Hi') . '.xlsx');
    }

    public function create()
    {
        $categories = ['Machine', 'Pièce de rechange et accessoire', 'Autres'];
        $suppliers = Supplier::all();
        return view('admin.purchases.create', compact('categories', 'suppliers'));
    }

    public function store(Request $request)
    {
        /* =========================
           Nettoyage des prix
        ==========================*/
        $this->cleanPriceInputs($request);

        /* =========================
           Validation
        ==========================*/
        $validatedData = $request->validate([
            'reference' => 'nullable|string',
            'description' => 'nullable|string',
            'purchasename' => 'required|string',
            'date_purchase' => 'required|date',
            'Category' => 'nullable|string',
            'other_category' => 'nullable|string',
            'brand' => 'nullable|string',
            'other_brand' => 'nullable|string',
            'status' => 'nullable|string',
            'purchaseprice' => 'required|numeric',
            'purchase_price_fcfa' => 'nullable|numeric',
            'currency' => 'required|string',
            'weight' => 'nullable|numeric',
            'quantity' => 'required|integer',
            'supplier_id' => 'nullable|string',
            'suppliername' => 'nullable|string',
            'country' => 'nullable|string',
            'transport_mode' => 'nullable|string',
            'incoterm' => 'nullable|string|max:100',
            'purchaseimage' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:2048',
        ]);

        /* =========================
           Calcul automatique du FCFA
        ==========================*/
        $validatedData['purchase_price_fcfa'] = $this->calculateFcfa($validatedData['purchaseprice'], $validatedData['currency']);

        /* =========================
           Gestion catégorie "Autres"
        ==========================*/
        if ($validatedData['Category'] === 'Autres' && !empty($validatedData['other_category'])) {
            $validatedData['Category'] = $validatedData['other_category'];
        }
        unset($validatedData['other_category']);

        /* =========================
           Gestion marque "Autres"
        ==========================*/
        if (isset($validatedData['brand']) && $validatedData['brand'] === 'Autres' && !empty($validatedData['other_brand'])) {
            $validatedData['brand'] = $validatedData['other_brand'];
        }
        unset($validatedData['other_brand']);

        /* =========================
           Gestion fournisseur
        ==========================*/
        if (isset($validatedData['supplier_id']) && $validatedData['supplier_id'] === 'other') {
            if (empty($validatedData['suppliername'])) {
                return back()->withErrors(['suppliername' => 'Merci de saisir un fournisseur.'])->withInput();
            }
        } else {
            if (!empty($validatedData['supplier_id'])) {
                $supplier = Supplier::find($validatedData['supplier_id']);
                if ($supplier) {
                    $validatedData['suppliername'] = $supplier->company_name;
                }
            }
        }
        unset($validatedData['supplier_id']);

        /* =========================
           🔐 Validation métier UNIQUE
        ==========================*/
        $exists = Purchase::where('reference', $validatedData['reference'])
            ->where('suppliername', $validatedData['suppliername'])
            ->where('purchaseprice', $validatedData['purchaseprice'])
            ->exists();

        if ($exists) {
            return back()->withErrors([
                'reference' => 'Un produit avec la même référence, le même fournisseur et le même prix existe déjà.'
            ])->withInput();
        }

        /* =========================
           Upload image
        ==========================*/
        if ($request->hasFile('purchaseimage')) {
            $image = $request->file('purchaseimage');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('purchaseImages'), $imageName);
            $validatedData['purchaseimage'] = $imageName;
        }

        Purchase::create($validatedData);

        Alert::success('Insertion réussie', 'Achat enregistré avec succès le ' . date('d_m_Y', strtotime($validatedData['date_purchase'])));
        return redirect()->route('purchaseList');
    }

    public function show($id)
    {
        $purchase = Purchase::findOrFail($id);
        $suppliers = Supplier::all();
        return view('admin.purchases.details', compact('purchase', 'suppliers'));
    }

    public function edit($id)
    {
        $purchase = Purchase::findOrFail($id);
        $categories = ['Machine', 'Pièce de rechange et accessoire', 'Autres'];
        $suppliers = Supplier::all();
        return view('admin.purchases.edit', compact('purchase', 'categories', 'suppliers'));
    }

    public function update(Request $request, $id)
    {
        $purchase = Purchase::findOrFail($id);

        $this->cleanPriceInputs($request);

        $validatedData = $request->validate([
            'reference' => 'nullable|string',
            'description' => 'nullable|string',
            'purchasename' => 'required|string',
            'date_purchase' => 'required|date',
            'Category' => 'nullable|string',
            'other_category' => 'nullable|string',
            'brand' => 'nullable|string',
            'other_brand' => 'nullable|string',
            'status' => 'nullable|string',
            'purchaseprice' => 'required|numeric',
            'purchase_price_fcfa' => 'nullable|numeric',
            'currency' => 'required|string',
            'weight' => 'nullable|numeric',
            'quantity' => 'required|integer',
            'supplier_id' => 'nullable|string',
            'suppliername' => 'nullable|string',
            'country' => 'nullable|string',
            'transport_mode' => 'nullable|string',
            'incoterm' => 'nullable|string|max:100',
            'purchaseimage' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:2048',
        ]);

        $validatedData['purchase_price_fcfa'] = $this->calculateFcfa($validatedData['purchaseprice'], $validatedData['currency']);

        if ($validatedData['Category'] === 'Autres' && !empty($validatedData['other_category'])) {
            $validatedData['Category'] = $validatedData['other_category'];
        }
        unset($validatedData['other_category']);

        if (isset($validatedData['brand']) && $validatedData['brand'] === 'Autres' && !empty($validatedData['other_brand'])) {
            $validatedData['brand'] = $validatedData['other_brand'];
        }
        unset($validatedData['other_brand']);

        if (isset($validatedData['supplier_id']) && $validatedData['supplier_id'] === 'other') {
            if (empty($validatedData['suppliername'])) {
                return back()->withErrors(['suppliername' => 'Merci de saisir un fournisseur.'])->withInput();
            }
        } else {
            if (!empty($validatedData['supplier_id'])) {
                $supplier = Supplier::find($validatedData['supplier_id']);
                if ($supplier) {
                    $validatedData['suppliername'] = $supplier->company_name;
                }
            }
        }
        unset($validatedData['supplier_id']);

        $exists = Purchase::where('reference', $validatedData['reference'])
            ->where('suppliername', $validatedData['suppliername'])
            ->where('purchaseprice', $validatedData['purchaseprice'])
            ->where('id', '!=', $purchase->id)
            ->exists();

        if ($exists) {
            return back()->withErrors(['reference' => 'Un produit identique existe déjà.'])->withInput();
        }

        if ($request->hasFile('purchaseimage')) {
            $image = $request->file('purchaseimage');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('purchaseImages'), $imageName);

            if ($purchase->purchaseimage && file_exists(public_path('purchaseImages/' . $purchase->purchaseimage))) {
                unlink(public_path('purchaseImages/' . $purchase->purchaseimage));
            }
            $validatedData['purchaseimage'] = $imageName;
        }

        $purchase->update($validatedData);

        Alert::success('Mise à jour réussie', 'Achat mis à jour avec succès');
        return redirect()->route('purchaseList');
    }

    public function destroy($id)
    {
        $purchase = Purchase::findOrFail($id);
        if ($purchase->purchaseimage && file_exists(public_path('purchaseImages/' . $purchase->purchaseimage))) {
            unlink(public_path('purchaseImages/' . $purchase->purchaseimage));
        }
        $purchase->delete();
        Alert::success('Suppression réussie', 'Achat supprimé avec succès');
        return redirect()->route('purchaseList');
    }

    private function cleanPriceInputs(Request $request)
    {
        $fields = ['purchaseprice', 'purchase_price_fcfa'];
        foreach ($fields as $field) {
            if ($request->has($field)) {
                $clean = str_replace(' ', '', $request->input($field));
                $clean = str_replace(',', '.', $clean);
                $request->merge([$field => $clean]);
            }
        }
    }

    private function calculateFcfa($price, $currency)
    {
        $price = (float) $price;
        switch ($currency) {
            case 'Euro':
                return $price * 656;
            case 'Dollar':
                return $price * 610;
            case 'Livre Sterling':
                return $price * 780;
            case 'FCFA':
            default:
                return $price;
        }
    }
}