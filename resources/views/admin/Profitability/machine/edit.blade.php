@extends('admin.layouts.master')

@section('content')
<div class="container-fluid">
  <div class="card shadow col-md-10 mx-auto">
    <div class="card-header text-white" style="background-color:#6c757d; margin-left: -12px; margin-right: -12px;">
      <h5 class="m-0 font-weight-bold">Modifier la rentabilité</h5>
    </div>

    {{-- Affichage des erreurs --}}
    @if ($errors->any())
      <div class="alert alert-danger mx-3 my-3">
        <ul class="mb-0">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form action="{{ route('machineUpdate') }}" method="POST" enctype="multipart/form-data" id="profitabilityForm">
      @method('PUT')
      <input type="hidden" name="id" value="{{ $profitability->id }}">
      @csrf

      <div class="card-body" style="background-color:#6c757d; margin-left: -12px; margin-right: -12px;">
        <div class="row">
          <!-- Colonne 1 -->
          <div class="col-md-6">
            <!-- Référence d'achat -->
            <div class="mb-3">
              <label for="purchase_reference" class="text-white fw-bold">Référence pièce <span class="text-danger">*</span></label>
              <select name="purchase_reference" id="purchase_reference" class="form-control @error('purchase_reference') is-invalid @enderror" required>
                <option value="">Sélectionnez une référence</option>
                @foreach($purchases as $purchase)
                  <option value="{{ $purchase->reference }}" {{ old('purchase_reference', $profitability->purchase_reference) == $purchase->reference ? 'selected' : '' }}>
                    {{ $purchase->reference }} - {{ $purchase->suppliername ?? 'Sans fournisseur' }}
                  </option>
                @endforeach
              </select>
              @error('purchase_reference')
                <small class="invalid-feedback">{{ $message }}</small>
              @enderror
            </div>

            <!-- Fournisseur (affichage) -->
            <div class="mb-3">
              <label class="text-white fw-bold">Fournisseur</label>
              <input type="text" id="suppliername" class="form-control" readonly value="{{ old('suppliername', $profitability->suppliername) }}">
              <input type="hidden" name="suppliername" id="suppliername_hidden" value="{{ old('suppliername', $profitability->suppliername) }}">
            </div>

            <!-- Request -->
            <div class="mb-3">
              <label for="request" class="form-label text-white fw-bold">Request</label>
              <input type="text" name="request" id="request" class="form-control @error('request') is-invalid @enderror" value="{{ old('request', $profitability->request) }}">
              @error('request')
                <small class="invalid-feedback">{{ $message }}</small>
              @enderror
            </div>

            <!-- Date de rentabilité -->
            <div class="mb-3">
                <label for="date_profitability" class="form-label text-white fw-bold">Date de rentabilité <span class="text-danger">*</span></label>
                <input type="date" name="date_profitability" id="date_profitability" class="form-control @error('date_profitability') is-invalid @enderror" value="{{ old('date_profitability', $profitability->date_profitability ? $profitability->date_profitability->format('Y-m-d') : '') }}" required>
                @error('date_profitability')
                  <small class="invalid-feedback">{{ $message }}</small>
                @enderror
            </div>

            <!-- Prix d'achat unité (readonly) -->
            <div class="mb-3">
              <label class="text-white fw-bold">Prix d'achat unité</label>
              <input type="text" id="purchase_price" class="form-control" readonly style="background-color: #e9ecef;">
              <input type="hidden" name="unit_purchase_price" id="unit_purchase_price_hidden" value="{{ old('unit_purchase_price', $profitability->unit_purchase_price) }}">
            </div>

            <!-- Poids (KG) readonly -->
            <div class="mb-3">
              <label class="text-white fw-bold">Poids (KG)</label>
              <input type="text" id="purchase_weight" class="form-control" readonly style="background-color: #e9ecef;">
              <input type="hidden" name="weight" id="weight_hidden" value="{{ old('weight', $profitability->weight) }}">
            </div>

            <!-- Marque readonly -->
            <div class="mb-3">
              <label class="text-white fw-bold">Marque</label>
              <input type="text" id="purchase_brand" class="form-control" readonly style="background-color: #e9ecef;">
              <input type="hidden" name="brand" id="brand_hidden" value="{{ old('brand', $profitability->brand) }}">
            </div>

            <!-- Etat readonly -->
            <div class="mb-3">
              <label class="text-white fw-bold">État</label>
              <input type="text" id="purchase_status" class="form-control" readonly style="background-color: #e9ecef;">
              <input type="hidden" name="status" id="status_hidden" value="{{ old('status', $profitability->status) }}">
            </div>

            <!-- Taux de conversion -->
            <div class="mb-3">
              <label for="conversion_rate" class="form-label text-white fw-bold">Taux de conversion</label>
              <input type="text" name="conversion_rate" id="conversion_rate" class="form-control @error('conversion_rate') is-invalid @enderror format-number" value="{{ old('conversion_rate', $profitability->conversion_rate) }}">
              @error('conversion_rate')
                <small class="invalid-feedback">{{ $message }}</small>
              @enderror
            </div>

            <!-- Bénéfice (Déplacé ici) -->
            <div class="mb-3">
                <label for="benefit" class="form-label text-white fw-bold">Bénéfice</label>
                <input type="text" id="benefit" class="form-control font-weight-bold" readonly style="background-color: #e9ecef;">
            </div>
          </div>

          <!-- Colonne 2 -->
          <div class="col-md-6">
            <!-- Nombre d'item (Déplacé ici) -->
            <div class="mb-3">
                <label for="quantity" class="form-label text-white fw-bold">Nombre d'item</label>
                <input type="number" name="quantity" id="quantity" class="form-control @error('quantity') is-invalid @enderror" value="{{ old('quantity', $profitability->quantity ?? 1) }}" min="1">
                @error('quantity')
                  <small class="invalid-feedback">{{ $message }}</small>
                @enderror
            </div>

            <!-- Transport urbain global -->
            <div class="mb-3">
              <label for="global_urbain_transport" class="form-label text-white fw-bold">Transport urbain global</label>
              <input type="text" name="global_urbain_transport" id="global_urbain_transport" class="form-control @error('global_urbain_transport') is-invalid @enderror format-number-no-decimal" value="{{ old('global_urbain_transport', $profitability->global_urbain_transport) }}">
              @error('global_urbain_transport')
                <small class="invalid-feedback">{{ $message }}</small>
              @enderror
            </div>

            <!-- Transport urbain (calculé) readonly -->
            <div class="mb-3">
              <label for="urban_transport" class="form-label text-white fw-bold">Transport urbain (par item)</label>
              <input type="text" name="urban_transport" id="urban_transport" class="form-control" readonly value="{{ old('urban_transport', $profitability->urban_transport) }}" style="background-color: #e9ecef;">
            </div>

            <!-- Concierge -->
            <div class="mb-3">
              <label for="concierge" class="form-label text-white fw-bold">Concierge</label>
              <input type="text" name="concierge" id="concierge" class="form-control @error('concierge') is-invalid @enderror format-number" value="{{ old('concierge', $profitability->concierge) }}">
              @error('concierge')
                <small class="invalid-feedback">{{ $message }}</small>
              @enderror
            </div>

            <!-- Transport Source/BKO -->
            <div class="mb-3">
              <label for="transport_source_bko" class="form-label text-white fw-bold">Transport Source/BKO</label>
              <input type="text" name="transport_source_bko" id="transport_source_bko" class="form-control @error('transport_source_bko') is-invalid @enderror format-number" value="{{ old('transport_source_bko', $profitability->transport_source_bko) }}">
              @error('transport_source_bko')
                <small class="invalid-feedback">{{ $message }}</small>
              @enderror
            </div>

            <!-- Droits de douane -->
            <div class="mb-3">
              <label for="customs" class="form-label text-white fw-bold">Droits de douane (%)</label>
              <input type="text" name="customs" id="customs" class="form-control @error('customs') is-invalid @enderror format-number" value="{{ old('customs', $profitability->customs) }}">
              @error('customs')
                <small class="invalid-feedback">{{ $message }}</small>
              @enderror
            </div>

            <!-- Transport terrestre -->
            <div class="mb-3">
              <label for="land_transport" class="form-label text-white fw-bold">Transport terrestre</label>
              <input type="text" name="land_transport" id="land_transport" class="form-control @error('land_transport') is-invalid @enderror format-number" value="{{ old('land_transport', $profitability->land_transport) }}">
              @error('land_transport')
                <small class="invalid-feedback">{{ $message }}</small>
              @enderror
            </div>

            <!-- Marge (%) -->
            <div class="mb-3">
              <label for="margin" class="form-label text-white fw-bold">Marge (%) <span class="text-danger">*</span></label>
              <input type="text" name="margin" id="margin" class="form-control @error('margin') is-invalid @enderror format-number" value="{{ old('margin', $profitability->margin) }}">
              @error('margin')
                <small class="invalid-feedback">{{ $message }}</small>
              @enderror
            </div>

            <!-- Financement -->
            <div class="mb-3">
              <label for="funding" class="form-label text-white fw-bold">Financement</label>
              <input type="text" name="funding" id="funding" class="form-control @error('funding') is-invalid @enderror format-number" value="{{ old('funding', $profitability->funding) }}">
              @error('funding')
                <small class="invalid-feedback">{{ $message }}</small>
              @enderror
            </div>

            <!-- Prix de vente -->
            <div class="mb-3">
              <label for="selling_price" class="form-label text-white fw-bold">Prix de vente</label>
              <input type="text" id="selling_price" name="selling_price" class="form-control font-weight-bold" readonly style="background-color: #e9ecef;">
            </div>
          </div>
        </div>

        <div class="mt-3 d-flex justify-content-between align-items-center">
          <button type="submit" class="btn btn-success" style="background-color: rgb(249, 138, 27); border: none;">Enregistrer les modifications</button>
          <button type="button" id="btn_show_price" class="btn btn-info" style="background-color: rgb(144, 142, 140); border: none;">Calculer les prix</button>
          <a href="{{ route('machineList') }}" class="btn btn-secondary" style="background-color: rgb(144, 142, 140); border: 1.5px solid rgba(255, 0, 0, 0.5);">Annuler</a>
        </div>
      </div>
    </form>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const purchases = @json($purchases->keyBy('reference'));
  const profitability = @json($profitability);
  const purchaseReferenceSelect = document.getElementById('purchase_reference');

  const purchasePriceInput = document.getElementById('purchase_price');
  const purchaseWeightInput = document.getElementById('purchase_weight');
  const purchaseBrandInput = document.getElementById('purchase_brand');
  const purchaseStatusInput = document.getElementById('purchase_status');

  const supplierNameInput = document.getElementById('suppliername');
  const supplierNameHidden = document.getElementById('suppliername_hidden');

  const unitPurchasePriceHidden = document.getElementById('unit_purchase_price_hidden');
  const weightHidden = document.getElementById('weight_hidden');
  const brandHidden = document.getElementById('brand_hidden');
  const statusHidden = document.getElementById('status_hidden');

  const sellingPriceInput = document.getElementById('selling_price');
  const benefitInput = document.getElementById('benefit');
  const fundingInput = document.getElementById('funding');
  const showPriceButton = document.getElementById('btn_show_price');

  const globalUrbainTransportInput = document.getElementById('global_urbain_transport');
  const urbanTransportInput = document.getElementById('urban_transport');
  const quantityInput = document.getElementById('quantity');

  function formatValue(num) {
    if (num === null || num === undefined || num === '') return '';
    num = parseFloat(num);
    if (num > 1) {
      return Math.round(num).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
    } else {
      return num.toFixed(2);
    }
  }

  function formatValueWithoutDecimal(num) {
    if (!num || isNaN(num)) return '';
    return Math.round(num).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
  }

  function formatInputNumber(input, noDecimal = false) {
    let val = input.value.replace(/\s+/g, '');
    if (!isNaN(val) && val !== '') {
      let num = parseFloat(val);
      if (noDecimal || num > 1) {
        input.value = Math.round(num).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
      } else {
        input.value = num.toFixed(2);
      }
    } else {
      input.value = '';
    }
  }

  function updatePurchaseDetails(ref) {
    if (purchases[ref]) {
      const purchase = purchases[ref];
      purchasePriceInput.value = formatValue(purchase.purchaseprice ?? '') + ' ' + (purchase.currency ?? '');
      unitPurchasePriceHidden.value = purchase.purchaseprice ?? '';
      purchaseWeightInput.value = formatValue(purchase.weight ?? '');
      weightHidden.value = purchase.weight ?? '';
      purchaseBrandInput.value = purchase.brand ?? '';
      brandHidden.value = purchase.brand ?? '';
      purchaseStatusInput.value = purchase.status ?? '';
      statusHidden.value = purchase.status ?? '';
      
      const sName = purchase.suppliername ?? '';
      supplierNameInput.value = sName;
      supplierNameHidden.value = sName;
    }
    updateUrbanTransport();
  }

  function updateUrbanTransport() {
    const globalUrbainTransport = parseFloat(globalUrbainTransportInput.value.replace(/\s+/g, '')) || 0;
    const quantity = parseInt(quantityInput.value) || 0;
    let val = 0;
    if (quantity > 0) {
      val = globalUrbainTransport / quantity;
    }
    urbanTransportInput.value = formatValue(val);
  }

  // Initialisation au chargement
  updatePurchaseDetails(purchaseReferenceSelect.value);
  // Profitability.profit correspond au champ benefit (Bénéfice)
  benefitInput.value = formatValueWithoutDecimal(profitability.profit);
  sellingPriceInput.value = formatValueWithoutDecimal(profitability.selling_price);

  purchaseReferenceSelect.addEventListener('change', function() {
    updatePurchaseDetails(this.value);
  });

  document.querySelectorAll('.format-number').forEach(input => {
    input.addEventListener('blur', () => formatInputNumber(input));
    if(input.value) formatInputNumber(input);
  });

  globalUrbainTransportInput.addEventListener('blur', () => {
    formatInputNumber(globalUrbainTransportInput, true);
    updateUrbanTransport();
  });

  quantityInput.addEventListener('input', () => {
    updateUrbanTransport();
  });

  showPriceButton.addEventListener('click', () => {
    const priceUnit = parseFloat(unitPurchasePriceHidden.value) || 0;
    const urbanTransport = parseFloat(urbanTransportInput.value.replace(/\s+/g, '')) || 0;
    const concierge = parseFloat(document.getElementById('concierge').value.replace(/\s+/g, '')) || 0;
    const transportSourceBko = parseFloat(document.getElementById('transport_source_bko').value.replace(/\s+/g, '')) || 0;
    const weight = parseFloat(weightHidden.value) || 0;
    const customs = parseFloat(document.getElementById('customs').value.replace(/\s+/g, '')) || 0;
    const marginPercent = parseFloat(document.getElementById('margin').value.replace(/\s+/g, '')) || 0;
    const conversionRate = parseFloat(document.getElementById('conversion_rate').value.replace(/\s+/g, '')) || 1;
    const landTransport = parseFloat(document.getElementById('land_transport').value.replace(/\s+/g, '')) || 0;
    const funding = parseFloat(fundingInput.value.replace(/\s+/g, '')) || 0;

    const revienSansTransport = priceUnit + urbanTransport + concierge + (transportSourceBko * weight) + ((customs / 100) * priceUnit);
    const reviensSansTransportConverti = revienSansTransport * conversionRate;
    const reviensAvecTransport = reviensSansTransportConverti + landTransport;
    const valeurMarge = (marginPercent / 100) * reviensAvecTransport;
    const prixVenteFinal = valeurMarge + reviensAvecTransport + funding;

    sellingPriceInput.value = formatValueWithoutDecimal(prixVenteFinal);
    benefitInput.value = formatValueWithoutDecimal(valeurMarge);
  });
});
</script>
@endsection