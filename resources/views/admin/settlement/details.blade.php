@extends('admin.layouts.master')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4" style="background-color:#6c757d; border: 1px solid white;">
        <div class="card-header py-3" style="background-color:#6c757d; border-bottom: 1px solid white;">
            <h5 class="m-0 font-weight-bold text-white">Détails de l'opération</h5>
        </div>
        <div class="card-body" style="background-color:#6c757d;">
            <div class="row">
                {{-- Informations de l'Entité --}}
                <div class="col-md-6 mb-4">
                    <h6 class="text-warning font-weight-bold text-uppercase">Informations générales</h6>
                    <hr style="border-color: white;">
                    <p class="text-white"><strong>Type :</strong> 
                        <span class="badge {{ $settlement->type == 'debt' ? 'bg-primary' : 'bg-danger' }}">
                            {{ $settlement->type == 'debt' ? 'Créance (Le client nous doit)' : 'Dette (Nous devons au fournisseur)' }}
                        </span>
                    </p>
                    <p class="text-white"><strong>Nom :</strong> {{ $settlement->entity_name }}</p>
                    <p class="text-white"><strong>Téléphone :</strong> {{ $settlement->phone ?? 'N/A' }}</p>
                    <p class="text-white"><strong>Email :</strong> {{ $settlement->email ?? 'N/A' }}</p>
                    <p class="text-white"><strong>Adresse :</strong> {{ $settlement->address ?? 'N/A' }}</p>
                </div>

                {{-- Informations Financières et Alerte --}}
                <div class="col-md-6 mb-4">
                    <h6 class="text-warning font-weight-bold text-uppercase">Informations financières</h6>
                    <hr style="border-color: white;">
                    <p class="text-white"><strong>Montant à régler :</strong> 
                        <span class="badge bg-light text-dark" style="font-size: 1.1rem;">
                            {{ number_format($settlement->amount, 0, '', ' ') }} {{ $settlement->currency }}
                        </span>
                    </p>

                    {{-- Affichage du montant converti en FCFA uniquement si la devise est étrangère --}}
                    @if($settlement->currency !== 'FCFA')
                        <p class="text-white"><strong>Contre-valeur en FCFA :</strong> 
                            <span class="badge bg-info text-white" style="font-size: 1rem;">
                                {{ number_format($settlement->amount_fcfa, 0, '', ' ') }} FCFA
                            </span>
                        </p>
                    @endif

                    <p class="text-white"><strong>Date d'émission :</strong> {{ $settlement->issue_date->format('d/m/Y') }}</p>
                    <p class="text-white"><strong>Date d'échéance :</strong> {{ $settlement->due_date->format('d/m/Y') }}</p>
                    
                    @php
                        $days = (int) now()->startOfDay()->diffInDays($settlement->due_date->startOfDay(), false);
                    @endphp

                    <p class="text-white"><strong>Statut de l'alerte :</strong> 
                        @if($settlement->status == 'paid')
                            <span class="badge bg-success">Réglé (Alerte désactivée)</span>
                        @else
                            @if($days < 0)
                                <span class="badge bg-danger">En retard de {{ abs($days) }} jour(s)</span>
                            @elseif($days <= 5)
                                <span class="badge bg-warning text-dark">Échéance dans {{ $days }} jour(s) (Alerte active)</span>
                            @else
                                <span class="badge bg-secondary">Échéance lointaine ({{ $days }} jours)</span>
                            @endif
                        @endif
                    </p>
                </div>
            </div>

            {{-- SECTION BOUTONS --}}
            <div class="mt-4 d-flex justify-content-between align-items-center">
                <div class="d-flex gap-2">
                    {{-- Bouton Modifier (Orange) --}}
                    <a href="{{ route('settlementEdit', $settlement->id) }}" class="btn text-white shadow-sm" style="background-color: rgb(249, 138, 27); border: none; padding: 10px 25px;">
                        <i class="fa-solid fa-pen-to-square mr-1"></i> Modifier
                    </a>
                </div>

                {{-- Bouton Retour (Gris) --}}
                <a href="{{ route('settlementList') }}" class="btn text-white shadow-sm" style="background-color: rgb(144, 142, 140); border: none; padding: 10px 25px;">
                    <i class="fa-solid fa-arrow-left mr-1"></i> Retour à la liste
                </a>
            </div>
        </div>
    </div>
</div>
@endsection