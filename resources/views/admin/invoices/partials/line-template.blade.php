<div class="line-item row align-items-end mb-2" data-index="INDEX">
    {{-- 1. RÉFÉRENCE (col-md-2) --}}
    <div class="col-md-2 mb-2">
        <label class="form-label text-white">Référence</label>
        <input type="text" class="form-control"
               name="lines[INDEX][reference]"
               placeholder="Réf."
               value="">
    </div>

    {{-- 2. PRODUIT (col-md-3) --}}
    <div class="col-md-3 mb-2">
        <label class="form-label text-white">Produit</label>
        <input type="text" class="form-control product-name-input"
               name="lines[INDEX][product_name]"
               placeholder="Nom du produit"
               value=""
               required>
    </div>

    {{-- 3. IMAGE (col-md-2) --}}
    <div class="col-md-2 mb-2">
        <label class="form-label text-white">Image</label>
        <div class="d-flex align-items-center">
            {{-- Petit aperçu visuel --}}
            <div class="img-preview-container mr-2" style="width: 40px; height: 40px; background: #eee; border-radius: 4px; overflow: hidden; display: flex; justify-content: center; align-items: center; border: 1px solid #ccc;">
                <img src="" class="img-preview" style="max-width: 100%; max-height: 100%; display: none;">
                <i class="fa fa-image text-muted empty-icon"></i>
            </div>
            {{-- Input fichier --}}
            <input type="file" class="form-control-file text-white user-image-input" 
                   name="lines[INDEX][image]" 
                   accept="image/*" 
                   style="font-size: 0.7rem; width: 100px;">
        </div>
    </div>

    {{-- 4. PRIX UNITAIRE HT (col-md-2) --}}
    <div class="col-md-2 mb-2">
        <label class="form-label text-white">P.U. HT</label>
        <input type="number" step="0.01" class="form-control unit-price" 
               name="lines[INDEX][unit_price_ht]" 
               placeholder="0.00" 
               value="" 
               required min="0">
    </div>

    {{-- 5. QUANTITÉ (col-md-1) --}}
    <div class="col-md-1 mb-2">
        <label class="form-label text-white">Qté</label>
        <input type="number" step="1" class="form-control quantity-input" 
               name="lines[INDEX][quantity]" 
               placeholder="1" 
               value="1" 
               required min="1">
    </div>

    {{-- 6. TOTAL HT (col-md-1) --}}
    <div class="col-md-1 mb-2">
        <label class="form-label text-white">Total</label>
        {{-- Ce champ est en lecture seule et est mis à jour par JS --}}
        <input type="text" class="form-control total-ht-row text-right" 
               name="lines[INDEX][total_ht]" 
               value="0.00" 
               readonly style="background-color: #e9ecef; font-weight: bold; padding: 6px 2px; font-size: 0.85rem;">
    </div>

    {{-- 7. BOUTON SUPPRESSION (col-md-1) --}}
    <div class="col-md-1 mb-2 text-center">
        <label class="form-label text-white">&nbsp;</label>
        <button type="button" class="btn btn-danger btn-sm remove-line" title="Supprimer">
            <i class="fa-solid fa-trash"></i>
        </button>
    </div>
</div>