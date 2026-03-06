@extends('admin.layouts.master')

@section('content')
@php
    /**
     * Helper pour formater les montants en FCFA
     * Utilise le format sans décimales pour le FCFA
     */
    function formatFcfa($value) {
        if ($value === null) return '-';
        return number_format($value, 0, ',', ' ') . ' FCFA';
    }
@endphp

<div class="container-fluid">
    {{-- On centre la carte pour une meilleure lecture sur grand écran --}}
    <div class="card shadow mb-4 col-lg-10 mx-auto">
        
        {{-- En-tête de la fiche --}}
        <div class="card-header py-3" style="background-color:#6c757d; margin-left: -12px; margin-right: -12px;">
            <h5 class="m-0 font-weight-bold text-white text-center">
                <i class="fa-solid fa-file-invoice me-2"></i> Fiche Loyer : {{ $rent->number }}
            </h5>
        </div>

        <div class="card-body" style="background-color:#6c757d; margin-left: -12px; margin-right: -12px;">
            <div class="row">
                
                {{-- Informations Générales (Colonne Gauche) --}}
                <div class="col-md-6">
                    <div class="mb-4">
                        <label class="form-label fw-bold text-white"><i class="fa-solid fa-hashtag me-1"></i> Numéro d'enregistrement</label>
                        <div class="border rounded p-2 bg-light text-dark fw-bold" style="font-size: 1.1rem;">
                            {{ $rent->number }}
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold text-white"><i class="fa-solid fa-calendar-days me-1"></i> Mois concerné</label>
                        <div class="border rounded p-2 bg-light text-dark">{{ $rent->month }}</div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold text-white"><i class="fa-solid fa-building me-1"></i> Structure / Emplacement</label>
                        <div class="border rounded p-2 bg-light text-dark">{{ $rent->structure }}</div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold text-white"><i class="fa-solid fa-bookmark me-1"></i> Référence externe</label>
                        <div class="border rounded p-2 bg-light text-dark">{{ $rent->reference ?? 'Aucune référence saisie' }}</div>
                    </div>
                </div>

                {{-- Informations Financières (Colonne Droite) --}}
                <div class="col-md-6">
                    <div class="mb-4">
                        <label class="form-label fw-bold text-white"><i class="fa-solid fa-money-bill-wave me-1"></i> Montant Total</label>
                        <div class="border rounded p-2 bg-light text-dark fw-bold" style="font-size: 1.2rem; color: #2e59d9 !important;">
                            {{ formatFcfa($rent->amount_fcfa) }}
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold text-white"><i class="fa-solid fa-clock me-1"></i> Date d'émission</label>
                        <div class="border rounded p-2 bg-light text-dark">
                            {{ $rent->issue_date ? $rent->issue_date->format('d/m/Y') : '-' }}
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold text-white"><i class="fa-solid fa-credit-card me-1"></i> Mode de Payement</label>
                        <div class="border rounded p-2 bg-light text-dark">
                            @if($rent->payment_mode === 'Autres')
                                <span class="badge bg-info text-white">Autre</span> {{ $rent->payment_mode_other }}
                            @else
                                {{ $rent->payment_mode }}
                            @endif
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold text-white"><i class="fa-solid fa-circle-info me-1"></i> État du payement</label>
                        <div class="border rounded p-2 text-center 
                            @if($rent->status == 'Payé') bg-success text-white 
                            @elseif($rent->status == 'Payé partiellement') bg-warning text-dark 
                            @else bg-danger text-white @endif">
                            <strong style="text-transform: uppercase;">{{ $rent->status }}</strong>
                        </div>
                    </div>
                </div>
            </div>

            <hr style="border-top: 1px solid rgba(255,255,255,0.2);">

            {{-- Zone d'actions --}}
            <div class="row mt-4">
                <div class="col-md-6 mb-2">
                    <a href="{{ route('rentEdit', $rent->id) }}" class="btn w-100 text-white shadow-sm" 
                       style="background-color: rgb(249, 138, 27); border: none; font-weight: bold;">
                        <i class="fa-solid fa-pen-to-square me-1"></i> Modifier ces informations
                    </a>
                </div>
                <div class="col-md-6 mb-2">
                    <a href="{{ route('rentList') }}" class="btn w-100 text-white shadow-sm" 
                       style="background-color: rgb(144, 142, 140); border: none; font-weight: bold;">
                        <i class="fa-solid fa-arrow-left me-1"></i> Retour à la liste des loyers
                    </a>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection