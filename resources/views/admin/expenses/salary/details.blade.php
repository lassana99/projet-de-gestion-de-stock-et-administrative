@extends('admin.layouts.master')
@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4 col-lg-10 mx-auto">
        <div class="card-header py-3" style="background-color:#6c757d; margin: 0 -12px;">
            <h5 class="m-0 font-weight-bold text-white text-center">Fiche Détail Salaire : {{ $salary->number }}</h5>
        </div>
        <div class="card-body" style="background-color:#6c757d; margin: 0 -12px;">
            <div class="row">
                {{-- Colonne 1 --}}
                <div class="col-md-6">
                    <div class="mb-4">
                        <label class="fw-bold text-white">Bénéficiaire</label>
                        <div class="bg-light text-dark p-2 rounded fw-bold">{{ $salary->full_name }}</div>
                    </div>
                    <div class="mb-4">
                        <label class="fw-bold text-white">Fonction / Service</label>
                        <div class="bg-light text-dark p-2 rounded">{{ $salary->position }}</div>
                    </div>
                    <div class="mb-4">
                        <label class="fw-bold text-white">Identité</label>
                        <div class="bg-light text-dark p-2 rounded">{{ $salary->getRealIdType() }} - N° {{ $salary->id_number }}</div>
                    </div>
                </div>
                {{-- Colonne 2 --}}
                <div class="col-md-6">
                    <div class="mb-4">
                        <label class="fw-bold text-white">Montant Total</label>
                        <div class="bg-light text-dark p-2 rounded fw-bold">{{ $salary->formattedAmount() }} FCFA</div>
                    </div>
                    <div class="mb-4">
                        <label class="fw-bold text-white">Date de paiement</label>
                        <div class="bg-light text-dark p-2 rounded">{{ $salary->payment_date->format('d/m/Y') }}</div>
                    </div>
                    <div class="mb-4">
                        <label class="fw-bold text-white">Modalité</label>
                        <div class="bg-light text-dark p-2 rounded">{{ $salary->payment_mode }}</div>
                    </div>
                </div>
                {{-- Bloc Large --}}
                <div class="col-12 mb-4">
                    <label class="fw-bold text-white">Détails complémentaires</label>
                    <div class="bg-light text-dark p-2 rounded" style="min-height: 60px;">{{ $salary->additional_details ?? 'Aucun détail supplémentaire' }}</div>
                </div>
            </div>
            
            <div class="row mt-4">
                <div class="col-md-6 mb-2"><a href="{{ route('salaryEdit', $salary->id) }}" class="btn w-100 text-white fw-bold shadow-sm" style="background-color: rgb(249, 138, 27); border: none;">Modifier</a></div>
                <div class="col-md-6 mb-2"><a href="{{ route('salaryList') }}" class="btn w-100 text-white fw-bold shadow-sm" style="background-color: rgb(144, 142, 140); border: none;">Retour</a></div>
            </div>
        </div>
    </div>
</div>
@endsection