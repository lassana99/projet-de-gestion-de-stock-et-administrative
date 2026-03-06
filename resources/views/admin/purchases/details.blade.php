@extends('admin.layouts.master')

@section('content')
@php
    function formatValue($value) {
        if ($value === null) return '-';
        return ($value > 1) ? number_format(round($value), 0, ',', ' ') : number_format($value, 2, ',', ' ');
    }
@endphp

<div class="container-fluid">
    <div class="card shadow mb-4 col-lg-10 mx-auto">
        <div class="card-header py-3" style="background-color:#6c757d; margin-left: -12px; margin-right: -12px;">
            <h5 class="m-0 font-weight-bold text-white text-center">Détails de l'achat #{{ $purchase->id }}</h5>
        </div>
        <div class="card-body" style="background-color:#6c757d; margin-left: -12px; margin-right: -12px;">
            <div class="row">
                <!-- Image -->
                <div class="col-md-4 d-flex align-items-start justify-content-center mb-3 mb-md-0">
                    @if($purchase->purchaseimage)
                        <img class="img-thumbnail shadow" src="{{ asset('purchaseImages/' . $purchase->purchaseimage) }}" alt="Image achat" style="max-width: 100%; height: auto; object-fit: cover; border: 2px solid white;">
                    @else
                        <img class="img-thumbnail shadow" src="{{ asset('defaultImg/default.jpg') }}" alt="Image achat" style="max-width: 100%; height: auto; object-fit: cover; border: 2px solid white;">
                    @endif
                </div>
                
                <!-- Info -->
                <div class="col-md-8">
                    <div class="row mb-3">
                        <div class="col-md-6 mb-2">
                            <label class="form-label fw-bold text-white">Référence</label>
                            <div class="border rounded p-2 bg-light text-dark font-weight-bold">{{ $purchase->reference ?? '-' }}</div>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="form-label fw-bold text-white">Produit</label>
                            <div class="border rounded p-2 bg-light text-dark font-weight-bold">{{ $purchase->purchasename ?? '-' }}</div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-white">Description</label>
                        <div class="border rounded p-2 bg-light text-dark" style="min-height: 60px; white-space: pre-line;">{{ $purchase->description ?? '-' }}</div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6 mb-2">
                            <label class="form-label fw-bold text-white">Catégorie</label>
                            <div class="border rounded p-2 bg-light text-dark">{{ $purchase->Category ?? '-' }}</div>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="form-label fw-bold text-white">Marque</label>
                            <div class="border rounded p-2 bg-light text-dark">{{ $purchase->brand ?? '-' }}</div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6 mb-2">
                            <label class="form-label fw-bold text-white">État (Statut)</label>
                            <div class="border rounded p-2 bg-light text-dark">{{ $purchase->status ?? '-' }}</div>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="form-label fw-bold text-white">Moyen de transport</label>
                            <div class="border rounded p-2 bg-light text-dark">{{ $purchase->transport_mode ?? '-' }}</div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6 mb-2">
                            <label class="form-label fw-bold text-white">Prix d'achat unité</label>
                            <div class="border rounded p-2 bg-light text-dark font-weight-bold text-primary">{{ formatValue($purchase->purchaseprice) }} {{ $purchase->currency ?? 'FCFA' }}</div>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="form-label fw-bold text-white">Poids (KG)</label>
                            <div class="border rounded p-2 bg-light text-dark">{{ formatValue($purchase->weight ?? 0) }}</div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6 mb-2">
                            <label class="form-label fw-bold text-white">Quantité</label>
                            <div class="border rounded p-2 bg-light text-dark">{{ formatValue($purchase->quantity) }}</div>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="form-label fw-bold text-white">Fournisseur</label>
                            <div class="border rounded p-2 bg-light text-dark">{{ $purchase->suppliername ?? '-' }}</div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6 mb-2">
                            <label class="form-label fw-bold text-white">Incoterm</label>
                            <div class="border rounded p-2 bg-light text-dark">{{ $purchase->incoterm ?? '-' }}</div>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="form-label fw-bold text-white">Pays d'origine</label>
                            <div class="border rounded p-2 bg-light text-dark">{{ $purchase->country ?? '-' }}</div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        {{-- NOUVEAU CHAMP : Date d'achat --}}
                        <div class="col-md-6 mb-2">
                            <label class="form-label fw-bold text-white">Date d'achat</label>
                            <div class="border rounded p-2 bg-light text-dark font-weight-bold" style="color: #e67e22 !important;">
                                {{ $purchase->date_purchase ? $purchase->date_purchase->format('d/m/Y') : '-' }}
                            </div>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="form-label fw-bold text-white">Créé le (Système)</label>
                            <div class="border rounded p-2 bg-light text-dark" style="font-size: 0.9rem;">{{ $purchase->created_at->format('d/m/Y H:i') }}</div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6 mb-2">
                            <label class="form-label fw-bold text-white">Modifié le (Système)</label>
                            <div class="border rounded p-2 bg-light text-dark" style="font-size: 0.9rem;">{{ $purchase->updated_at->format('d/m/Y H:i') }}</div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-4">
                            <a href="{{ route('purchaseList') }}" class="btn w-100 text-white shadow-sm" style="background-color: rgb(144, 142, 140); border: 1px solid white;">
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