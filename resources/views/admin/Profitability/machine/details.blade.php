@extends('admin.layouts.master')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4 col-lg-9 mx-auto">
        <div class="card-header py-3"
            style="background-color:#6c757d; margin-left: -12px; margin-right: -12px;">
            <h5 class="m-0 font-weight-bold text-white text-center">Détails de la rentabilité #{{ $machine->id }}</h5>
        </div>
        <div class="card-body"
            style="background-color:#6c757d; margin-left: -12px; margin-right: -12px;">
            <ul class="list-group">
                <li class="list-group-item text-dark"><strong>Référence :</strong> {{ $machine->purchase_reference }}</li>
                <li class="list-group-item text-dark"><strong>Marque :</strong> {{ $machine->brand }}</li>
                <li class="list-group-item text-dark"><strong>État :</strong> {{ $machine->status }}</li>
                <li class="list-group-item text-dark"><strong>Poids (KG) :</strong> {{ number_format($machine->weight ?? 0, 0, ',', ' ') }}</li>
                <li class="list-group-item text-dark"><strong>Prix achat unité :</strong> {{ number_format($machine->unit_purchase_price ?? 0, 0, ',', ' ') }} {{ $machine->purchase->currency ?? '' }}</li>
                <li class="list-group-item text-dark"><strong>Transport urbain global :</strong> {{ number_format($machine->global_urbain_transport ?? 0, 0, ',', ' ') }}</li>
                <li class="list-group-item text-dark"><strong>Transport urbain (par item) :</strong> {{ number_format($machine->urban_transport ?? 0, 0, ',', ' ') }}</li>
                <li class="list-group-item text-dark"><strong>Concierge :</strong> {{ number_format($machine->concierge ?? 0, 0, ',', ' ') }}</li>
                <li class="list-group-item text-dark"><strong>Transport Source / BKO :</strong> {{ number_format($machine->transport_source_bko ?? 0, 0, ',', ' ') }}</li>
                <li class="list-group-item text-dark"><strong>Droits de douane (%) :</strong> {{ number_format($machine->customs ?? 0, 0, ',', ' ') }}</li>
                <li class="list-group-item text-dark"><strong>Transport terrestre :</strong> {{ number_format($machine->land_transport ?? 0, 0, ',', ' ') }}</li>
                <li class="list-group-item text-dark"><strong>Marge (%) :</strong> {{ number_format($machine->margin ?? 0, 0, ',', ' ') }}</li>
                <li class="list-group-item text-dark"><strong>Financement :</strong> {{ number_format($machine->funding ?? 0, 0, ',', ' ') }}</li>
                <li class="list-group-item text-dark"><strong>Nombre d'item :</strong> {{ $machine->quantity ?? 1 }}</li>
                <li class="list-group-item text-dark"><strong>Request :</strong> {{ $machine->request ?? '-' }}</li>
                <li class="list-group-item text-dark"><strong>Fournisseur :</strong> {{ $machine->suppliername ?? '-' }}</li>
                
                {{-- NOUVEAU CHAMP : Date de rentabilité --}}
                <li class="list-group-item text-dark" style="background-color: #e2e3e5;">
                    <strong>Date de rentabilité :</strong> {{ $machine->date_profitability ? $machine->date_profitability->format('d/m/Y') : '-' }}
                </li>
                
                {{-- Séparateur visuel pour les totaux --}}
                <li class="list-group-item bg-light text-dark font-weight-bold">RÉCAPITULATIF FINANCIER</li>

                <li class="list-group-item text-dark"><strong>Bénéfice :</strong> {{ number_format($machine->profit ?? 0, 0, ',', ' ') }} FCFA</li>
                <li class="list-group-item text-dark"><strong>Prix de vente unité HT (FCFA) :</strong> {{ number_format($machine->selling_price ?? 0, 0, ',', ' ') }} FCFA</li>
                <li class="list-group-item text-dark"><strong>TVA (18%) :</strong> {{ number_format($machine->tva ?? 0, 0, ',', ' ') }} FCFA</li>
                <li class="list-group-item text-dark font-weight-bold" style="background-color: #fff3cd;">
                    <strong>PRIX DE VENTE UNITÉ TTC :</strong> {{ number_format($machine->selling_price_ttc ?? 0, 0, ',', ' ') }} FCFA
                </li>
            </ul>

            <div class="row mt-4">
                <div class="col-6">
                    <a href="{{ route('machineList') }}" class="btn w-100 text-white"
                        style="background-color: rgb(144, 142, 140); border: none;">
                        <i class="fas fa-arrow-left"></i> Retour à la liste
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection