@extends('admin.layouts.master')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4 col-lg-10 mx-auto">
        <div class="card-header py-3" style="background-color:#6c757d; margin-left: -12px; margin-right: -12px;">
            <h5 class="m-0 font-weight-bold text-white text-center">Fiche Détail : {{ $utility->number }}</h5>
        </div>
        <div class="card-body" style="background-color:#6c757d; margin-left: -12px; margin-right: -12px;">
            <div class="row">
                {{-- Colonne Gauche --}}
                <div class="col-md-6">
                    <div class="mb-4">
                        <label class="fw-bold text-white"><i class="fa-solid fa-hashtag me-1"></i> Numéro d'enregistrement</label>
                        <div class="border rounded p-2 bg-light text-dark fw-bold">{{ $utility->number }}</div>
                    </div>
                    <div class="mb-4">
                        <label class="fw-bold text-white"><i class="fa-solid fa-calendar-days me-1"></i> Mois concerné</label>
                        <div class="border rounded p-2 bg-light text-dark">{{ $utility->month }}</div>
                    </div>
                    <div class="mb-4">
                        <label class="fw-bold text-white"><i class="fa-solid fa-circle-info me-1"></i> Description</label>
                        <div class="border rounded p-2 bg-light text-dark">{{ $utility->displayDescription() }}</div>
                    </div>
                    <div class="mb-4">
                        <label class="fw-bold text-white"><i class="fa-solid fa-bookmark me-1"></i> Référence</label>
                        <div class="border rounded p-2 bg-light text-dark">{{ $utility->reference ?? '-' }}</div>
                    </div>
                </div>

                {{-- Colonne Droite --}}
                <div class="col-md-6">
                    <div class="mb-4">
                        <label class="fw-bold text-white"><i class="fa-solid fa-money-bill-wave me-1"></i> Montant Total</label>
                        <div class="border rounded p-2 bg-light text-dark" style="font-size: 1.2rem;">
                            {{ $utility->formattedAmount() }} FCFA
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="fw-bold text-white"><i class="fa-solid fa-clock me-1"></i> Date d'émission</label>
                        <div class="border rounded p-2 bg-light text-dark">
                            {{ $utility->issue_date->format('d/m/Y') }}
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="fw-bold text-white"><i class="fa-solid fa-credit-card me-1"></i> Mode de Payement</label>
                        <div class="border rounded p-2 bg-light text-dark">
                            {{ $utility->payment_mode }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-md-6 mb-2">
                    <a href="{{ route('utilityEdit', $utility->id) }}" class="btn w-100 text-white fw-bold shadow-sm" style="background-color: rgb(249, 138, 27); border: none;">
                        <i class="fa-solid fa-pen-to-square me-1"></i> Modifier
                    </a>
                </div>
                <div class="col-md-6 mb-2">
                    <a href="{{ route('utilityList') }}" class="btn w-100 text-white fw-bold shadow-sm" style="background-color: rgb(144, 142, 140); border: none;">
                        <i class="fa-solid fa-arrow-left me-1"></i> Retour à la liste
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection