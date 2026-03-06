@extends('admin.layouts.master')

@section('content')

<div class="container-fluid">
    <div class="card shadow mb-4" style="background-color:#6c757d; border: 1px solid rgba(255,255,255,0.1);">
        {{-- Card Header Stylisé --}}
        <div class="card-header py-3" style="background-color:#6c757d; border-bottom: 1px solid rgba(255,255,255,0.2);">
            <h5 class="m-0 font-weight-bold text-white"><i class="fa-solid fa-truck-ramp-box"></i> Créer un Bordereau de Livraison</h5>
            <p class="text-white mt-1 mb-0 opacity-75">Référence : Facture N° **{{ $invoice->invoice_number }}** | Client : **{{ $invoice->client }}**</p>
        </div>

        {{-- Card Body Stylisé --}}
        <div class="card-body" style="background-color:#6c757d;">
            
            @if ($errors->any())
                <div class="alert alert-danger shadow">
                    <h6 class="font-weight-bold"><i class="fa-solid fa-circle-exclamation"></i> Erreurs de validation :</h6>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('delivery_notes.store') }}" method="POST">
                @csrf
                
                {{-- Champs Cachés (Données héritées de la Facture) --}}
                <input type="hidden" name="invoice_id" value="{{ $invoice->id }}">
                <input type="hidden" name="client_name" value="{{ $invoice->client }}">
                <input type="hidden" name="client_address" value="{{ $invoice->client_address }}">
                <input type="hidden" name="code_client" value="{{ $invoice->code_client }}">
                
                {{-- Récupération du N° de Bon de Commande depuis l'URL (Étape 1) --}}
                <input type="hidden" name="purchase_order_number" 
                       value="{{ request()->query('purchase_order_number') ?? $invoice->purchase_order_number }}">
                
                <h6 class="text-white font-weight-bold mb-3 text-uppercase small" style="letter-spacing: 1px;">1. Informations Générales</h6>

                <div class="row mb-4">
                    {{-- Numéro du BL (Généré par le controller) --}}
                    <div class="col-md-4 mb-3">
                        <label for="delivery_note_number" class="form-label text-white fw-bold">Numéro du Bordereau</label>
                        <input type="text" name="delivery_note_number" id="delivery_note_number" 
                               class="form-control bg-light font-weight-bold" 
                               value="{{ old('delivery_note_number', $deliveryNoteNumber) }}" required>
                        @error('delivery_note_number') <small class="text-warning fw-bold">{{ $message }}</small> @enderror
                    </div>
                    
                    {{-- Date de Livraison (Héritée de l'Étape 1 ou Aujourd'hui) --}}
                    <div class="col-md-4 mb-3">
                        <label for="date_delivery" class="form-label text-white fw-bold">Date de Livraison</label>
                        <input type="date" name="date_delivery" id="date_delivery" 
                               class="form-control" 
                               value="{{ old('date_delivery', request()->query('date_delivery') ?? now()->format('Y-m-d')) }}" required>
                        @error('date_delivery') <small class="text-warning fw-bold">{{ $message }}</small> @enderror
                    </div>
                    
                    {{-- Lieu de Livraison / Adresse --}}
                    <div class="col-md-4 mb-3">
                        <label for="delivery_location" class="form-label text-white fw-bold">Lieu de livraison (Adresse)</label>
                        <input type="text" name="delivery_location" id="delivery_location" 
                               class="form-control" placeholder="Ex: Entrepôt client..."
                               value="{{ old('delivery_location', $invoice->client_address) }}">
                        @error('delivery_location') <small class="text-warning fw-bold">{{ $message }}</small> @enderror
                    </div>
                </div>

                <hr style="border-color: rgba(255,255,255,0.2);">

                {{-- Tableau des Lignes à Livrer --}}
                <h6 class="text-white font-weight-bold mt-4 mb-3 text-uppercase small" style="letter-spacing: 1px;">2. Articles et Quantités</h6>
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0" style="border-color: rgba(255,255,255,0.3);">
                        <thead class="text-white" style="background-color: #5a6268;">
                            <tr class="text-center">
                                <th style="width: 15%;">Référence</th>
                                <th style="width: 30%;">Désignation du Produit</th>
                                <th style="width: 12%;">Qté Cmd.</th>
                                <th style="width: 13%;">Qté à Livrer</th>
                                <th style="width: 30%;">Observations</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($invoice->lines as $line)
                            <tr class="text-white" style="background-color: #7b838c; vertical-align: middle;">
                                <td class="text-center">{{ $line->reference ?? 'N/A' }}</td>
                                <td class="font-weight-bold">{{ $line->product_name }}</td>
                                <td class="text-center">
                                    <span class="badge badge-light px-3 py-2" style="font-size: 0.9rem;">{{ $line->quantity }}</span>
                                </td>
                                <td>
                                    {{-- Ajustement de la quantité livrée --}}
                                    <input type="number" name="line_quantities[{{ $line->id }}]" 
                                            class="form-control form-control-sm text-center font-weight-bold" 
                                            value="{{ old('line_quantities.' . $line->id, $line->quantity) }}" 
                                            min="0" max="{{ $line->quantity }}" required>
                                    <small class="text-white-50 d-block text-center mt-1">Max: {{ $line->quantity }}</small>
                                </td>
                                <td>
                                    {{-- Observation par ligne --}}
                                    <input type="text" name="line_observations[{{ $line->id }}]" 
                                            class="form-control form-control-sm" 
                                            value="{{ old('line_observations.' . $line->id) }}"
                                            placeholder="Ex: Livraison partielle, RAS...">
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="alert alert-info mt-3 bg-dark text-white border-0 shadow-sm" style="opacity: 0.8;">
                    <i class="fa-solid fa-circle-info"></i> Par défaut, les quantités livrées correspondent aux quantités commandées. Vous pouvez les réduire en cas de livraison partielle.
                </div>

                <hr style="border-color: rgba(255,255,255,0.2);">

                {{-- Boutons d'action --}}
                <div class="row mt-4">
                    <div class="col d-flex justify-content-between align-items-center"> 
                        
                        {{-- Bouton "Générer" --}}
                        <div>
                            <button type="submit" class="btn text-white fw-bold shadow" 
                                style="background-color: rgb(249, 138, 27); border: none; padding: 10px 25px;">
                                <i class="fas fa-file-signature"></i> Générer et Enregistrer le Bordereau
                            </button>
                        </div>
                        
                        {{-- Bouton "Annuler" --}}
                        <div>
                            <a href="{{ route('delivery_notes.create') }}" class="btn text-white" 
                                style="background-color: rgb(144, 142, 140); border: none; padding: 10px 25px;"> 
                                <i class="fa-solid fa-rotate-left"></i> Retour
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection