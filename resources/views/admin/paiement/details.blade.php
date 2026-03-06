@extends('admin.layouts.master')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4" style="background-color:#6c757d; border: 1px solid white;">
        <div class="card-header py-3" style="background-color:#6c757d; border-bottom: 1px solid white;">
            <h5 class="m-0 font-weight-bold text-white">Détails du Paiement</h5>
        </div>
        <div class="card-body" style="background-color:#6c757d;">
            <div class="row">
                {{-- Informations principales --}}
                <div class="col-md-6 mb-4">
                    <h6 class="text-warning font-weight-bold text-uppercase">Information Facture</h6>
                    <hr style="border-color: white;">
                    <p class="text-white"><strong>N° Facture :</strong> {{ $payment->invoice_number }}</p>
                    <p class="text-white"><strong>Client :</strong> {{ $payment->client_name }}</p>
                    <p class="text-white"><strong>Montant HTVA :</strong> 
                        <span class="badge bg-light text-dark" style="font-size: 1rem;">
                            {{ number_format($payment->amount_htva, 0, '', ' ') }} FCFA
                        </span>
                    </p>
                </div>

                {{-- Informations règlement --}}
                <div class="col-md-6 mb-4">
                    <h6 class="text-warning font-weight-bold text-uppercase">Information Règlement</h6>
                    <hr style="border-color: white;">
                    <p class="text-white"><strong>Mode de paiement :</strong> 
                        <span class="badge bg-info">{{ $payment->payment_method }}</span>
                    </p>
                    <p class="text-white"><strong>Date du paiement :</strong> {{ $payment->payment_date->format('d/m/Y') }}</p>
                    <p class="text-white"><strong>Enregistré le :</strong> {{ $payment->created_at->format('d/m/Y à H:i') }}</p>
                </div>
            </div>

            {{-- Boutons en bas --}}
            <div class="mt-4 d-flex justify-content-between">
                <div>
                    <a href="{{ route('paymentEdit', $payment->id) }}" class="btn text-white shadow-sm" style="background-color: rgb(249, 138, 27); padding: 10px 25px; border: none;">
                        <i class="fa-solid fa-pen-to-square"></i> Modifier
                    </a>
                    
                    {{-- Lien vers la facture source si besoin --}}
                    @if($payment->invoice_id)
                    <a href="{{ route('invoices.details', $payment->invoice_id) }}" class="btn btn-primary shadow-sm ml-2" style="padding: 10px 25px;">
                        <i class="fa-solid fa-file-invoice"></i> Voir la Facture
                    </a>
                    @endif
                </div>

                <a href="{{ route('paymentList') }}" class="btn btn-secondary shadow-sm" style="padding: 10px 25px; background-color: rgb(144, 142, 140); border: none;">
                    <i class="fa-solid fa-arrow-left"></i> Retour
                </a>
            </div>
        </div>
    </div>
</div>
@endsection