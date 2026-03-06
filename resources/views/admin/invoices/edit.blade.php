@extends('admin.layouts.master')

@section('content')

<div class="container-fluid">
    <div class="card shadow mb-4">
        {{-- Card Header --}}
        <div class="card-header py-3" style="background-color:#6c757d; border-bottom: 1px solid white;">
            <h5 class="m-0 font-weight-bold text-white">Modifier la Facture : {{ $invoice->invoice_number }}</h5>
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

            <form action="{{ route('invoices.update', $invoice->id) }}" method="POST" id="invoice-form" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row">
                    {{-- 1. Devis lié --}}
                    <div class="col-md-4 mb-3">
                        <label for="devis_id" class="form-label text-white">Devis lié</label>
                        <select class="form-control" id="devis_id" name="devis_id">
                            <option value="{{ $invoice->devis_id }}" selected>
                                Devis actuel : {{ $invoice->devis->devis_number ?? 'Non lié' }}
                            </option>
                            @foreach($validDevis as $devis) 
                                @if ($devis->id != $invoice->devis_id)
                                    <option value="{{ $devis->id }}" {{ old('devis_id') == $devis->id ? 'selected' : '' }}>
                                        {{ $devis->devis_number }} ({{ $devis->client }})
                                    </option>
                                @endif
                            @endforeach
                        </select>
                    </div>

                    {{-- 2. Date --}}
                    <div class="col-md-4 mb-3">
                        <label for="date_invoice" class="form-label text-white">Date de la facture</label>
                        <input type="date" class="form-control" id="date_invoice" name="date_invoice" 
                               value="{{ old('date_invoice', $invoice->date_invoice->format('Y-m-d')) }}" required>
                    </div>
                    
                    {{-- 3. État --}}
                    <div class="col-md-4 mb-3">
                        <label for="status" class="form-label text-white">État de la Facture</label>
                        <select class="form-control" id="status" name="status" required>
                            @php $currentStatus = old('status', $invoice->status); @endphp
                            <option value="pending" {{ $currentStatus == 'pending' ? 'selected' : '' }}>En attente</option>
                            <option value="sent" {{ $currentStatus == 'sent' ? 'selected' : '' }}>Envoyée</option>
                            <option value="paid" {{ $currentStatus == 'paid' ? 'selected' : '' }}>Payée</option>
                            <option value="partially_paid" {{ $currentStatus == 'partially_paid' ? 'selected' : '' }}>Payée partiellement</option>
                            <option value="canceled" {{ $currentStatus == 'canceled' ? 'selected' : '' }}>Annulée</option>
                        </select>
                    </div>
                </div>
                
                <hr style="border-color: white;">

                {{-- Lignes de facture --}}
                <h6 class="text-white mb-3">Contenu de la facture</h6>
                <div id="lines-container">
                    @php
                        $lines = old('lines', $invoice->lines);
                        if($lines instanceof \Illuminate\Database\Eloquent\Collection) { $lines = $lines->toArray(); }
                    @endphp

                    @foreach($lines as $index => $line)
                        @php 
                            $catalogProduct = $products->firstWhere('reference', $line['reference']); 
                            $isOther = !$catalogProduct;
                        @endphp
                        <div class="line-item row align-items-start mb-3" data-index="{{ $index }}">
                            
                            {{-- Image existante conservée --}}
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

                            {{-- 3. IMAGE (Choix manuel uniquement) --}}
                            <div class="col-md-2 mb-2">
                                <label class="form-label text-white">Image</label>
                                <div class="d-flex align-items-center">
                                    <div class="img-preview-container mr-2" style="width: 40px; height: 40px; background: #eee; border-radius: 4px; overflow: hidden; display: flex; justify-content: center; align-items: center; border: 1px solid #ccc;">
                                        @php
                                            $imgSrc = '';
                                            if(!empty($line['image'])){
                                                $imgSrc = str_starts_with($line['image'], 'catalog:') 
                                                    ? asset('purchaseImages/' . str_replace('catalog:', '', $line['image'])) 
                                                    : asset('storage/' . $line['image']);
                                            }
                                        @endphp
                                        <img src="{{ $imgSrc }}" class="img-preview" style="max-width: 100%; max-height: 100%; {{ empty($imgSrc) ? 'display:none;' : '' }}">
                                        <i class="fa fa-image text-muted empty-icon" style="{{ !empty($imgSrc) ? 'display:none;' : '' }}"></i>
                                    </div>
                                    <input type="file" class="form-control-file text-white user-image-input" name="lines[{{ $index }}][image]" accept="image/*" style="font-size: 0.7rem; width: 100px;">
                                </div>
                            </div>

                            {{-- 4. PRIX --}}
                            <div class="col-md-2 mb-2">
                                <label class="form-label text-white">P.U. HT <span class="text-danger">*</span></label>
                                <input type="text" class="form-control unit-price formatted-number-input" name="lines[{{ $index }}][unit_price_ht]" value="{{ number_format($line['unit_price_ht'], 0, '', ' ') }}" required>
                            </div>

                            {{-- 5. QUANTITÉ --}}
                            <div class="col-md-1 mb-2">
                                <label class="form-label text-white">Qté <span class="text-danger">*</span></label>
                                <input type="number" step="1" class="form-control quantity-input" name="lines[{{ $index }}][quantity]" value="{{ $line['quantity'] }}" required min="1">
                            </div>

                            {{-- 6. TOTAL --}}
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

                {{-- Récapitulatif --}}
                <hr style="border-color: white;">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label class="text-white">Montant global (HT)</label>
                        <input type="text" class="form-control text-right" id="montant_global" readonly value="0" style="font-weight: bold;">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="text-white">Remise (%)</label>
                        <input type="number" step="0.01" min="0" max="100" class="form-control" id="discount" name="discount" value="{{ old('discount', $invoice->discount) }}">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="text-white">TVA (18%)</label>
                        <input type="text" class="form-control text-right" id="total_tva" readonly value="0">
                        <input type="hidden" name="tax_rate" id="tax_rate" value="0.18">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="text-white">Total TTC</label>
                        <input type="text" class="form-control text-right" id="total_ttc" readonly value="0" style="font-weight: bold; background-color: #f8f9fa;">
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col d-flex justify-content-between">
                        <button type="submit" class="btn btn-warning text-white" style="background-color: #f98a1b; border: none;">Mettre à jour la Facture</button>
                        <a href="{{ route('invoices.list') }}" class="btn btn-secondary" style="background-color: rgb(144, 142, 140); border: none;">Annuler</a>
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
    const devisForm = document.getElementById('invoice-form');
    let lineIndex = {{ count($lines) }};

    function formatNumber(v) { return Math.round(v).toString().replace(/\B(?=(\d{3})+(?!\d))/g, " "); }
    function unformatNumber(s) { return parseFloat(s.toString().replace(/\s/g, '')) || 0; }

    function applyLiveFormatting(input) {
        let value = input.value.replace(/\D/g, ''); 
        if (value === "") { input.value = ""; return; }
        input.value = formatNumber(parseFloat(value));
    }

    function handleImagePreview(inputElement) {
        const row = inputElement.closest('.line-item');
        const preview = row.querySelector('.img-preview');
        const icon = row.querySelector('.empty-icon');
        if (inputElement.files && inputElement.files[0]) {
            const reader = new FileReader();
            reader.onload = f => { preview.src = f.target.result; preview.style.display = 'block'; icon.style.display = 'none'; }
            reader.readAsDataURL(inputElement.files[0]);
        }
    }

    function handleReferenceChange(selectElement) {
        const row = selectElement.closest('.line-item');
        const manualInput = row.querySelector('.manual-ref-input');
        const nameInput = row.querySelector('.product-name-input');
        const priceInput = row.querySelector('.unit-price');
        const selectedOption = selectElement.options[selectElement.selectedIndex];

        if (selectElement.value === 'other') {
            manualInput.style.display = 'block'; manualInput.required = true;
            nameInput.value = ''; priceInput.value = '';
        } else {
            manualInput.style.display = 'none'; manualInput.required = false;
            manualInput.value = selectElement.value;
            nameInput.value = selectedOption.dataset.name || '';
            priceInput.value = formatNumber(parseFloat(selectedOption.dataset.price) || 0);
        }
        calculateTotals();
    }

    function calculateTotals() {
        let globalHT = 0;
        document.querySelectorAll('.line-item').forEach(row => {
            const price = unformatNumber(row.querySelector('.unit-price').value);
            const qty = parseFloat(row.querySelector('.quantity-input').value) || 0;
            const total = price * qty;
            row.querySelector('.total-ht-row').value = formatNumber(total);
            globalHT += total;
        });
        const discountPct = parseFloat(document.getElementById('discount').value) || 0;
        const totalHtva = globalHT - (globalHT * discountPct / 100);
        const tva = totalHtva * 0.18;
        document.getElementById('montant_global').value = formatNumber(globalHT);
        document.getElementById('total_tva').value = formatNumber(tva);
        document.getElementById('total_ttc').value = formatNumber(totalHtva + tva);
    }

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
                    <option value="other">Autres</option>
                </select>
                <input type="text" class="form-control mt-2 manual-ref-input" name="lines[${idx}][reference]" style="display:none;">
            </div>
            <div class="col-md-3 mb-2">
                <label class="form-label text-white">Produit</label>
                <input type="text" class="form-control product-name-input" name="lines[${idx}][product_name]" required>
            </div>
            <div class="col-md-2 mb-2">
                <label class="form-label text-white">Image</label>
                <div class="d-flex align-items-center">
                    <div class="img-preview-container mr-2" style="width: 40px; height: 40px; background: #eee; border: 1px solid #ccc; border-radius: 4px; overflow: hidden; display: flex; justify-content: center; align-items: center;">
                        <img src="" class="img-preview" style="max-width: 100%; max-height: 100%; display: none;">
                        <i class="fa fa-image text-muted empty-icon"></i>
                    </div>
                    <input type="file" class="form-control-file text-white user-image-input" name="lines[${idx}][image]" accept="image/*" style="font-size: 0.7rem; width: 100px;">
                </div>
            </div>
            <div class="col-md-2 mb-2">
                <label class="form-label text-white">P.U. HT</label>
                <input type="text" class="form-control unit-price formatted-number-input" name="lines[${idx}][unit_price_ht]" required>
            </div>
            <div class="col-md-1 mb-2">
                <label class="form-label text-white">Qté</label>
                <input type="number" class="form-control quantity-input" name="lines[${idx}][quantity]" value="1" required min="1">
            </div>
            <div class="col-md-1 mb-2">
                <label class="form-label text-white">Total</label>
                <input type="text" class="form-control total-ht-row text-right" readonly style="background-color: #e9ecef; font-weight: bold;">
            </div>
            <div class="col-md-1 mb-2 pt-4 text-center">
                <button type="button" class="btn btn-danger btn-sm remove-line"><i class="fa-solid fa-trash"></i></button>
            </div>
        </div>`;

    container.addEventListener('input', e => {
        if (e.target.classList.contains('unit-price')) { applyLiveFormatting(e.target); calculateTotals(); }
        if (e.target.classList.contains('quantity-input')) { calculateTotals(); }
    });
    container.addEventListener('change', e => {
        if (e.target.classList.contains('ref-select')) handleReferenceChange(e.target);
        if (e.target.classList.contains('user-image-input')) handleImagePreview(e.target);
    });
    addButton.addEventListener('click', () => { container.insertAdjacentHTML('beforeend', getLineTemplate(lineIndex++)); });
    container.addEventListener('click', e => {
        if (e.target.closest('.remove-line')) { e.target.closest('.line-item').remove(); calculateTotals(); }
    });
    document.getElementById('discount').addEventListener('input', calculateTotals);
    devisForm.addEventListener('submit', () => {
        document.querySelectorAll('.unit-price').forEach(i => { i.value = unformatNumber(i.value); });
    });

    calculateTotals();
});
</script>

@endsection