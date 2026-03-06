<div class="line-item row align-items-end mb-2" data-index="INDEX">
    {{-- Produit (col-md-2) --}}
    <div class="col-md-2 mb-2">
        <label class="form-label text-white">Produit</label>
        <input type="text" class="form-control"
               name="lines[INDEX][product_name]"
               placeholder="Nom du produit"
               value=""
               required>
    </div>

    {{-- Référence (NOUVEAU - col-md-2) --}}
    <div class="col-md-2 mb-2">
        <label class="form-label text-white">Référence</label>
        <input type="text" class="form-control"
               name="lines[INDEX][reference]"
               placeholder="Ex: REF-001"
               value="">
    </div>

    {{-- Image (NOUVEAU - col-md-2) --}}
    <div class="col-md-2 mb-2">
        <label class="form-label text-white">Image (optionnel)</label>
        {{-- Input type file pour l'image --}}
        <input type="file" class="form-control-file text-white" 
               name="lines[INDEX][image]" 
               accept="image/*"
               style="font-size: 0.8rem;">
    </div>

    {{-- Prix unitaire HT (col-md-2) --}}
    <div class="col-md-2 mb-2">
        <label class="form-label text-white">Prix unitaire HT</label>
        <input type="text" class="form-control unit-price formatted-number-input" 
               name="lines[INDEX][unit_price_ht]" 
               placeholder="0" 
               value="" 
               required>
    </div>

    {{-- Quantité (col-md-1) --}}
    <div class="col-md-1 mb-2">
        <label class="form-label text-white">Qté</label>
        <input type="number" step="1" class="form-control quantity-input" 
               name="lines[INDEX][quantity]" 
               placeholder="1" 
               value="1" 
               required min="1">
    </div>

    {{-- Total HT (col-md-2) --}}
    <div class="col-md-2 mb-2">
        <label class="form-label text-white">Total HT</label>
        <input type="text" class="form-control total-ht-row text-right" 
               name="lines[INDEX][total_ht]" 
               value="0.00" 
               readonly style="background-color: #e9ecef; font-weight: bold;">
    </div>

    {{-- Bouton de suppression (col-md-1) --}}
    <div class="col-md-1 mb-2 text-center">
        <label class="form-label text-white">&nbsp;</label>
        <button type="button" class="btn btn-danger btn-sm remove-line" title="Supprimer">
            <i class="fa-solid fa-trash"></i>
        </button>
    </div>
</div>