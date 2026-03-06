@extends('admin.layouts.master')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4" style="background-color:#6c757d; border: 1px solid white;">
        <div class="card-header py-3" style="background-color:#6c757d; border-bottom: 1px solid white;">
            <h5 class="m-0 font-weight-bold text-white">Modifier le Règlement : {{ $settlement->entity_name }}</h5>
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

            <form action="{{ route('settlementUpdate', $settlement->id) }}" method="POST" id="settlement-form">
                @csrf
                @method('PUT')
                
                {{-- SECTION 1 : Type et Entité --}}
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="text-white font-weight-bold">Type d'opération <span style="color: red;">*</span></label>
                        <select name="type" class="form-control" required>
                            <option value="debt" {{ $settlement->type == 'debt' ? 'selected' : '' }}>Créance</option>
                            <option value="credit" {{ $settlement->type == 'credit' ? 'selected' : '' }}>Dette</option>
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="text-white font-weight-bold">Nom du Client / Fournisseur <span style="color: red;">*</span></label>
                        <input type="text" name="entity_name" class="form-control" value="{{ old('entity_name', $settlement->entity_name) }}" required>
                    </div>
                </div>

                {{-- SECTION 2 : Coordonnées --}}
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="text-white font-weight-bold">Téléphone</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone', $settlement->phone) }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="text-white font-weight-bold">Email</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $settlement->email) }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="text-white font-weight-bold">Adresse</label>
                        <input type="text" name="address" class="form-control" value="{{ old('address', $settlement->address) }}">
                    </div>
                </div>

                <hr style="border-color: white;">

                {{-- SECTION 3 : Montant et Devises --}}
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="text-white font-weight-bold">Montant <span style="color: red;">*</span></label>
                        <input type="text" name="amount" id="amount_input" class="form-control fw-bold" value="{{ number_format($settlement->amount, 0, '', ' ') }}" required>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="text-white font-weight-bold">Devise <span style="color: red;">*</span></label>
                        <select name="currency" id="currency_select" class="form-control" required>
                            <option value="FCFA" {{ $settlement->currency == 'FCFA' ? 'selected' : '' }}>FCFA</option>
                            <option value="Dollar" {{ $settlement->currency == 'Dollar' ? 'selected' : '' }}>Dollar</option>
                            <option value="Euro" {{ $settlement->currency == 'Euro' ? 'selected' : '' }}>Euro</option>
                            <option value="Livre Sterling" {{ $settlement->currency == 'Livre Sterling' ? 'selected' : '' }}>Livre Sterling</option>
                        </select>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="text-white font-weight-bold text-warning">Montant en FCFA <span style="color: red;">*</span></label>
                        <input type="text" name="amount_fcfa" id="amount_fcfa_input" class="form-control fw-bold" value="{{ number_format($settlement->amount_fcfa, 0, '', ' ') }}" style="background-color: #e9ecef;" readonly required>
                    </div>
                </div>

                {{-- SECTION 4 : Dates et Statut --}}
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label class="text-white font-weight-bold">Date d'émission <span style="color: red;">*</span></label>
                        <input type="date" name="issue_date" class="form-control" value="{{ $settlement->issue_date->format('Y-m-d') }}" required>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="text-white font-weight-bold text-warning">Date d'échéance <span style="color: red;">*</span></label>
                        <input type="date" name="due_date" class="form-control" value="{{ $settlement->due_date->format('Y-m-d') }}" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="text-white font-weight-bold">État du règlement <span style="color: red;">*</span></label>
                        <select name="status" class="form-control" required>
                            <option value="pending" {{ $settlement->status == 'pending' ? 'selected' : '' }}>En attente</option>
                            <option value="paid" {{ $settlement->status == 'paid' ? 'selected' : '' }}>Réglé / Payé</option>
                        </select>
                    </div>
                </div>

                <div class="mt-4 d-flex justify-content-between">
                    <button type="submit" class="btn fw-bold text-white shadow-sm" style="background-color: rgb(249, 138, 27); border: none; padding: 10px 25px;">
                        <i class="fa-solid fa-sync mr-1"></i> Mettre à jour
                    </button>
                    <a href="{{ route('settlementList') }}" class="btn btn-secondary shadow-sm" style="padding: 10px 25px; background-color: rgb(144, 142, 140); border: none;">Annuler</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const amountInput = document.getElementById('amount_input');
    const currencySelect = document.getElementById('currency_select');
    const amountFcfaInput = document.getElementById('amount_fcfa_input');
    const form = document.getElementById('settlement-form');

    // Taux de change fixes
    const rates = {
        'FCFA': 1,
        'Euro': 656,
        'Dollar': 610,
        'Livre Sterling': 780
    };

    // --- Fonction de Formatage ---
    function formatNumber(value) {
        if (!value) return "";
        let cleanValue = value.toString().replace(/[^\d.]/g, "");
        if (!cleanValue) return "";
        
        let parts = cleanValue.split('.');
        parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, " ");
        return parts.join('.');
    }

    // --- Calcul de la conversion ---
    function calculateConversion() {
        let rawAmount = amountInput.value.replace(/\s/g, '');
        let currency = currencySelect.value;
        
        if (rawAmount && !isNaN(rawAmount)) {
            let rate = rates[currency] || 1;
            let converted = Math.round(parseFloat(rawAmount) * rate);
            amountFcfaInput.value = formatNumber(converted);
        } else {
            amountFcfaInput.value = "";
        }
    }

    // --- Événements ---
    amountInput.addEventListener('input', function() {
        this.value = formatNumber(this.value);
        calculateConversion();
    });

    currencySelect.addEventListener('change', function() {
        calculateConversion();
    });

    // --- Initialisation au chargement (pour vérifier la cohérence) ---
    calculateConversion();

    // --- Nettoyage avant soumission ---
    form.addEventListener('submit', function() {
        amountInput.value = amountInput.value.replace(/\s/g, '');
        amountFcfaInput.value = amountFcfaInput.value.replace(/\s/g, '');
    });
});
</script>
@endsection