@extends('admin.layouts.master')

@section('content')

<div class="container-fluid">
    {{-- Le background de la carte est forcé en gris foncé --}}
    <div class="card shadow mb-4" style="background-color:#6c757d;"> 
        
        {{-- Card Header Uniformisé en Gris Foncé --}}
        <div class="card-header py-3" style="background-color:#6c757d; border-bottom: 1px solid rgba(255,255,255,0.1);">
            <h5 class="m-0 font-weight-bold text-white"><i class="fa-solid fa-truck"></i> Créer un Bordereau de Livraison</h5>
        </div>

        {{-- Card Body Uniformisé en Gris Foncé --}}
        <div class="card-body" style="background-color:#6c757d;">
            
            <div class="text-center mb-4">
                <h4 class="h4 font-weight-bold text-white">Sélectionnez la Facture et les Détails Initiaux</h4>
                <p class="text-light">Étape 1 sur 2 : Paramètres de base</p>
            </div>
            
            {{-- Formulaire géré par JavaScript pour redirection --}}
            <form id="invoiceSelectionForm">
                @csrf
                
                <div class="row mb-4">
                    
                    {{-- 1. Liste Déroulante des Factures (col-md-4) --}}
                    <div class="col-md-4 mb-3">
                        <label for="invoice_id" class="form-label text-white fw-bold">Facture Source <span class="text-danger">*</span></label>
                        <select name="invoice_id" id="invoice_id" class="form-control" required>
                            <option value="">-- Sélectionnez une Facture (N°) --</option>
                            @foreach ($invoices as $invoice)
                                <option value="{{ $invoice->id }}">
                                    {{ $invoice->invoice_number }} - {{ $invoice->client }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-white-50 mt-1" id="invoice_error" style="display:none; color: #ffd700 !important;">Veuillez sélectionner une facture.</small>
                    </div>

                    {{-- 2. Champ Date (col-md-4) --}}
                    <div class="col-md-4 mb-3">
                        <label for="date_delivery" class="form-label text-white fw-bold">Date de Livraison <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="date_delivery" name="date_delivery" 
                                value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}" required>
                    </div>
                    
                    {{-- 3. Champ Bon de Commande N° (col-md-4) --}}
                    <div class="col-md-4 mb-3">
                        <label for="purchase_order_number" class="form-label text-white fw-bold">N° Bon de Commande (BC)</label>
                        <input type="text" class="form-control" id="purchase_order_number" 
                                name="purchase_order_number" placeholder="Ex: BC-2024-001">
                    </div>
                    
                </div>
                
                <hr style="border-color: rgba(255,255,255,0.2);">

                {{-- Boutons d'action --}}
                <div class="row mt-4">
                    <div class="col d-flex justify-content-between align-items-center"> 
                        
                        {{-- Bouton Continuer (Orange) --}}
                        <div>
                            <button type="submit" class="btn text-white fw-bold" 
                                style="background-color: rgb(249, 138, 27); border: none; padding: 10px 25px;">
                                <i class="fa-solid fa-arrow-right"></i> Continuer vers le détail
                            </button>
                        </div>
                        
                        {{-- Bouton Annuler (Gris) --}}
                        <div>
                            <a href="{{ route('delivery_notes.list') }}" class="btn text-white" 
                                style="background-color: rgb(144, 142, 140); border: none; padding: 10px 25px;"> 
                                Annuler
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Script de redirection --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('invoiceSelectionForm');
        const invoiceSelect = document.getElementById('invoice_id');
        const dateInput = document.getElementById('date_delivery');
        const purchaseOrderInput = document.getElementById('purchase_order_number');
        
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const selectedInvoiceId = invoiceSelect.value;
            
            if (!selectedInvoiceId) {
                document.getElementById('invoice_error').style.display = 'block';
                return;
            }

            // Récupération des valeurs
            const dateValue = dateInput.value;
            const poNumber = purchaseOrderInput.value;
            
            // 1. Construction de la route : delivery_notes.create_from_invoice
            // On utilise l'ID sélectionné dans l'URL
            let redirectUrl = "{{ route('delivery_notes.create_from_invoice', ':id') }}";
            redirectUrl = redirectUrl.replace(':id', selectedInvoiceId);
            
            // 2. Préparation des paramètres GET pour passer la date et le BC à l'étape suivante
            const params = new URLSearchParams();
            if (dateValue) {
                params.append('date_delivery', dateValue);
            }
            if (poNumber) {
                params.append('purchase_order_number', poNumber);
            }
            
            // 3. Redirection vers le formulaire complet (create.blade.php)
            window.location.href = redirectUrl + '?' + params.toString();
        });
    });
</script>

@endsection