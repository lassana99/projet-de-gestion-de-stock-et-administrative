@extends('admin.layouts.master')

@section('content')
@php
    function formatValue($value) {
        if ($value === null) return '-';
        return number_format(round($value), 0, ',', ' ');
    }
@endphp

<div class="container-fluid">
    <div class="card shadow mb-4 col-lg-9 mx-auto">
        <div class="card-header py-3" style="background-color:#6c757d; margin-left: -12px; margin-right: -12px;">
            <h5 class="m-0 font-weight-bold text-white text-center">Détails du Prix Fournisseur #{{ $articlePrice->id }}</h5>
        </div>
        <div class="card-body" style="background-color:#6c757d; margin-left: -12px; margin-right: -12px;">
            <div class="row">
                {{-- Colonne gauche --}}
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label fw-bold text-white">Référence</label>
                        <div class="border rounded p-2 bg-light text-dark">{{ $articlePrice->reference ?? '-' }}</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-white">Désignation / Machine</label>
                        <div class="border rounded p-2 bg-light text-dark">{{ $articlePrice->designation ?? '-' }}</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-white">Prix unitaire</label>
                        <div class="border rounded p-2 bg-light text-dark">{{ formatValue($articlePrice->unit_price) }} {{ $articlePrice->currency ?? 'FCFA' }}</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-white">Type</label>
                        <div class="border rounded p-2 bg-light text-dark">{{ $articlePrice->type ?? '-' }}</div>
                    </div>
                </div>

                {{-- Colonne droite --}}
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label fw-bold text-white">Fournisseur</label>
                        <div class="border rounded p-2 bg-light text-dark">{{ $articlePrice->supplier_name ?? ($articlePrice->supplier?->company_name ?? '-') }}</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-white">Date</label>
                        <div class="border rounded p-2 bg-light text-dark">{{ $articlePrice->date ? $articlePrice->date->format('d/m/Y') : '-' }}</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-white">Incoterm</label>
                        <div class="border rounded p-2 bg-light text-dark">{{ $articlePrice->incoterm ?? '-' }}</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-white">Pays</label>
                        <div class="border rounded p-2 bg-light text-dark">{{ $articlePrice->country ?? '-' }}</div>
                    </div>
                </div>
            </div>

            {{-- Boutons --}}
            <div class="row mt-4">
                <div class="col-6">
                    <a href="{{ route('articlePriceEdit', $articlePrice->id) }}" class="btn w-100 text-white" style="background-color: rgb(249, 138, 27); border: none;">
                        <i class="fa-solid fa-pen-to-square" ></i> Modifier
                    </a>
                </div>
                <div class="col-6">
                    <a href="{{ route('articlePriceList') }}" class="btn w-100 text-white" style="background-color: rgb(144, 142, 140);">
                        Retour à la liste
                    </a>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection