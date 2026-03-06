@extends('admin.layouts.master')
@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4 col-lg-10 mx-auto">
        <div class="card-header py-3" style="background-color:#6c757d; margin-left: -12px; margin-right: -12px;">
            <h5 class="m-0 font-weight-bold text-white text-center">Fiche Impôt : {{ $tax->number }}</h5>
        </div>
        <div class="card-body" style="background-color:#6c757d; margin-left: -12px; margin-right: -12px;">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-4">
                        <label class="fw-bold text-white">Numéro d'enregistrement</label>
                        <div class="border rounded p-2 bg-light text-dark fw-bold">{{ $tax->number }}</div>
                    </div>
                    <div class="mb-4">
                        <label class="fw-bold text-white">Mois concerné</label>
                        <div class="border rounded p-2 bg-light text-dark">{{ $tax->month }}</div>
                    </div>
                    <div class="mb-4">
                        <label class="fw-bold text-white">Description</label>
                        <div class="border rounded p-2 bg-light text-dark">{{ $tax->description === 'Autres' ? $tax->description_other : $tax->description }}</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-4">
                        <label class="fw-bold text-white">Montant Total</label>
                        <div class="border rounded p-2 bg-light text-dark">{{ number_format($tax->amount_fcfa, 0, ',', ' ') }} FCFA</div>
                    </div>
                    <div class="mb-4">
                        <label class="fw-bold text-white">Date d'émission</label>
                        <div class="border rounded p-2 bg-light text-dark">{{ $tax->issue_date->format('d/m/Y') }}</div>
                    </div>
                    <div class="mb-4">
                        <label class="fw-bold text-white">Mode de Payement</label>
                        <div class="border rounded p-2 bg-light text-dark">{{ $tax->payment_mode }}</div>
                    </div>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-md-6 mb-2"><a href="{{ route('taxEdit', $tax->id) }}" class="btn w-100 text-white" style="background-color: rgb(249, 138, 27);">Modifier</a></div>
                <div class="col-md-6 mb-2"><a href="{{ route('taxList') }}" class="btn w-100 text-white" style="background-color: rgb(144, 142, 140);">Retour</a></div>
            </div>
        </div>
    </div>
</div>
@endsection