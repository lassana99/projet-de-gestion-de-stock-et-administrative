@extends('admin.layouts.master')

@section('content')

<div class="container-fluid">
    <div class="card shadow mb-4" style="background-color:#6c757d; border: 1px solid rgba(255,255,255,0.1);">
        {{-- Card Header Stylisé --}}
        <div class="card-header py-3" style="background-color:#6c757d; border-bottom: 1px solid rgba(255,255,255,0.2);">
            <h5 class="m-0 font-weight-bold text-white">
                <i class="fas fa-edit"></i> Modifier le Bordereau de Livraison N° {{ $deliveryNote->delivery_note_number }}
            </h5>
            <p class="text-white mt-1 mb-0 opacity-75">Modification des informations de livraison et des quantités articles.</p>
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

            <form action="{{ route('delivery_notes.update', $deliveryNote->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                {{-- Identifiants fixes --}}
                <input type="hidden" name="invoice_id" value="{{ $deliveryNote->invoice_id }}">
                
                <h6 class="text-white font-weight-bold mb-3 text-uppercase small" style="letter-spacing: 1px;">1. Informations Générales du Bordereau</h6>

                <div class="row mb-4">
                    {{-- Numéro du BL --}}
                    <div class="col-md-4 mb-3">
                        <label for="delivery_note_number" class="form-label text-white fw-bold">Numéro du Bordereau</label>
                        <input type="text" name="delivery_note_number" id="delivery_note_number" 
                               class="form-control bg-light font-weight-bold" 
                               value="{{ old('delivery_note_number', $deliveryNote->delivery_note_number) }}" required>
                        @error('delivery_note_number') <small class="text-warning fw-bold">{{ $message }}</small> @enderror
                    </div>
                    
                    {{-- Date de Livraison --}}
                    <div class="col-md-4 mb-3">
                        <label for="date_delivery" class="form-label text-white fw-bold">Date de Livraison</label>
                        <input type="date" name="date_delivery" id="date_delivery" 
                               class="form-control" 
                               value="{{ old('date_delivery', $deliveryNote->date_delivery->format('Y-m-d')) }}" required>
                        @error('date_delivery') <small class="text-warning fw-bold">{{ $message }}</small> @enderror
                    </div>
                    
                    {{-- Lieu de Livraison / Adresse --}}
                    <div class="col-md-4 mb-3">
                        <label for="delivery_location" class="form-label text-white fw-bold">Lieu de livraison (Adresse)</label>
                        <input type="text" name="delivery_location" id="delivery_location" 
                               class="form-control" 
                               value="{{ old('delivery_location', $deliveryNote->delivery_location) }}">
                        @error('delivery_location') <small class="text-warning fw-bold">{{ $message }}</small> @enderror
                    </div>
                </div>

                <hr style="border-color: rgba(255,255,255,0.2);">

                {{-- Tableau des Lignes à Livrer (Modification) --}}
                <h6 class="text-white font-weight-bold mt-4 mb-3 text-uppercase small" style="letter-spacing: 1px;">2. Articles livrés (Mise à jour des quantités)</h6>
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0" style="border-color: rgba(255,255,255,0.3);">
                        <thead class="text-white" style="background-color: #5a6268;">
                            <tr class="text-center">
                                <th style="width: 15%;">Référence</th>
                                <th style="width: 30%;">Désignation du Produit</th>
                                <th style="width: 12%;">Qté Cmd.</th>
                                <th style="width: 13%;">Qté Livrée</th>
                                <th style="width: 30%;">Observations</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($deliveryNote->lines as $line)
                            <tr class="text-white" style="background-color: #7b838c; vertical-align: middle;">
                                <td class="text-center">
                                    {{ $line->reference ?? 'N/A' }}
                                    {{-- Champ caché pour transmettre l'ID de la ligne au contrôleur --}}
                                    <input type="hidden" name="line_ids[]" value="{{ $line->id }}">
                                </td>
                                <td class="font-weight-bold">{{ $line->product_name }}</td>
                                <td class="text-center">
                                    <span class="badge badge-light px-3 py-2" style="font-size: 0.9rem;">{{ $line->quantity_ordered }}</span>
                                </td>
                                <td>
                                    {{-- Ajustement de la quantité livrée --}}
                                    <input type="number" name="line_quantities[{{ $line->id }}]" 
                                            class="form-control form-control-sm text-center font-weight-bold" 
                                            value="{{ old('line_quantities.' . $line->id, $line->quantity_delivered) }}" 
                                            min="0" max="{{ $line->quantity_ordered }}" required>
                                    <small class="text-white-50 d-block text-center mt-1">Max: {{ $line->quantity_ordered }}</small>
                                </td>
                                <td>
                                    {{-- Observation par ligne --}}
                                    <input type="text" name="line_observations[{{ $line->id }}]" 
                                            class="form-control form-control-sm" 
                                            value="{{ old('line_observations.' . $line->id, $line->observation) }}"
                                            placeholder="Ex: RAS, Article endommagé...">
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <hr style="border-color: rgba(255,255,255,0.2);">

                {{-- Boutons d'action --}}
                <div class="row mt-4">
                    <div class="col d-flex justify-content-between align-items-center"> 
                        
                        {{-- Bouton Sauvegarder (Orange) --}}
                        <div>
                            <button type="submit" class="btn text-white fw-bold shadow" 
                                style="background-color: rgb(249, 138, 27); border: none; padding: 10px 25px;">
                                <i class="fas fa-save"></i> Sauvegarder les Modifications
                            </button>
                        </div>
                        
                        {{-- Bouton Annuler (Gris) --}}
                        <div>
                            <a href="{{ route('delivery_notes.show', $deliveryNote->id) }}" class="btn text-white" 
                                style="background-color: rgb(144, 142, 140); border: none; padding: 10px 25px;"> 
                                <i class="fa-solid fa-xmark"></i> Annuler
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection