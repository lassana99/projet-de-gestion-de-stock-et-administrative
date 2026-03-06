@extends('admin.layouts.master')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4 col-lg-9 mx-auto">
        <div class="card-header py-3" style="background-color:#6c757d; margin-left: -12px; margin-right: -12px;">
            <h5 class="m-0 font-weight-bold text-white text-center">Détails du produit</h5>
        </div>
        <div class="card-body" style="background-color:#6c757d; margin-left: -12px; margin-right: -12px;">
            <div class="row">
                <!-- Image du produit -->
                <div class="col-md-4 d-flex align-items-start justify-content-center mb-3 mb-md-0">
                    <img class="img-thumbnail shadow" src="{{ asset('purchaseImages/' . $data->image) }}" alt="Image produit" style="max-width: 280px; max-height: 280px; object-fit: cover; border: 2px solid white;">
                </div>
                
                <!-- Infos produit -->
                <div class="col-md-8">
                    <div class="row mb-3">
                        <div class="col-md-6 mb-2">
                            <label class="form-label fw-bold text-white">Référence</label>
                            <div class="border rounded p-2 bg-light text-dark font-weight-bold">{{ $data->reference }}</div>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="form-label fw-bold text-white">Fournisseur</label>
                            <div class="border rounded p-2 bg-light text-dark">{{ $data->suppliername }}</div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12 mb-2">
                            <label class="form-label fw-bold text-white">Nom du produit</label>
                            <div class="border rounded p-2 bg-light text-dark font-weight-bold">{{ $data->name }}</div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4 mb-2">
                            <label class="form-label fw-bold text-white">Marque</label>
                            <div class="border rounded p-2 bg-light text-dark">{{ $data->brand }}</div>
                        </div>
                        <div class="col-md-4 mb-2">
                            <label class="form-label fw-bold text-white">État</label>
                            <div class="border rounded p-2 bg-light text-dark">{{ $data->status }}</div>
                        </div>
                        <div class="col-md-4 mb-2">
                            <label class="form-label fw-bold text-white">Quantité en stock</label>
                            <div class="border rounded p-2 bg-light text-dark font-weight-bold">{{ $data->count }}</div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6 mb-2">
                            <label class="form-label fw-bold text-white">Prix de vente</label>
                            <div class="border rounded p-2 bg-light text-dark font-weight-bold text-primary">
                                {{ number_format($data->price, 0, ',', ' ') }} FCFA
                            </div>
                        </div>
                        {{-- NOUVEAU CHAMP : Date du produit --}}
                        <div class="col-md-6 mb-2">
                            <label class="form-label fw-bold text-white">Date du produit</label>
                            <div class="border rounded p-2 bg-light text-dark font-weight-bold">
                                <i class="fas fa-calendar-day mr-1 text-secondary"></i> {{ $data->date_product ? $data->date_product->format('d/m/Y') : '-' }}
                            </div>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="mb-3">
                        <label class="form-label fw-bold text-white">Description</label>
                        <div class="border rounded p-2 bg-light text-dark" style="min-height: 80px; white-space: pre-line;">{{ $data->description ?? '-' }}</div>
                    </div>

                    <!-- Bouton Retour -->
                    <div class="row mt-4">
                        <div class="col-6">
                            <a href="{{ route('productList') }}" class="btn w-100 text-white shadow-sm" style="background-color: rgb(144, 142, 140); border: 1px solid white;">
                                <i class="fas fa-arrow-left mr-2"></i>Retour à la liste
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection