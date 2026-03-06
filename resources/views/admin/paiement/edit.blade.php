@extends('admin.layouts.master')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4" style="background-color:#6c757d; border: 1px solid white;">
        <div class="card-header py-3" style="background-color:#6c757d; border-bottom: 1px solid white;">
            <h5 class="m-0 font-weight-bold text-white">Modifier le Paiement : {{ $payment->invoice_number }}</h5>
        </div>
        <div class="card-body" style="background-color:#6c757d;">

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('paymentUpdate', $payment->id) }}" method="POST" id="payment-form">
                @csrf
                @method('PUT')

                @php
                    $standard_methods = ['Cash', 'Orange Money', 'Wave', 'Sama Money', 'Financement', 'Virement bancaire'];
                    $is_other_method = !in_array($payment->payment_method, $standard_methods);
                    
                    // Vérifie si le paiement est une saisie manuelle (invoice_id est null en DB)
                    $is_manual_invoice = is_null($payment->invoice_id);
                @endphp

                {{-- SECTION 1 : Sélection Facture ou Saisie Manuelle --}}
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="text-white font-weight-bold">N° Facture *</label>
                        <select name="invoice_id" id="invoice_id" class="form-control" required>
                            <option value="" disabled>Choisir une facture</option>
                            @foreach($invoices as $inv)
                                <option value="{{ $inv->id }}" 
                                    data-client="{{ $inv->client }}" 
                                    data-htva="{{ $inv->total_htva }}"
                                    {{ $payment->invoice_id == $inv->id ? 'selected' : '' }}>
                                    {{ $inv->invoice_number }}
                                </option>
                            @endforeach
                            <option value="other" {{ $is_manual_invoice ? 'selected' : '' }}>Autres (Saisie manuelle)</option>
                        </select>
                    </div>

                    <div class="col-md-6 mb-3" id="manual_invoice_group" style="{{ $is_manual_invoice ? '' : 'display: none;' }}">
                        <label class="text-white font-weight-bold">Numéro de facture manuel *</label>
                        <input type="text" name="manual_invoice_number" id="manual_invoice_number" 
                               class="form-control" 
                               value="{{ $is_manual_invoice ? $payment->invoice_number : '' }}"
                               placeholder="Ex: FAC-001...">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="text-white font-weight-bold">Client *</label>
                        <input type="text" id="client_display" name="manual_client_name" 
                               class="form-control" 
                               value="{{ $payment->client_name }}"
                               {{ $is_manual_invoice ? '' : 'readonly' }} 
                               style="background-color: {{ $is_manual_invoice ? '#ffffff' : '#e9ecef' }};">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="text-white font-weight-bold">Montant total HTVA (FCFA) *</label>
                        {{-- Affichage initial formaté avec PHP, puis géré par JS --}}
                        <input type="text" name="amount_htva" id="amount_htva" 
                               class="form-control font-weight-bold" 
                               value="{{ number_format($payment->amount_htva, 0, '', ' ') }}" 
                               required>
                    </div>
                </div>

                <hr style="border-color: white;">

                {{-- SECTION 2 : Mode et Date --}}
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="text-white font-weight-bold">Mode de paiement *</label>
                        <select name="payment_method" id="payment_method" class="form-control" required>
                            @foreach($standard_methods as $method)
                                <option value="{{ $method }}" {{ $payment->payment_method == $method ? 'selected' : '' }}>
                                    {{ $method }}
                                </option>
                            @endforeach
                            <option value="Autres" {{ $is_other_method ? 'selected' : '' }}>Autres</option>
                        </select>
                        <input type="text" name="other_method" id="other_method" 
                               class="form-control mt-2" 
                               value="{{ $is_other_method ? $payment->payment_method : '' }}"
                               placeholder="Précisez le mode de paiement..." 
                               style="{{ $is_other_method ? '' : 'display:none;' }}">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="text-white font-weight-bold">Date du paiement *</label>
                        <input type="date" name="payment_date" class="form-control" value="{{ $payment->payment_date->format('Y-m-d') }}" required>
                    </div>
                </div>

                <div class="mt-4 d-flex justify-content-between">
                    <button type="submit" class="btn fw-bold text-white shadow-sm" style="background-color: rgb(249, 138, 27); border: none; padding: 10px 25px;">
                        <i class="fa-solid fa-sync mr-1"></i> Mettre à jour le paiement
                    </button>
                    <a href="{{ route('paymentList') }}" class="btn btn-secondary shadow-sm" style="padding: 10px 25px; background-color: rgb(144, 142, 140); border: none;">
                        Annuler
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const invoiceSelect = document.getElementById('invoice_id');
    const clientDisplay = document.getElementById('client_display');
    const amountInput = document.getElementById('amount_htva');
    const manualInvoiceGroup = document.getElementById('manual_invoice_group');
    const manualInvoiceInput = document.getElementById('manual_invoice_number');
    const paymentMethodSelect = document.getElementById('payment_method');
    const otherMethodInput = document.getElementById('other_method');
    const form = document.getElementById('payment-form');

    // --- Fonction de Formatage (Espaces pour les milliers) ---
    function formatNumber(value) {
        if (!value) return "";
        // On nettoie tout ce qui n'est pas un chiffre puis on ajoute les espaces
        return value.toString().replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, " ");
    }

    function unformatNumber(str) {
        return str.toString().replace(/\s/g, '');
    }

    // --- Gestion du changement de sélection de facture ---
    invoiceSelect.addEventListener('change', function() {
        if (this.value === 'other') {
            manualInvoiceGroup.style.display = 'block';
            manualInvoiceInput.required = true;
            clientDisplay.readOnly = false;
            clientDisplay.style.backgroundColor = '#ffffff';
        } else {
            manualInvoiceGroup.style.display = 'none';
            manualInvoiceInput.required = false;
            clientDisplay.readOnly = true;
            clientDisplay.style.backgroundColor = '#e9ecef';

            let opt = this.options[this.selectedIndex];
            clientDisplay.value = opt.dataset.client || '';
            
            let htva = opt.dataset.htva || 0;
            amountInput.value = formatNumber(Math.round(htva));
        }
    });

    // --- Formatage en direct du montant ---
    amountInput.addEventListener('input', function() {
        this.value = formatNumber(this.value);
    });

    // --- Gestion du mode de paiement "Autres" ---
    paymentMethodSelect.addEventListener('change', function() {
        if (this.value === 'Autres') {
            otherMethodInput.style.display = 'block';
            otherMethodInput.required = true;
        } else {
            otherMethodInput.style.display = 'none';
            otherMethodInput.required = false;
        }
    });

    // --- Nettoyage avant soumission ---
    form.addEventListener('submit', function() {
        amountInput.value = unformatNumber(amountInput.value);
    });
});
</script>
@endsection