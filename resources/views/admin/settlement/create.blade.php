@extends('admin.layouts.master')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4" style="background-color:#6c757d; border: 1px solid white;">
        <div class="card-header py-3" style="background-color:#6c757d; border-bottom: 1px solid white;">
            <h5 class="m-0 font-weight-bold text-white">Enregistrer une Dette / Créance</h5>
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

            <form action="{{ route('settlementStore') }}" method="POST" id="settlement-form">
                @csrf
                
                {{-- SECTION 1 : Type et Entité --}}
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="text-white font-weight-bold">Type d'opération <span style="color: red;">*</span></label>
                        <select name="type" class="form-control" required>
                            <option value="" disabled selected>Choisir le type...</option>
                            <option value="debt">Créance</option>
                            <option value="credit">Dette</option>
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="text-white font-weight-bold">Nom du Client / Fournisseur <span style="color: red;">*</span></label>
                        <input type="text" name="entity_name" class="form-control" placeholder="Nom de l'entité" required>
                    </div>
                </div>

                {{-- SECTION 2 : Coordonnées --}}
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="text-white font-weight-bold">Téléphone</label>
                        <input type="text" name="phone" class="form-control" placeholder="Numéro de téléphone">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="text-white font-weight-bold">Email</label>
                        <input type="email" name="email" class="form-control" placeholder="Adresse email">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="text-white font-weight-bold">Adresse</label>
                        <input type="text" name="address" class="form-control" placeholder="Adresse physique">
                    </div>
                </div>

                <hr style="border-color: white;">

                {{-- SECTION 3 : Montant et Devises --}}
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="text-white font-weight-bold">Montant <span style="color: red;">*</span></label>
                        <input type="text" name="amount" id="amount_input" class="form-control fw-bold" placeholder="Ex: 1500" required>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="text-white font-weight-bold">Devise <span style="color: red;">*</span></label>
                        <select name="currency" id="currency_select" class="form-control" required>
                            <option value="FCFA" selected>FCFA</option>
                            <option value="Dollar">Dollar</option>
                            <option value="Euro">Euro</option>
                            <option value="Livre Sterling">Livre Sterling</option>
                        </select>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="text-white font-weight-bold text-warning">Montant en FCFA <span style="color: red;">*</span></label>
                        <input type="text" name="amount_fcfa" id="amount_fcfa_input" class="form-control fw-bold" style="background-color: #e9ecef;" readonly required>
                    </div>
                </div>

                {{-- SECTION 4 : Dates --}}
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="text-white font-weight-bold">Date d'émission <span style="color: red;">*</span></label>
                        <input type="date" name="issue_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        <small class="text-light">Date de la facture ou de l'accord</small>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="text-white font-weight-bold text-warning">Date d'échéance <span style="color: red;">*</span> (Déclenche l'alerte)</label>
                        <input type="date" name="due_date" class="form-control" required>
                    </div>
                </div>

                <div class="mt-4 d-flex justify-content-between">
                    <button type="submit" class="btn fw-bold text-white shadow-sm" style="background-color: rgb(249, 138, 27); border: none; padding: 10px 25px;">
                        <i class="fa-solid fa-save mr-1"></i> Enregistrer et activer l'alerte
                    </button>
                    <a href="{{ route('settlementList') }}" class="btn btn-secondary shadow-sm" style="background-color: rgb(144, 142, 140); border: none;">Annuler</a>
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

    // Taux de change fixes (identiques au contrôleur)
    const rates = {
        'FCFA': 1,
        'Euro': 656,
        'Dollar': 610,
        'Livre Sterling': 780
    };

    // --- Fonction de Formatage (Espaces pour les milliers) ---
    function formatNumber(value) {
        if (!value) return "";
        // Supprimer tout ce qui n'est pas chiffre ou point
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

    // --- Nettoyage avant soumission ---
    form.addEventListener('submit', function() {
        // On retire les espaces pour envoyer des nombres purs au serveur
        amountInput.value = amountInput.value.replace(/\s/g, '');
        amountFcfaInput.value = amountFcfaInput.value.replace(/\s/g, '');
    });
});
</script>
@endsection