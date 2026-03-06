@extends('admin.layouts.master')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4 col-lg-11 mx-auto">
        {{-- En-tête --}}
        <div class="card-header py-3" style="background-color:#6c757d; margin: 0 -12px;">
            <h5 class="m-0 font-weight-bold text-white text-center">
                <i class="fa-solid fa-shield-halved me-2"></i> Fiche Détail Paiement INPS : {{ $payment->number }}
            </h5>
        </div>

        <div class="card-body" style="background-color:#6c757d; margin: 0 -12px;">
            <div class="row text-white">
                
                {{-- Informations sur la période --}}
                <div class="col-md-4 mb-4">
                    <label class="fw-bold">Période concernée</label>
                    <div class="bg-light text-dark p-2 rounded fw-bold text-center">
                        Du {{ $payment->start_date->format('d/m/Y') }} au {{ $payment->end_date->format('d/m/Y') }}
                    </div>
                </div>

                {{-- Informations financières --}}
                <div class="col-md-4 mb-4">
                    <label class="fw-bold">Montant Total</label>
                    <div class="bg-light text-dark p-2 rounded fw-bold text-center">
                        {{ $payment->formattedAmount() }} FCFA
                    </div>
                </div>

                <div class="col-md-4 mb-4">
                    <label class="fw-bold">Date et Mode de Paiement</label>
                    <div class="bg-light text-dark p-2 rounded text-center">
                        Le {{ $payment->payment_date->format('d/m/Y') }} via <strong>{{ $payment->payment_mode }}</strong>
                    </div>
                </div>

                {{-- Liste des Personnes Concernées --}}
                <div class="col-12 mb-4">
                    <label class="fw-bold"><i class="fa-solid fa-users me-1"></i> Employés concernés par ce versement ({{ $payment->employees->count() }})</label>
                    <div class="bg-light text-dark p-3 rounded">
                        <div class="row">
                            @foreach($payment->employees as $employee)
                                <div class="col-md-4 mb-1">
                                    <i class="fa-solid fa-circle-check text-success me-1"></i> {{ $employee->full_name }} 
                                    <small class="text-muted">({{ $employee->matricule }})</small>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Détails complémentaires --}}
                <div class="col-12 mb-4">
                    <label class="fw-bold">Détails complémentaires</label>
                    <div class="bg-light text-dark p-2 rounded" style="min-height: 60px; white-space: pre-wrap;">{{ $payment->additional_details ?? 'Aucun détail supplémentaire' }}</div>
                </div>
            </div>

            <hr style="border-top: 1px solid rgba(255,255,255,0.2);">

            {{-- Boutons d'action --}}
            <div class="row mt-4">
                <div class="col-md-6 mb-2">
                    <a href="{{ route('inpsEdit', $payment->id) }}" class="btn w-100 text-white fw-bold shadow-sm" style="background-color: rgb(249, 138, 27); border: none;">
                        <i class="fa-solid fa-pen-to-square me-1"></i> Modifier ce paiement
                    </a>
                </div>
                <div class="col-md-6 mb-2">
                    <a href="{{ route('inpsList') }}" class="btn w-100 text-white fw-bold shadow-sm" style="background-color: rgb(144, 142, 140); border: none;">
                        <i class="fa-solid fa-arrow-left me-1"></i> Retour à la liste
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection