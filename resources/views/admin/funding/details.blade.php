@extends('admin.layouts.master')

@section('content')
@php
  use \Carbon\Carbon;
@endphp

<div class="container-fluid">
  <div class="card shadow mb-4 col-md-8 mx-auto" style="background-color:#6c757d; margin-left: -12px; margin-right: -12px;">
    <div class="card-header py-3" style="background-color:#6c757d; margin-left: -12px; margin-right: -12px;">
      <h5 class="m-0 font-weight-bold text-white text-center">Détails du prêt bancaire #{{ $funding->id }}</h5>
    </div>
    <div class="card-body" style="background-color:#6c757d; margin-left: -12px; margin-right: -12px;">
      <ul class="list-group">
        <li class="list-group-item text-dark"><strong>Motif :</strong> {{ $funding->motif }}</li>
        <li class="list-group-item text-dark"><strong>Nom de la banque :</strong> {{ $funding->nom_de_banque }}</li>
        <li class="list-group-item text-dark"><strong>Montant emprunté :</strong> {{ number_format($funding->montant_emprunte, 2, ',', ' ') }}</li>
        <li class="list-group-item text-dark"><strong>Nombre de jours :</strong> {{ $funding->nombre_de_jours }}</li>
        <li class="list-group-item text-dark"><strong>Taux (%) :</strong> {{ $funding->taux }}</li>
        <li class="list-group-item text-dark"><strong>Montant à payer :</strong> {{ number_format($funding->montant_a_payer, 2, ',', ' ') }}</li>
        <li class="list-group-item text-dark"><strong>Montant à payer par item :</strong> {{ number_format($funding->montant_a_payer_par_item ?? 0, 2, ',', ' ') }}</li>
        <li class="list-group-item text-dark"><strong>Nombre d'items :</strong> {{ $funding->nombre_d_items }}</li>
        <li class="list-group-item text-dark"><strong>Date :</strong> {{ $funding->date ? Carbon::parse($funding->date)->format('d/m/Y') : 'N/A' }}</li>
      </ul>
      <div class="row mt-4">
        <div class="col-6">
          <a href="{{ route('funding.list') }}" class="btn w-100 text-white" style="background-color: rgb(144, 142, 140); border: none;">Retour à la liste</a>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
