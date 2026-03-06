@extends('admin.layouts.master')

@section('content')
<div class="container-fluid">
    {{-- On centre la fiche pour une meilleure présentation --}}
    <div class="card shadow mb-4 col-lg-10 mx-auto">
        
        {{-- En-tête de la fiche --}}
        <div class="card-header py-3" style="background-color:#6c757d; margin: 0 -12px;">
            <h5 class="m-0 font-weight-bold text-white text-center">
                <i class="fa-solid fa-circle-info me-2"></i> Fiche Détail Dépense : {{ $expense->number }}
            </h5>
        </div>

        <div class="card-body" style="background-color:#6c757d; margin: 0 -12px;">
            <div class="row">
                
                {{-- Informations principales (Bénéficiaire, Montant, Date) --}}
                <div class="col-md-4 mb-4">
                    <label class="fw-bold text-white">Bénéficiaire</label>
                    <div class="bg-light text-dark p-2 rounded fw-bold">
                        {{ $expense->full_name }}
                    </div>
                </div>

                <div class="col-md-4 mb-4">
                    <label class="fw-bold text-white">Montant</label>
                    <div class="bg-light text-dark p-2 rounded fw-bold">
                        {{ $expense->formattedAmount() }} FCFA
                    </div>
                </div>

                <div class="col-md-4 mb-4">
                    <label class="fw-bold text-white">Date d'émission</label>
                    <div class="bg-light text-dark p-2 rounded">
                        {{ $expense->date->format('d/m/Y') }}
                    </div>
                </div>

                {{-- Paiement (Motif, Modalité) --}}
                <div class="col-md-6 mb-4">
                    <label class="fw-bold text-white">Motif du Paiement</label>
                    <div class="bg-light text-dark p-2 rounded">
                        {{ $expense->getRealReason() }}
                    </div>
                </div>

                <div class="col-md-6 mb-4">
                    <label class="fw-bold text-white">Modalité de Payement</label>
                    <div class="bg-light text-dark p-2 rounded">
                        {{ $expense->payment_mode }}
                    </div>
                </div>

                {{-- Désignation (Grand bloc) --}}
                <div class="col-md-12 mb-4">
                    <label class="fw-bold text-white">Désignation</label>
                    <div class="bg-light text-dark p-2 rounded" style="min-height: 80px; white-space: pre-wrap;">{{ $expense->designation }}</div>
                </div>

                {{-- Détails complémentaires (Grand bloc) --}}
                <div class="col-md-12 mb-4">
                    <label class="fw-bold text-white">Détails complémentaires</label>
                    <div class="bg-light text-dark p-2 rounded" style="min-height: 80px; white-space: pre-wrap;">{{ $expense->additional_details ?? 'Aucun détail supplémentaire' }}</div>
                </div>
            </div>

            <hr style="border-top: 1px solid rgba(255,255,255,0.2);">

            {{-- Zone d'actions --}}
            <div class="row mt-4">
                <div class="col-md-6 mb-2">
                    <a href="{{ route('otherExpenseEdit', $expense->id) }}" class="btn w-100 text-white fw-bold shadow-sm" style="background-color: rgb(249, 138, 27); border: none;">
                        <i class="fa-solid fa-pen-to-square me-1"></i> Modifier ces informations
                    </a>
                </div>
                <div class="col-md-6 mb-2">
                    <a href="{{ route('otherExpenseList') }}" class="btn w-100 text-white fw-bold shadow-sm" style="background-color: rgb(144, 142, 140); border: none;">
                        <i class="fa-solid fa-arrow-left me-1"></i> Retour à la liste
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection