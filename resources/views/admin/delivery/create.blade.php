@extends('admin.layouts.master')

@section('content')
<div class="card shadow col-md-8 mx-auto">
    <div class="card-header text-white" style = "background-color:#6c757d; margin-left: -12px; margin-right: -12px;">
        <h5 class="m-0">Nouvelle Livraison</h5>
    </div>
    <form action="{{ route('delivery#Create') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="card-body" style = "background-color:#6c757d; margin-left: -12px; margin-right: -12px;">
            <!-- Numéro de facture -->
            <div class="mb-3">
                <label for="invoice_number" class="form-label">Numéro de facture</label>
                <input type="text" name="invoice_number" 
                       class="form-control @error('invoice_number') is-invalid @enderror"
                       value="{{ old('invoice_number') }}" required>
                @error('invoice_number')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Produit associé -->
            <div class="mb-3">
                <label for="product_id" class="form-label">Produit</label>
                <select name="product_id" 
                        class="form-control @error('product_id') is-invalid @enderror" required>
                    <option value="">Sélectionnez un produit</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                            {{ $product->name }}
                        </option>
                    @endforeach
                </select>
                @error('product_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Image -->
            <div class="mb-3">
                <label for="image" class="form-label">Image du produit à livré</label>
                <input type="file" name="image" 
                       class="form-control @error('image') is-invalid @enderror"
                       accept="image/*">
                @error('image')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <!-- Client -->
            <div class="mb-3">
                <label for="customer_name" class="form-label">Nom du client</label>
                <input type="text" name="customer_name" 
                       class="form-control @error('customer_name') is-invalid @enderror"
                       value="{{ old('customer_name') }}" required>
                @error('customer_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Adresse -->
            <div class="mb-3">
                <label for="customer_address" class="form-label">Adresse du client</label>
                <input type="text" name="customer_address" 
                       class="form-control @error('customer_address') is-invalid @enderror"
                       value="{{ old('customer_address') }}" required>
                @error('customer_address')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>


            <!-- Quantité -->
            <div class="mb-3">
                <label for="quantity" class="form-label">Quantité</label>
                <input type="number" name="quantity" 
                       class="form-control @error('quantity') is-invalid @enderror"
                       value="{{ old('quantity') }}" min="1" required>
                @error('quantity')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Date de livraison -->
            <div class="mb-3">
                <label for="delivery_date" class="form-label">Date de livraison</label>
                <input type="date" name="delivery_date" 
                       class="form-control @error('delivery_date') is-invalid @enderror"
                       value="{{ old('delivery_date') }}" required>
                @error('delivery_date')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="row mt-4">
                <!-- Bouton Enregistrer (orange) -->
                <div class="col-6">
                    <button type="submit"
                            class="btn w-100 text-white"
                            style="
                                background-color: rgb(144, 142, 140);
                                border: none;">
                        Enregistrer
                    </button>
                </div>

                <!-- Bouton Retour (rouge) -->
                <div class="col-6">
                    <a href="{{ route('deliveryList') }}"
                    class="btn w-100 text-white text-center"
                    style="
                        background-color: rgb(144, 142, 140);
                        border: none;">
                        Retour
                    </a>
                </div>
            </div>

        </div>
    </form>
</div>
@endsection
