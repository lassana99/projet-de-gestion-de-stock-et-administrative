<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
// Importations pour l'exportation Excel
use App\Exports\ProductExport;
use Maatwebsite\Excel\Facades\Excel;

class ProductController extends Controller
{
    /**
     * Liste des produits avec RECHERCHE GLOBALE sur tous les champs
     */
    public function list(Request $request)
    {
        $query = Product::query();

        // Application de la recherche globale
        $this->applyFilters($query, $request->searchKey);

        // Tri par date de produit (métier)
        $products = $query->orderBy('date_product', 'desc')
            ->paginate(10);

        return view('admin.product.list', compact('products'));
    }

    /**
     * Méthode privée pour centraliser la logique de recherche (Web + Excel)
     */
    private function applyFilters($query, $key)
    {
        if ($key) {
            $query->where(function($q) use ($key) {
                $q->where('name', 'like', '%' . $key . '%')
                  ->orWhere('reference', 'like', '%' . $key . '%')
                  ->orWhere('suppliername', 'like', '%' . $key . '%')
                  ->orWhere('price', 'like', '%' . $key . '%')
                  ->orWhere('count', 'like', '%' . $key . '%')
                  ->orWhere('status', 'like', '%' . $key . '%')
                  ->orWhere('brand', 'like', '%' . $key . '%')
                  ->orWhere('date_product', 'like', '%' . $key . '%')
                  ->orWhere('description', 'like', '%' . $key . '%');
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
        // Génération du fichier Excel en passant la clé de recherche
        return Excel::download(new ProductExport($searchKey), 'liste_produits_' . date('d_m_Y_Hi') . '.xlsx');
    }

    /**
     * Page création produit
     */
    public function createPage()
    {
        $machines = DB::table('machines_profitabilities')->get();
        return view('admin.product.create', compact('machines'));
    }

    /**
     * Création du produit
     */
    public function productCreate(Request $request)
    {
        // Validation incluant la date
        $request->validate([
            'reference'    => 'required|string|exists:machines_profitabilities,purchase_reference',
            'name'         => 'required|string|max:255',
            'date_product' => 'required|date',
            'price'        => 'nullable|string',
            'count'        => 'nullable|integer|min:0',
            'status'       => 'nullable|string',
            'brand'        => 'nullable|string',
            'suppliername' => 'nullable|string',
            'image'        => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'description'  => 'nullable|string',
        ]);

        // Récupération de la machine de référence pour les valeurs par défaut
        $machine = DB::table('machines_profitabilities')
            ->where('purchase_reference', $request->reference)
            ->first();

        if (!$machine) {
            return back()->withErrors(['reference' => 'Référence invalide !'])->withInput();
        }

        // Nettoyage et priorité aux valeurs saisies manuellement
        $price = $request->filled('price') 
                 ? str_replace(' ', '', $request->price) 
                 : str_replace(' ', '', $machine->selling_price);

        $count    = $request->filled('count') ? $request->count : ($machine->quantity ?? 1);
        $status   = $request->filled('status') ? $request->status : ($machine->status ?? 'Neuve');
        $brand    = $request->filled('brand') ? $request->brand : ($machine->brand ?? '');
        $supplier = $request->filled('suppliername') ? $request->suppliername : ($machine->suppliername ?? '');
        $date     = $request->date_product;

        $data = [
            'reference'    => $machine->purchase_reference,
            'name'         => $request->name,
            'suppliername' => $supplier,
            'price'        => $price,
            'count'        => $count,
            'status'       => $status,
            'brand'        => $brand,
            'date_product' => $date,
            'description'  => $request->description ?? '',
        ];

        // Gestion de l'image
        if ($request->hasFile('image')) {
            $fileName = uniqid() . '_' . $request->file('image')->getClientOriginalName();
            $request->file('image')->move(public_path('purchaseImages'), $fileName);
            $data['image'] = $fileName;
        } else {
            $data['image'] = 'default.jpg';
        }

        Product::create($data);

        session()->flash('success', 'Produit créé avec succès !');
        return redirect()->route('productList');
    }

    /**
     * Page édition
     */
    public function edit($id)
    {
        $product  = Product::findOrFail($id);
        $machines = DB::table('machines_profitabilities')->get();

        return view('admin.product.edit', compact('product', 'machines'));
    }

    /**
     * Mise à jour
     */
    public function update(Request $request)
    {
        $request->validate([
            'productID'    => 'required|exists:products,id',
            'name'         => 'required|string|max:255',
            'price'        => 'required|string',
            'count'        => 'required|integer|min:0',
            'status'       => 'required|string',
            'brand'        => 'required|string',
            'date_product' => 'required|date',
            'suppliername' => 'nullable|string',
            'image'        => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'description'  => 'nullable|string',
        ]);

        $product = Product::findOrFail($request->productID);

        // Nettoyage du prix
        $cleanPrice = str_replace(' ', '', $request->price);

        $data = [
            'name'         => $request->name,
            'price'        => $cleanPrice,
            'count'        => $request->count,
            'status'       => $request->status,
            'brand'        => $request->brand,
            'date_product' => $request->date_product,
            'suppliername' => $request->suppliername,
            'description'  => $request->description ?? '',
        ];

        if ($request->hasFile('image')) {
            // Suppression de l'ancienne image si elle existe
            if ($product->image && $product->image !== 'default.jpg' && File::exists(public_path('purchaseImages/' . $product->image))) {
                File::delete(public_path('purchaseImages/' . $product->image));
            }

            $fileName = uniqid() . '_' . $request->file('image')->getClientOriginalName();
            $request->file('image')->move(public_path('purchaseImages'), $fileName);
            $data['image'] = $fileName;
        }

        $product->update($data);

        session()->flash('success', 'Produit mis à jour avec succès !');
        return redirect()->route('productList');
    }

    /**
     * Détails
     */
    public function details($id)
    {
        $data = Product::findOrFail($id);
        return view('admin.product.details', compact('data'));
    }

    /**
     * Suppression
     */
    public function delete($id)
    {
        $product = Product::findOrFail($id);

        if ($product->image && $product->image !== 'default.jpg' && File::exists(public_path('purchaseImages/' . $product->image))) {
            File::delete(public_path('purchaseImages/' . $product->image));
        }

        $product->delete();

        session()->flash('success', 'Produit supprimé avec succès !');
        return back();
    }
}