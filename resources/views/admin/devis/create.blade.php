@extends('admin.layouts.master')

@section('content')

<div class="container-fluid">
    <div class="card shadow mb-4">
        {{-- Card Header --}}
        <div class="card-header py-3" style="background-color:#6c757d;">
            <h5 class="m-0 font-weight-bold text-white">Créer un nouveau devis</h5>
        </div>

        {{-- Card Body --}}
        <div class="card-body" style="background-color:#6c757d;">

            @if ($errors->any())
                <div class="alert alert-danger">
                    <strong>Erreurs de validation:</strong>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Formulaire avec gestion des fichiers --}}
            <form action="{{ route('devis.store') }}" method="POST" id="devis-form" enctype="multipart/form-data">
                @csrf

                {{-- SECTION 1: Client / Adresse --}}
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="client" class="form-label text-white">Client <span class="text-danger">*</span></label>
                            <select class="form-control" id="client" name="client" required>
                                <option value="" disabled {{ old('client') ? '' : 'selected' }}>Choisir un client</option>
                                @foreach($customers as $customer)
                                    <option 
                                        value="{{ $customer->name }}" 
                                        data-code="{{ $customer->code_client }}" 
                                        data-payment="{{ $customer->payment_deadline }}"
                                        data-address="{{ $customer->address }}"
                                        {{ old('client') === $customer->name ? 'selected' : '' }}>
                                        {{ $customer->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="client_address" class="form-label text-white">Adresse du client</label>
                            <textarea class="form-control" id="client_address" name="client_address" rows="3">{{ old('client_address') }}</textarea>
                        </div>
                    </div>

                    <div class="col-md-4"></div>

                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="date_devis" class="form-label text-white">Date du devis <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="date_devis" name="date_devis" value="{{ old('date_devis', now()->format('Y-m-d')) }}" required>
                        </div>
                    </div>
                </div>

                <hr style="border-color: #8c8f92;">

                {{-- SECTION 2: Conditions du devis --}}
                <h6 class="text-white mb-3">Conditions du devis</h6>
                
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="validity_select" class="form-label text-white">Validité de l'Offre <span class="text-danger">*</span></label>
                        <select class="form-control" id="validity_select" required>
                            <option value="" disabled {{ old('validity') ? '' : 'selected' }}>Choisir une validité</option>
                            @php
                                $validity_options = ['30 jours', '60 jours', '90 jours', 'Autres'];
                                $old_validity = old('validity'); 
                            @endphp
                            @foreach ($validity_options as $option)
                                <option value="{{ $option }}" {{ $old_validity === $option ? 'selected' : '' }}>{{ $option }}</option>
                            @endforeach
                        </select>
                        <input type="text" class="form-control mt-2" id="validity_other" placeholder="Saisir la validité..." style="display: none;">
                        <input type="hidden" id="validity" name="validity" value="{{ old('validity') }}">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="delivery_terms_select" class="form-label text-white">Délai de livraison <span class="text-danger">*</span></label>
                        <select class="form-control" id="delivery_terms_select" required>
                            <option value="" disabled {{ old('delivery_terms') ? '' : 'selected' }}>Choisir un délai</option>
                            @php $delivery_options = ['Article(s) disponible(s)', 'Une semaine', 'Deux semaines', 'Autres']; @endphp
                            @foreach ($delivery_options as $option)
                                <option value="{{ $option }}" {{ old('delivery_terms') === $option ? 'selected' : '' }}>{{ $option }}</option>
                            @endforeach
                        </select>
                        <input type="text" class="form-control mt-2" id="delivery_terms_other" placeholder="Saisir le délai..." style="display: none;">
                        <input type="hidden" id="delivery_terms" name="delivery_terms" value="{{ old('delivery_terms') }}">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="tax_rate" class="form-label text-white">Taux de TVA</label>
                        <input type="number" step="0.01" class="form-control" id="tax_rate" name="tax_rate" value="{{ old('tax_rate', 0.18) }}">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="code_client" class="form-label text-white">Code client</label>
                        <input type="text" class="form-control" id="code_client" name="code_client" value="{{ old('code_client') }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="delivery_location" class="form-label text-white">Lieu de livraison</label>
                        <input type="text" class="form-control" id="delivery_location" name="delivery_location" value="{{ old('delivery_location') }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="payment_terms" class="form-label text-white">Conditions de règlement</label>
                        <input type="text" class="form-control" id="payment_terms" name="payment_terms" value="{{ old('payment_terms') }}">
                    </div>
                </div>

                <hr style="border-color: white;">

                {{-- SECTION 3: Lignes du devis --}}
                <h6 class="text-white">Contenu du devis</h6>

                <div id="lines-container">
                    @php 
                        $oldLines = old('lines');
                        $lineData = is_array($oldLines) ? $oldLines : [0 => ['product_name' => '', 'reference' => '', 'unit_price_ht' => '', 'quantity' => '1']];
                    @endphp
                    
                    @foreach($lineData as $index => $data)
                        <div class="line-item row align-items-start mb-3" data-index="{{ $index }}">
                            
                            {{-- 1. RÉFÉRENCE (Menu déroulant) --}}
                            <div class="col-md-2 mb-2">
                                <label class="form-label text-white">Référence <span class="text-danger">*</span></label>
                                <select class="form-control ref-select" name="lines[{{ $index }}][ref_choice]" required>
                                    <option value="" disabled selected>Choisir réf.</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->reference }}" 
                                                data-name="{{ $product->name }}" 
                                                data-price="{{ $product->price }}">
                                            {{ $product->reference }}
                                        </option>
                                    @endforeach
                                    <option value="other">Autres (Saisie manuelle)</option>
                                </select>
                                <input type="text" class="form-control mt-2 manual-ref-input" name="lines[{{ $index }}][reference]" value="{{ $data['reference'] ?? '' }}" placeholder="Saisir référence" style="display:none;">
                            </div>

                            {{-- 2. PRODUIT (Désignation) --}}
                            <div class="col-md-3 mb-2">
                                <label class="form-label text-white">Produit (Désignation) <span class="text-danger">*</span></label>
                                <input type="text" class="form-control product-name-input" name="lines[{{ $index }}][product_name]" value="{{ $data['product_name'] ?? '' }}" placeholder="Nom du produit" required>
                            </div>

                            {{-- 3. IMAGE (Choix manuel par l'utilisateur) --}}
                            <div class="col-md-2 mb-2">
                                <label class="form-label text-white">Image</label>
                                <div class="d-flex align-items-center">
                                    <div class="img-preview-container mr-2" style="width: 40px; height: 40px; background: #eee; border-radius: 4px; overflow: hidden; display: flex; justify-content: center; align-items: center; border: 1px solid #ccc;">
                                        <img src="" class="img-preview" style="max-width: 100%; max-height: 100%; display: none;">
                                        <i class="fa fa-image text-muted empty-icon"></i>
                                    </div>
                                    <input type="file" class="form-control-file text-white user-image-input" name="lines[{{ $index }}][image]" accept="image/*" style="font-size: 0.7rem; width: 100px;">
                                </div>
                            </div>

                            {{-- 4. PRIX UNITAIRE --}}
                            <div class="col-md-2 mb-2">
                                <label class="form-label text-white">P.U. HT <span class="text-danger">*</span></label>
                                <input type="text" class="form-control unit-price formatted-number-input" name="lines[{{ $index }}][unit_price_ht]" value="{{ $data['unit_price_ht'] ?? '' }}" required placeholder="0">
                            </div>

                            {{-- 5. QUANTITÉ --}}
                            <div class="col-md-1 mb-2">
                                <label class="form-label text-white">Qté <span class="text-danger">*</span></label>
                                <input type="number" step="1" class="form-control quantity-input" name="lines[{{ $index }}][quantity]" value="{{ $data['quantity'] ?? '1' }}" required min="1">
                            </div>

                            {{-- 6. TOTAL LIGNE --}}
                            <div class="col-md-1 mb-2">
                                <label class="form-label text-white">Total</label>
                                <input type="text" class="form-control total-ht-row text-right" readonly style="background-color: #e9ecef; font-weight: bold; padding: 6px 2px; font-size: 0.85rem;">
                            </div>

                            {{-- 7. SUPPRESSION --}}
                            <div class="col-md-1 mb-2 pt-4 text-center">
                                <button type="button" class="btn btn-danger btn-sm remove-line"><i class="fa-solid fa-trash"></i></button>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mb-3">
                    <button type="button" id="add-line" class="btn btn-sm text-white" style="background-color: #008000;">
                        <i class="fa fa-plus"></i> Ajouter une ligne
                    </button>
                </div>

                <hr style="border-color: white;">

                {{-- SECTION 4 : Récapitulatif --}}
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="text-white">Montant global (HT)</label>
                        <input type="text" class="form-control text-right" id="montant_global" readonly value="0" style="font-weight: bold;">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="text-white">Remise (%)</label>
                        <input type="number" step="0.01" min="0" max="100" class="form-control" id="discount" name="discount" value="{{ old('discount', 0) }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="text-white">Montant total HTVA</label>
                        <input type="text" class="form-control text-right" id="total_htva" readonly value="0" style="font-weight: bold;">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="text-white">TVA</label>
                        <input type="text" class="form-control text-right" id="total_tva" readonly value="0" style="font-weight: bold;">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="text-white">Montant total TTC</label>
                        <input type="text" class="form-control text-right" id="total_ttc" readonly value="0" style="font-weight: bold; background-color: #f8f9fa;">
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col d-flex justify-content-between">
                        <button type="submit" class="btn btn-warning text-white" style="background-color: #f98a1b; border: none;">Enregistrer le devis</button>
                        <a href="{{ route('devis.list') }}" class="btn btn-secondary" style="background-color: rgb(144, 142, 140); border: none;">Annuler</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const container = document.getElementById('lines-container');
    const addButton = document.getElementById('add-line');
    const clientSelect = document.getElementById('client');
    const devisForm = document.getElementById('devis-form');
    let lineIndex = container.querySelectorAll('.line-item').length;

    // --- Fonctions de Formatage ---
    function formatNumber(value) {
        if (isNaN(value) || value === null) return "0";
        return Math.round(value).toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ");
    }

    function unformatNumber(str) {
        if (!str) return 0;
        return parseFloat(str.toString().replace(/\s/g, '')) || 0;
    }

    function applyLiveFormatting(input) {
        let value = input.value.replace(/\D/g, ''); 
        if (value === "") {
            input.value = "";
            return;
        }
        input.value = formatNumber(parseFloat(value));
    }

    // --- Aperçu de l'image lors du choix manuel de l'utilisateur ---
    function handleImagePreview(inputElement) {
        const row = inputElement.closest('.line-item');
        const preview = row.querySelector('.img-preview');
        const icon = row.querySelector('.empty-icon');
        
        if (inputElement.files && inputElement.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
                icon.style.display = 'none';
            }
            reader.readAsDataURL(inputElement.files[0]);
        }
    }

    // --- Gestion du changement de Référence (Auto-remplissage Nom & Prix uniquement) ---
    function handleReferenceChange(selectElement) {
        const row = selectElement.closest('.line-item');
        const manualInput = row.querySelector('.manual-ref-input');
        const nameInput = row.querySelector('.product-name-input');
        const priceInput = row.querySelector('.unit-price');
        
        const selectedOption = selectElement.options[selectElement.selectedIndex];

        if (selectElement.value === 'other') {
            manualInput.style.display = 'block';
            manualInput.value = '';
            manualInput.required = true;
            nameInput.value = '';
            priceInput.value = '';
        } else {
            manualInput.style.display = 'none';
            manualInput.required = false;
            manualInput.value = selectElement.value; 

            // On remplit la désignation et le prix
            nameInput.value = selectedOption.dataset.name || '';
            let rawPrice = parseFloat(selectedOption.dataset.price) || 0;
            priceInput.value = formatNumber(rawPrice);
            
            // NOTE : L'image n'est PAS pré-remplie ici comme demandé.
        }
        calculateTotals();
    }

    // --- Calcul des totaux ---
    function calculateTotals() {
        let globalHT = 0;
        document.querySelectorAll('.line-item').forEach(row => {
            const price = unformatNumber(row.querySelector('.unit-price').value);
            const qty = parseFloat(row.querySelector('.quantity-input').value) || 0;
            const rowTotal = price * qty;
            
            row.querySelector('.total-ht-row').value = formatNumber(rowTotal);
            globalHT += rowTotal;
        });

        const discountPct = parseFloat(document.getElementById('discount').value) || 0;
        const taxRate = parseFloat(document.getElementById('tax_rate').value) || 0;
        
        const discountAmt = (globalHT * discountPct) / 100;
        const totalHtva = Math.max(0, globalHT - discountAmt);
        const totalTva = totalHtva * taxRate;
        const totalTtc = totalHtva + totalTva;

        document.getElementById('montant_global').value = formatNumber(globalHT);
        document.getElementById('total_htva').value = formatNumber(totalHtva);
        document.getElementById('total_tva').value = formatNumber(totalTva);
        document.getElementById('total_ttc').value = formatNumber(totalTtc);
    }

    // --- Gestion des conditions (Validité/Livraison) ---
    function setupToggleOther(selectId, otherId, hiddenId) {
        const select = document.getElementById(selectId);
        const otherInput = document.getElementById(otherId);
        const hiddenInput = document.getElementById(hiddenId);

        select.addEventListener('change', function() {
            if (this.value === 'Autres') {
                otherInput.style.display = 'block';
                otherInput.required = true;
            } else {
                otherInput.style.display = 'none';
                otherInput.required = false;
                hiddenInput.value = this.value;
            }
        });
        otherInput.addEventListener('input', () => hiddenInput.value = otherInput.value);
    }

    setupToggleOther('validity_select', 'validity_other', 'validity');
    setupToggleOther('delivery_terms_select', 'delivery_terms_other', 'delivery_terms');

    // --- Template ligne ---
    const getLineTemplate = (idx) => `
        <div class="line-item row align-items-start mb-3" data-index="${idx}">
            <div class="col-md-2 mb-2">
                <label class="form-label text-white">Référence</label>
                <select class="form-control ref-select" name="lines[${idx}][ref_choice]" required>
                    <option value="" disabled selected>Choisir réf.</option>
                    @foreach($products as $product)
                        <option value="{{ $product->reference }}" 
                                data-name="{{ $product->name }}" 
                                data-price="{{ $product->price }}">
                            {{ $product->reference }}
                        </option>
                    @endforeach
                    <option value="other">Autres (Saisie manuelle)</option>
                </select>
                <input type="text" class="form-control mt-2 manual-ref-input" name="lines[${idx}][reference]" placeholder="Saisir référence" style="display:none;">
            </div>
            <div class="col-md-3 mb-2">
                <label class="form-label text-white">Produit (Désignation)</label>
                <input type="text" class="form-control product-name-input" name="lines[${idx}][product_name]" required placeholder="Nom du produit">
            </div>
            <div class="col-md-2 mb-2">
                <label class="form-label text-white">Image</label>
                <div class="d-flex align-items-center">
                    <div class="img-preview-container mr-2" style="width: 40px; height: 40px; background: #eee; border-radius: 4px; overflow: hidden; display: flex; justify-content: center; align-items: center; border: 1px solid #ccc;">
                        <img src="" class="img-preview" style="max-width: 100%; max-height: 100%; display: none;">
                        <i class="fa fa-image text-muted empty-icon"></i>
                    </div>
                    <input type="file" class="form-control-file text-white user-image-input" name="lines[${idx}][image]" accept="image/*" style="font-size: 0.7rem; width: 100px;">
                </div>
            </div>
            <div class="col-md-2 mb-2">
                <label class="form-label text-white">P.U. HT</label>
                <input type="text" class="form-control unit-price formatted-number-input" name="lines[${idx}][unit_price_ht]" required placeholder="0">
            </div>
            <div class="col-md-1 mb-2">
                <label class="form-label text-white">Qté</label>
                <input type="number" step="1" class="form-control quantity-input" name="lines[${idx}][quantity]" required min="1" value="1">
            </div>
            <div class="col-md-1 mb-2">
                <label class="form-label text-white">Total</label>
                <input type="text" class="form-control total-ht-row text-right" readonly style="background-color: #e9ecef; font-weight: bold; padding: 6px 2px; font-size: 0.85rem;">
            </div>
            <div class="col-md-1 mb-2 pt-4 text-center">
                <button type="button" class="btn btn-danger btn-sm remove-line"><i class="fa-solid fa-trash"></i></button>
            </div>
        </div>`;

    // --- Événements délégués ---
    container.addEventListener('input', (e) => {
        if (e.target.classList.contains('unit-price')) {
            applyLiveFormatting(e.target);
            calculateTotals();
        }
        if (e.target.classList.contains('quantity-input')) {
            calculateTotals();
        }
    });

    container.addEventListener('change', (e) => {
        if (e.target.classList.contains('ref-select')) handleReferenceChange(e.target);
        if (e.target.classList.contains('user-image-input')) handleImagePreview(e.target);
    });

    addButton.addEventListener('click', () => {
        container.insertAdjacentHTML('beforeend', getLineTemplate(lineIndex++));
    });

    container.addEventListener('click', (e) => {
        if (e.target.closest('.remove-line')) {
            if (document.querySelectorAll('.line-item').length > 1) {
                e.target.closest('.line-item').remove();
                calculateTotals();
            }
        }
    });

    document.getElementById('discount').addEventListener('input', calculateTotals);

    clientSelect.addEventListener('change', function() {
        const opt = this.options[this.selectedIndex];
        document.getElementById('code_client').value = opt.dataset.code || '';
        document.getElementById('client_address').value = opt.dataset.address || '';
        document.getElementById('payment_terms').value = opt.dataset.payment || '';
    });

    devisForm.addEventListener('submit', function() {
        document.querySelectorAll('.unit-price').forEach(input => {
            input.value = unformatNumber(input.value);
        });
    });

    calculateTotals();
});
</script>

@endsection