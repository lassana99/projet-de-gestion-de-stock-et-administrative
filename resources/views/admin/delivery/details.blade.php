@extends('admin.layouts.master')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4 col-lg-8 mx-auto">
        <div class="card-header py-3" style = "background-color:#6c757d; margin-left: -12px; margin-right: -12px;">
            <h5 class="m-0 font-weight-bold text-white text-center">Détails de la Livraison</h5>
        </div>
        <div class="card-body" style = "background-color:#6c757d; margin-left: -12px; margin-right: -12px;">
            <div class="row">
                <!-- Image de la livraison -->
                <div class="col-md-4 d-flex align-items-center justify-content-center mb-3 mb-md-0">
                    @if($delivery->image)
                        <img class="img-thumbnail shadow" src="{{ asset('storage/' . $delivery->image) }}" alt="Image livraison" style="max-width: 220px; max-height: 220px; object-fit: cover;">
                    @else
                        <div class="border rounded p-4 text-center bg-light text-dark" style="width: 220px; height: 220px; display: flex; align-items: center; justify-content: center;">
                            Pas d'image
                        </div>
                    @endif
                </div>
                <!-- Infos livraison -->
                <div class="col-md-8">
                    <div class="row mb-3">
                        <div class="col-md-6 mb-2">
                            <label class="form-label fw-bold text-white">Numéro de la facture</label>
                            <div class="border rounded p-2 bg-light text-dark">{{ $delivery->invoice_number }}</div>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="form-label fw-bold text-white">Client</label>
                            <div class="border rounded p-2 bg-light text-dark">{{ $delivery->customer_name }}</div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6 mb-2">
                            <label class="form-label fw-bold text-white">Adresse</label>
                            <div class="border rounded p-2 bg-light text-dark">{{ $delivery->customer_address }}</div>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="form-label fw-bold text-white">Produit</label>
                            <div class="border rounded p-2 bg-light text-dark">
                                {{ $delivery->product ? $delivery->product->name : 'Non spécifié' }}
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 mb-2">
                            <label class="form-label fw-bold text-white">Quantité</label>
                            <div class="border rounded p-2 bg-light text-dark">{{ $delivery->quantity }}</div>
                        </div>
                        <div class="col-md-4 mb-2">
                            <label class="form-label fw-bold text-white">Date de Livraison</label>
                            <div class="border rounded p-2 bg-light text-dark">
                                {{ $delivery->delivery_date ? $delivery->delivery_date->format('d/m/Y') : 'Non spécifiée' }}
                            </div>
                        </div>
                        <div class="col-md-4 mb-2">
                            <label class="form-label fw-bold text-white">Statut</label>
                            <div class="border rounded p-2 bg-light">
                                <span class="badge {{ $delivery->delivery_status == 'delivered' ? 'bg-success' : 'bg-secondary' }}">
                                    {{ ucfirst($delivery->delivery_status) }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <a href="{{ route('deliveryList') }}"
                               class="btn w-100 text-white mt-2"
                               style="background-color: rgb(144, 142, 140); border: none;">
                                Retour à la liste
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> 
</div>
@endsection
