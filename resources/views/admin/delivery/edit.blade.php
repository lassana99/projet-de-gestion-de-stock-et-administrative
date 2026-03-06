@extends('admin.layouts.master')

@section('content')
<div class="card shadow col-md-8 mx-auto">
    <div class="card-header" style="background-color:#6c757d; margin-left: -12px; margin-right: -12px;">
        <h5 class="m-0 font-weight-bold" style="color: rgb(255, 193, 7);">Modifier la Livraison</h5>
    </div>
    <form action="{{ route('deliveryUpdate', $delivery->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="card-body" style="background-color:#6c757d; margin-left: -12px; margin-right: -12px;">
            <!-- Champ caché pour l'ID -->
            <input type="hidden" name="id" value="{{ $delivery->id }}">

            <!-- Numéro de facture -->
            <div class="mb-3">
                <label>Numéro de facture</label>
                <input type="text" name="invoice_number" 
                       class="form-control @error('invoice_number') is-invalid @enderror" 
                       value="{{ old('invoice_number', $delivery->invoice_number) }}" required>
                @error('invoice_number')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Client -->
            <div class="mb-3">
                <label>Nom du client</label>
                <input type="text" name="customer_name" 
                       class="form-control @error('customer_name') is-invalid @enderror" 
                       value="{{ old('customer_name', $delivery->customer_name) }}" required>
                @error('customer_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Adresse -->
            <div class="mb-3">
                <label>Adresse du client</label>
                <input type="text" name="customer_address" 
                       class="form-control @error('customer_address') is-invalid @enderror" 
                       value="{{ old('customer_address', $delivery->customer_address) }}" required>
                @error('customer_address')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Statut de livraison -->
            <div class="mb-3">
                <label>Statut</label>
                <select name="delivery_status" class="form-control @error('delivery_status') is-invalid @enderror" required>
                    <option value="pending" {{ $delivery->delivery_status == 'pending' ? 'selected' : '' }}>En attente</option>
                    <option value="delivered" {{ $delivery->delivery_status == 'delivered' ? 'selected' : '' }}>Livré</option>
                </select>
                @error('delivery_status')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Quantité -->
            <div class="mb-3">
                <label>Quantité</label>
                <input type="number" name="quantity" 
                       class="form-control @error('quantity') is-invalid @enderror" 
                       value="{{ old('quantity', $delivery->quantity) }}" min="1" required>
                @error('quantity')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Date de livraison -->
            <div class="mb-3">
                <label>Date de livraison</label>
                <input type="date" name="delivery_date" 
                       class="form-control @error('delivery_date') is-invalid @enderror" 
                       value="{{ old('delivery_date', $delivery->delivery_date->format('Y-m-d')) }}" required>
                @error('delivery_date')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Produit -->
            <div class="mb-3">
                <label>Produit</label>
                <select name="product_id" class="form-control @error('product_id') is-invalid @enderror" required>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" 
                            {{ $delivery->product_id == $product->id ? 'selected' : '' }}>
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
                <label>Image actuelle</label>
                @if($delivery->image)
                    <img src="{{ asset('storage/'.$delivery->image) }}" width="100" class="d-block mb-2">
                @endif
                <input type="file" name="image" class="form-control @error('image') is-invalid @enderror">
                @error('image')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="row mt-4">
                <!-- Bouton Enregistrer (orange) -->
                <div class="col-6">
                    <button type="submit"
                            class="btn w-100 text-white"
                            style="background-color: rgb(144, 142, 140); border: none;">
                        Enregistrer
                    </button>
                </div>
                <!-- Bouton Retour (rouge) -->
                <div class="col-6">
                    <a href="{{ route('deliveryList') }}"
                    class="btn w-100 text-white text-center"
                    style="background-color: rgb(144, 142, 140); border: none;">
                        Retour
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
