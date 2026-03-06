@extends('admin.layouts.master')

@section('content')

<div class="container-fluid">
    <div class="card shadow mb-4">
        {{-- Card Header --}}
        <div class="card-header py-3" style="background-color:#6c757d;">
            <h5 class="m-0 font-weight-bold text-white">Modifier le devis #{{ $devis->devis_number }}</h5>
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

            <form action="{{ route('devis.update', $devis->id) }}" method="POST" id="devis-form" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                {{-- SECTION 1: Client / Adresse / Date / État --}}
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="client" class="form-label text-white">Client <span class="text-danger">*</span></label>
                            <select class="form-control" id="client" name="client" required>
                                <option value="" disabled>Choisir un client</option>
                                @foreach($customers as $customer)
                                    <option 
                                        value="{{ $customer->name }}" 
                                        data-code="{{ $customer->code_client }}" 
                                        data-payment="{{ $customer->payment_deadline }}"
                                        data-address="{{ $customer->address }}"
                                        {{ old('client', $devis->client) === $customer->name ? 'selected' : '' }}>
                                        {{ $customer->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="client_address" class="form-label text-white">Adresse du client</label>
                            <textarea class="form-control" id="client_address" name="client_address" rows="3">{{ old('client_address', $devis->client_address) }}</textarea>
                        </div>
                    </div>

                    <div class="col-md-4"></div>

                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="date_devis" class="form-label text-white">Date du devis <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="date_devis" name="date_devis" value="{{ old('date_devis', $devis->date_devis->format('Y-m-d')) }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="status" class="form-label text-white">État du devis <span class="text-danger">*</span></label>
                            @php
                                $statusesMap = ['Envoyé' => 'Envoyé', 'Accepté' => 'Accepté', 'Annulé' => 'Annulé', 'Abandonné' => 'Abandonné'];
                                $dbToFrStatusMap = ['sent' => 'Envoyé', 'accepted' => 'Accepté', 'rejected' => 'Annulé', 'abandoned' => 'Abandonné'];
                                $currentStatusValue = $dbToFrStatusMap[$devis->status] ?? $devis->status;
                                $selectedStatus = old('status', $currentStatusValue);
                            @endphp
                            <select class="form-control" id="status" name="status" required>
                                @foreach ($statusesMap as $val => $label)
                                    <option value="{{ $val }}" {{ $selectedStatus === $val ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <hr style="border-color: #8c8f92;">

                {{-- SECTION 2: Conditions du devis --}}
                <h6 class="text-white mb-3">Conditions du devis</h6>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="validity_select" class="form-label text-white">Validité de l'Offre <span class="text-danger">*</span></label>
                        @php
                            $validity_options = ['30 jours','60 jours','90 jours'];
                            $current_validity = old('validity', $devis->validity);
                            $is_validity_other = !empty($current_validity) && !in_array($current_validity, $validity_options);
                        @endphp
                        <select class="form-control" id="validity_select" required>
                            <option value="" disabled>Choisir une validité</option>
                            @foreach ($validity_options as $option)
                                <option value="{{ $option }}" {{ $current_validity === $option ? 'selected' : '' }}>{{ $option }}</option>
                            @endforeach
                            <option value="Autres" {{ $is_validity_other ? 'selected' : '' }}>Autres</option>
                        </select>
                        <input type="text" class="form-control mt-2" id="validity_other" placeholder="Saisir la validité..." value="{{ $is_validity_other ? $current_validity : '' }}" style="{{ $is_validity_other ? 'display:block;' : 'display:none;' }}">
                        <input type="hidden" id="validity" name="validity" value="{{ $current_validity }}">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="delivery_terms_select" class="form-label text-white">Délai de livraison <span class="text-danger">*</span></label>
                        @php
                            $delivery_options = ['Article(s) disponible(s)','Une semaine','Deux semaines','Trois semaines','Quatre semaines','Deux mois','Trois mois'];
                            $current_delivery = old('delivery_terms', $devis->delivery_terms);
                            $is_delivery_other = !empty($current_delivery) && !in_array($current_delivery, $delivery_options);
                        @endphp
                        <select class="form-control" id="delivery_terms_select" required>
                            <option value="" disabled>Choisir un délai</option>
                            @foreach ($delivery_options as $option)
                                <option value="{{ $option }}" {{ $current_delivery === $option ? 'selected' : '' }}>{{ $option }}</option>
                            @endforeach
                            <option value="Autres" {{ $is_delivery_other ? 'selected' : '' }}>Autres</option>
                        </select>
                        <input type="text" class="form-control mt-2" id="delivery_terms_other" placeholder="Saisir le délai..." value="{{ $is_delivery_other ? $current_delivery : '' }}" style="{{ $is_delivery_other ? 'display:block;' : 'display:none;' }}">
                        <input type="hidden" id="delivery_terms" name="delivery_terms" value="{{ $current_delivery }}">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="tax_rate" class="form-label text-white">Taux de TVA</label>
                        <input type="number" step="0.01" class="form-control" id="tax_rate" name="tax_rate" value="{{ old('tax_rate', $devis->tax_rate) }}">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="code_client" class="form-label text-white">Code client</label>
                        <input type="text" class="form-control" id="code_client" name="code_client" value="{{ old('code_client', $devis->code_client) }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="delivery_location" class="form-label text-white">Lieu de livraison</label>
                        <input type="text" class="form-control" id="delivery_location" name="delivery_location" value="{{ old('delivery_location', $devis->delivery_location) }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="payment_terms" class="form-label text-white">Conditions de règlement</label>
                        <input type="text" class="form-control" id="payment_terms" name="payment_terms" value="{{ old('payment_terms', $devis->payment_terms) }}">
                    </div>
                </div>

                <hr style="border-color: white;">

                {{-- SECTION 3: Lignes du devis --}}
                <h6 class="text-white">Contenu du devis</h6>
                <div id="lines-container">
                    @php
                        $lines = old('lines', $devis->lines);
                        if($lines instanceof \Illuminate\Database\Eloquent\Collection) { $lines = $lines->toArray(); }
                    @endphp

                    @foreach($lines as $index => $line)
                        @php 
                            $catalogProduct = $products->firstWhere('reference', $line['reference']); 
                            $isOther = !$catalogProduct;
                        @endphp
                        <div class="line-item row align-items-start mb-3" data-index="{{ $index }}">
                            
                            {{-- Champ caché pour conserver l'image si on ne la change pas --}}
                            <input type="hidden" name="lines[{{ $index }}][old_image]" value="{{ $line['image'] ?? '' }}">

                            {{-- 1. RÉFÉRENCE (Select + Manuel) --}}
                            <div class="col-md-2 mb-2">
                                <label class="form-label text-white">Référence <span class="text-danger">*</span></label>
                                <select class="form-control ref-select" name="lines[{{ $index }}][ref_choice]" required>
                                    <option value="" disabled>Choisir réf.</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->reference }}" 
                                                data-name="{{ $product->name }}" 
                                                data-price="{{ $product->price }}"
                                                {{ $line['reference'] == $product->reference ? 'selected' : '' }}>
                                            {{ $product->reference }}
                                        </option>
                                    @endforeach
                                    <option value="other" {{ $isOther ? 'selected' : '' }}>Autres (Saisie manuelle)</option>
                                </select>
                                <input type="text" class="form-control mt-2 manual-ref-input" name="lines[{{ $index }}][reference]" value="{{ $line['reference'] }}" placeholder="Saisir référence" style="{{ $isOther ? '' : 'display:none;' }}">
                            </div>

                            {{-- 2. PRODUIT (Désignation) --}}
                            <div class="col-md-3 mb-2">
                                <label class="form-label text-white">Produit (Désignation) <span class="text-danger">*</span></label>
                                <input type="text" class="form-control product-name-input" name="lines[{{ $index }}][product_name]" value="{{ $line['product_name'] }}" required placeholder="Nom du produit">
                            </div>

                            {{-- 3. IMAGE (Manuel uniquement) --}}
                            <div class="col-md-2 mb-2">
                                <label class="form-label text-white">Image</label>
                                <div class="d-flex align-items-center">
                                    <div class="img-preview-container mr-2" style="width: 40px; height: 40px; background: #eee; border-radius: 4px; overflow: hidden; display: flex; justify-content: center; align-items: center; border: 1px solid #ccc;">
                                        @php
                                            $imgSrc = '';
                                            if(!empty($line['image'])){
                                                if(str_starts_with($line['image'], 'catalog:')){
                                                    $imgSrc = asset('purchaseImages/' . str_replace('catalog:', '', $line['image']));
                                                } else {
                                                    $imgSrc = asset('storage/' . $line['image']);
                                                }
                                            }
                                        @endphp
                                        <img src="{{ $imgSrc }}" class="img-preview" style="max-width: 100%; max-height: 100%; {{ empty($imgSrc) ? 'display:none;' : '' }}">
                                        <i class="fa fa-image text-muted empty-icon" style="{{ !empty($imgSrc) ? 'display:none;' : 'block' }}"></i>
                                    </div>
                                    <input type="file" class="form-control-file text-white user-image-input" name="lines[{{ $index }}][image]" accept="image/*" style="font-size: 0.7rem; width: 100px;">
                                </div>
                            </div>

                            {{-- 4. PRIX UNITAIRE --}}
                            <div class="col-md-2 mb-2">
                                <label class="form-label text-white">P.U. HT <span class="text-danger">*</span></label>
                                <input type="text" class="form-control unit-price formatted-number-input" name="lines[{{ $index }}][unit_price_ht]" value="{{ number_format($line['unit_price_ht'], 0, '', ' ') }}" required placeholder="0">
                            </div>

                            {{-- 5. QUANTITÉ --}}
                            <div class="col-md-1 mb-2">
                                <label class="form-label text-white">Qté <span class="text-danger">*</span></label>
                                <input type="number" step="1" class="form-control quantity-input" name="lines[{{ $index }}][quantity]" value="{{ $line['quantity'] }}" required min="1">
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

                {{-- SECTION 4: Récapitulatif --}}
                <hr style="border-color: white;">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="text-white">Montant global (HT)</label>
                        <input type="text" class="form-control text-right" id="montant_global" readonly value="0" style="font-weight: bold;">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="text-white">Remise (%)</label>
                        <input type="number" step="0.01" min="0" max="100" class="form-control" id="discount" name="discount" value="{{ old('discount', $devis->discount) }}">
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
                        <button type="submit" class="btn btn-warning text-white" style="background-color: #f98a1b; border: none;">Mettre à jour le devis</button>
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
    const discountInput = document.getElementById('discount');
    const taxRateInput = document.getElementById('tax_rate');
    
    let lineIndex = {{ count($lines) }};

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
        let rawValue = input.value.replace(/\D/g, ''); 
        if (rawValue === "") {
            input.value = "";
            return;
        }
        input.value = formatNumber(parseFloat(rawValue));
    }

    // --- Aperçu de l'image (Upload manuel) ---
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

    // --- Gestion du changement de Référence ---
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

            nameInput.value = selectedOption.dataset.name || '';
            let rawPrice = parseFloat(selectedOption.dataset.price) || 0;
            priceInput.value = formatNumber(rawPrice);
            
            // L'image n'est pas modifiée ici par choix manuel.
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

        const discountPct = parseFloat(discountInput.value) || 0;
        const taxRate = parseFloat(taxRateInput.value) || 0;
        
        const discountAmt = (globalHT * discountPct) / 100;
        const totalHtva = Math.max(0, globalHT - discountAmt);
        const totalTva = totalHtva * taxRate;
        const totalTtc = totalHtva + totalTva;

        document.getElementById('montant_global').value = formatNumber(globalHT);
        document.getElementById('total_htva').value = formatNumber(totalHtva);
        document.getElementById('total_tva').value = formatNumber(totalTva);
        document.getElementById('total_ttc').value = formatNumber(totalTtc);
    }

    // --- Template ligne adaptée ---
    const getLineTemplate = (idx) => `
        <div class="line-item row align-items-start mb-3" data-index="${idx}">
            <input type="hidden" name="lines[${idx}][old_image]" value="">
            <div class="col-md-2 mb-2">
                <label class="form-label text-white">Référence</label>
                <select class="form-control ref-select" name="lines[${idx}][ref_choice]" required>
                    <option value="" disabled selected>Choisir réf.</option>
                    @foreach($products as $product)
                        <option value="{{ $product->reference }}" data-name="{{ $product->name }}" data-price="{{ $product->price }}">{{ $product->reference }}</option>
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

    discountInput.addEventListener('input', calculateTotals);
    taxRateInput.addEventListener('input', calculateTotals);

    devisForm.addEventListener('submit', function() {
        document.querySelectorAll('.unit-price').forEach(input => {
            input.value = unformatNumber(input.value);
        });
    });

    calculateTotals();
});
</script>

@endsection