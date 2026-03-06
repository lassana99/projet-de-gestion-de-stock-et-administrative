@extends('admin.layouts.master')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4" style="background-color:#6c757d; border: 1px solid white;">
        <div class="card-header py-3" style="background-color:#6c757d; border-bottom: 1px solid white;">
            <h5 class="m-0 font-weight-bold text-white">Enregistrer un Paiement</h5>
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

            <form action="{{ route('paymentStore') }}" method="POST" id="payment-form">
                @csrf
                
                {{-- SECTION 1 : Sélection Facture ou Saisie Manuelle --}}
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="text-white font-weight-bold">N° Facture *</label>
                        <select name="invoice_id" id="invoice_id" class="form-control" required>
                            <option value="" disabled selected>Choisir une facture</option>
                            @foreach($invoices as $inv)
                                <option value="{{ $inv->id }}" data-client="{{ $inv->client }}" data-htva="{{ $inv->total_htva }}">
                                    {{ $inv->invoice_number }}
                                </option>
                            @endforeach
                            <option value="other">Autres (Saisie manuelle)</option>
                        </select>
                    </div>

                    <div class="col-md-6 mb-3" id="manual_invoice_group" style="display: none;">
                        <label class="text-white font-weight-bold">Numéro de facture manuel *</label>
                        <input type="text" name="manual_invoice_number" id="manual_invoice_number" class="form-control" placeholder="Ex: FAC-001...">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="text-white font-weight-bold">Client *</label>
                        {{-- On utilise manual_client_name pour le contrôleur --}}
                        <input type="text" id="client_display" name="manual_client_name" class="form-control" placeholder="Nom du client" readonly style="background-color: #e9ecef;">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="text-white font-weight-bold">Montant total HTVA (FCFA) *</label>
                        {{-- Champ modifiable avec formatage d'espaces --}}
                        <input type="text" name="amount_htva" id="amount_htva" class="form-control font-weight-bold" placeholder="0" required>
                    </div>
                </div>

                <hr style="border-color: white;">

                {{-- SECTION 2 : Mode et Date --}}
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="text-white font-weight-bold">Mode de paiement *</label>
                        <select name="payment_method" id="payment_method" class="form-control" required>
                            <option value="Cash">Cash</option>
                            <option value="Orange Money">Orange Money</option>
                            <option value="Wave">Wave</option>
                            <option value="Sama Money">Sama Money</option>
                            <option value="Financement">Financement</option>
                            <option value="Virement bancaire">Virement bancaire</option>
                            <option value="Autres">Autres</option>
                        </select>
                        <input type="text" name="other_method" id="other_method" class="form-control mt-2" placeholder="Précisez le mode de paiement..." style="display:none;">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="text-white font-weight-bold">Date du paiement *</label>
                        <input type="date" name="payment_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                </div>

                <div class="mt-4 d-flex justify-content-between">
                    <button type="submit" class="btn fw-bold text-white shadow-sm" style="background-color: rgb(249, 138, 27); border: none; padding: 10px 25px;">
                        <i class="fa-solid fa-save mr-1"></i> Enregistrer le paiement
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
        return value.toString().replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, " ");
    }

    function unformatNumber(str) {
        return str.toString().replace(/\s/g, '');
    }

    // --- Gestion du changement de sélection de facture ---
    invoiceSelect.addEventListener('change', function() {
        if (this.value === 'other') {
            // Mode Manuel
            manualInvoiceGroup.style.display = 'block';
            manualInvoiceInput.required = true;
            
            clientDisplay.readOnly = false;
            clientDisplay.style.backgroundColor = '#ffffff';
            clientDisplay.value = '';
            
            amountInput.value = '';
        } else {
            // Mode Automatique (Facture existante)
            manualInvoiceGroup.style.display = 'none';
            manualInvoiceInput.required = false;
            manualInvoiceInput.value = '';
            
            clientDisplay.readOnly = true;
            clientDisplay.style.backgroundColor = '#e9ecef';

            let opt = this.options[this.selectedIndex];
            clientDisplay.value = opt.dataset.client || '';
            
            // Pré-remplissage du montant HTVA formaté
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
        // On retire les espaces pour que le contrôleur reçoive un nombre pur
        amountInput.value = unformatNumber(amountInput.value);
    });
});
</script>
@endsection