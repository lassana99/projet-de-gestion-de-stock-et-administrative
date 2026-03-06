@extends('admin.layouts.master')

@section('content')

<div class="container-fluid">
    <div class="card shadow mb-4">
        {{-- Card Header - Harmonisé avec le style Devis --}}
        <div class="card-header py-3" style="background-color:#6c757d; border-bottom: 1px solid white;">
            <h5 class="m-0 font-weight-bold text-white">Créer une Facture à partir d'un Devis approuvé</h5>
        </div>

        {{-- Card Body --}}
        <div class="card-body" style="background-color:#6c757d;">

            @if ($errors->any())
                <div class="alert alert-danger">
                    <strong>Erreurs de validation:</strong>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Le formulaire soumet les informations de base pour générer la facture --}}
            <form action="{{ route('invoices.store') }}" method="POST" id="invoice-form">
                @csrf

                <div class="row">
                    {{-- 1. Sélection du Devis --}}
                    <div class="col-md-4 mb-3">
                        <label for="devis_id" class="form-label text-white">Sélectionner le Devis approuvé <span class="text-danger">*</span></label>
                        <select class="form-control" id="devis_id" name="devis_id" required>
                            <option value="" disabled selected>Choisir un numéro de devis</option>
                            @foreach($validDevis as $devis) 
                                <option 
                                    value="{{ $devis->id }}" 
                                    data-client="{{ $devis->client }}"
                                    {{ old('devis_id') == $devis->id ? 'selected' : '' }}>
                                    {{ $devis->devis_number }} ({{ $devis->client }})
                                </option>
                            @endforeach
                        </select>
                        @if ($validDevis->isEmpty())
                            <div class="alert alert-warning mt-2 py-2">
                                <small><i class="fa fa-exclamation-triangle"></i> Aucun devis validé et non facturé disponible.</small>
                            </div>
                        @endif
                    </div>

                    {{-- 2. Date de la Facture --}}
                    <div class="col-md-4 mb-3">
                        <label for="date_invoice" class="form-label text-white">Date de la facture <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="date_invoice" name="date_invoice" value="{{ old('date_invoice', now()->format('Y-m-d')) }}" required>
                    </div>
                    
                    {{-- 3. État de la Facture (Synchronisé avec le contrôleur) --}}
                    <div class="col-md-4 mb-3">
                        <label for="status" class="form-label text-white">État initial de la facture <span class="text-danger">*</span></label>
                        <select class="form-control" id="status" name="status" required>
                            <option value="pending" {{ old('status', 'pending') == 'pending' ? 'selected' : '' }}>En attente</option>
                            <option value="sent" {{ old('status') == 'sent' ? 'selected' : '' }}>Envoyée</option>
                            <option value="paid" {{ old('status') == 'paid' ? 'selected' : '' }}>Payée</option>
                        </select>
                    </div>
                </div>
                
                <hr style="border-color: #ffffff;">

                {{-- Note informative pour l'utilisateur --}}
                <div class="row mb-4">
                    <div class="col-12">
                        <p class="text-white-50">
                            <i class="fa fa-info-circle"></i> 
                            En créant cette facture, tous les articles, les références, les images et les conditions du devis sélectionné seront automatiquement importés.
                        </p>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col d-flex justify-content-between"> 
                        
                        {{-- Bouton "Créer la Facture" --}}
                        <button type="submit" class="btn text-white fw-bold shadow-sm" 
                            {{ $validDevis->isEmpty() ? 'disabled' : '' }}
                            style="background-color: rgb(249, 138, 27); border: none;">
                            <i class="fa fa-file-invoice"></i> Générer la Facture
                        </button>
                        
                        {{-- Bouton "Annuler" --}}
                        <a href="{{ route('invoices.list') }}" class="btn text-white" 
                            style="background-color: rgb(144, 142, 140); border: none;">
                            Annuler
                        </a>

                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection