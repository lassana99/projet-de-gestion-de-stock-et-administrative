<?php

namespace App\Http\Controllers\Admin;

use App\Models\Delivery;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class DeliveryController extends Controller
{
    // Afficher la liste des livraisons
    public function list()
    {
        $deliveries = Delivery::with('product')->paginate(5);
        return view('admin.delivery.list', compact('deliveries'));
    }

    // Afficher le formulaire de création
    public function createPage()
    {
        $products = Product::all();
        return view('admin.delivery.create', compact('products'));
    }

    // Enregistrer une nouvelle livraison
    public function deliveryCreate(Request $request)
{
    $request->validate([
        'invoice_number' => 'required|unique:deliveries',
        'customer_name' => 'required',
        'customer_address' => 'required',
        'quantity' => 'required|numeric|min:1',
        'delivery_date' => 'required|date',
        'product_id' => 'required|exists:products,id',
        'image' => 'nullable|image|max:2048'
    ]);

    $data = $request->all();

    // Définir une valeur par défaut pour le statut si non renseigné
    $data['delivery_status'] = $request->delivery_status ?? 'pending';

    if ($request->hasFile('image')) {
        $data['image'] = $request->file('image')->store('deliveries', 'public');
    }

    Delivery::create($data);

    return redirect()->route('deliveryList')->with('success', 'Livraison créée avec succès.');
}


    // Afficher les détails d'une livraison
    public function details($id)
    {
        $delivery = Delivery::with('product')->findOrFail($id);
        return view('admin.delivery.details', compact('delivery'));
    }

    // Afficher le formulaire d'édition
    public function edit($id)
    {
        $delivery = Delivery::findOrFail($id);
        $products = Product::all();
        return view('admin.delivery.edit', compact('delivery', 'products'));
    }

    // Mettre à jour une livraison
    public function update(Request $request)
    {
        $delivery = Delivery::findOrFail($request->id);

        $request->validate([
            'invoice_number' => "required|unique:deliveries,invoice_number,{$request->id}",
            'customer_name' => 'required',
            'customer_address' => 'required',
            'delivery_status' => 'required|in:pending,delivered',
            'quantity' => 'required|numeric|min:1',
            'delivery_date' => 'required|date',
            'product_id' => 'required|exists:products,id',
            'image' => 'nullable|image|max:2048'
        ]);

        $data = $request->all();

        if ($request->hasFile('image')) {
            Storage::disk('public')->delete($delivery->image);
            $data['image'] = $request->file('image')->store('deliveries', 'public');
        }

        $delivery->update($data);

        return redirect()->route('deliveryList')->with('success', 'Livraison mise à jour avec succès.');
    }

    // Supprimer une livraison
    public function delete($id)
    {
        $delivery = Delivery::findOrFail($id);
        
        if ($delivery->image) {
            Storage::disk('public')->delete($delivery->image);
        }

        $delivery->delete();

        return back()->with('success', 'Livraison supprimée avec succès.');
    }
}
