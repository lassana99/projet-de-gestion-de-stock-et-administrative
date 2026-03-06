@extends('admin.layouts.master')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        {{-- En-tête --}}
        <div class="card-header py-3" style="background-color:#6c757d;">
            <h5 class="m-0 text-white font-weight-bold">Nouvelle Dépense (Autres)</h5>
        </div>

        {{-- Formulaire --}}
        <form action="{{ route('otherExpenseStore') }}" method="POST">
            @csrf
            <div class="card-body" style="background-color:#6c757d;">
                
                {{-- Affichage des erreurs de validation --}}
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="row">
                    {{-- Colonne 1 : Informations Générales --}}
                    <div class="col-md-6">
                        <div class="mb-3 text-white">
                            <label class="form-label fw-bold">Nom et Prénom <span class="text-danger">*</span></label>
                            <input type="text" name="full_name" class="form-control @error('full_name') is-invalid @enderror" value="{{ old('full_name') }}" required>
                        </div>

                        <div class="mb-3 text-white">
                            <label class="form-label fw-bold">Montant (FCFA) <span class="text-danger">*</span></label>
                            <input type="text" id="amount_display" class="form-control" value="{{ old('amount_fcfa') }}" placeholder="Ex: 100 000" required>
                            <input type="hidden" name="amount_fcfa" id="amount_real" value="{{ old('amount_fcfa') }}">
                        </div>

                        <div class="mb-3 text-white">
                            <label class="form-label fw-bold">Date <span class="text-danger">*</span></label>
                            <input type="date" name="date" class="form-control @error('date') is-invalid @enderror" value="{{ old('date', date('Y-m-d')) }}" required>
                        </div>

                        {{-- Désignation (CHAMP FACULTATIF / NULLABLE) --}}
                        <div class="mb-3 text-white">
                            <label class="form-label fw-bold">Désignation</label>
                            <textarea name="designation" class="form-control @error('designation') is-invalid @enderror" rows="4" placeholder="Désignation de la dépense (optionnel)">{{ old('designation') }}</textarea>
                        </div>
                    </div>

                    {{-- Colonne 2 : Détails du Paiement --}}
                    <div class="col-md-6">
                        <div class="mb-3 text-white">
                            <label class="form-label fw-bold">Motif du Paiement <span class="text-danger">*</span></label>
                            <select name="payment_reason" id="payment_reason" class="form-control" onchange="toggleReasonField(this)" required>
                                <option value="">-- Sélectionner un motif --</option>
                                <option value="Avance sur salaire" {{ old('payment_reason') == 'Avance sur salaire' ? 'selected' : '' }}>Avance sur salaire</option>
                                <option value="Per diem / Mission" {{ old('payment_reason') == 'Per diem / Mission' ? 'selected' : '' }}>Per diem / Mission</option>
                                <option value="Remboursement" {{ old('payment_reason') == 'Remboursement' ? 'selected' : '' }}>Remboursement</option>
                                <option value="Carburant Voiture" {{ old('payment_reason') == 'Carburant Voiture' ? 'selected' : '' }}>Carburant Voiture</option>
                                <option value="Carburant Moto" {{ old('payment_reason') == 'Carburant Moto' ? 'selected' : '' }}>Carburant Moto</option>
                                <option value="Réparation Véhicule" {{ old('payment_reason') == 'Réparation Véhicule' ? 'selected' : '' }}>Réparation Véhicule</option>
                                <option value="Autres" {{ old('payment_reason') == 'Autres' ? 'selected' : '' }}>Autres</option>
                            </select>
                        </div>

                        {{-- Motif "Autres" conditionnel --}}
                        <div class="mb-3" id="reason_other_div" style="{{ old('payment_reason') == 'Autres' ? 'display:block;' : 'display:none;' }}">
                            <label class="text-white fw-bold">Précisez le motif <span class="text-danger">*</span></label>
                            <input type="text" name="payment_reason_other" id="payment_reason_other" class="form-control" value="{{ old('payment_reason_other') }}" placeholder="Saisir le motif personnalisé...">
                        </div>

                        <div class="mb-3 text-white">
                            <label class="form-label fw-bold">Modalité de Payement <span class="text-danger">*</span></label>
                            <select name="payment_mode" class="form-control" required>
                                <option value="Espèces" {{ old('payment_mode') == 'Espèces' ? 'selected' : '' }}>Espèces</option>
                                <option value="Virement bancaire" {{ old('payment_mode') == 'Virement bancaire' ? 'selected' : '' }}>Virement bancaire</option>
                                <option value="Chèque" {{ old('payment_mode') == 'Chèque' ? 'selected' : '' }}>Chèque</option>
                                <option value="Mobile money" {{ old('payment_mode') == 'Mobile money' ? 'selected' : '' }}>Mobile money</option>
                            </select>
                        </div>

                        <div class="mb-3 text-white">
                            <label class="form-label fw-bold">Détails complémentaires (Si nécessaire)</label>
                            <textarea name="additional_details" class="form-control" rows="4" placeholder="Informations additionnelles...">{{ old('additional_details') }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- Boutons d'action --}}
                <div class="row mt-4">
                    <div class="col-6">
                        <button type="submit" class="btn w-100 text-white shadow-sm" style="background-color: rgb(249, 138, 27); border: none; font-weight: bold;">
                            <i class="fa-solid fa-floppy-disk me-1"></i> Ajouter
                        </button>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('otherExpenseList') }}" class="btn w-100 text-white shadow-sm" style="background-color: #908e8c; border: none; font-weight: bold;">
                            Annuler
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
/**
 * Gère l'affichage du champ "Autres" pour le motif
 */
function toggleReasonField(select) {
    const div = document.getElementById('reason_other_div');
    const input = document.getElementById('payment_reason_other');
    if(select.value === 'Autres') {
        div.style.display = 'block';
        input.required = true;
    } else {
        div.style.display = 'none';
        input.required = false;
        input.value = '';
    }
}

/**
 * Formatage automatique du montant avec espaces (milliers)
 */
document.getElementById('amount_display').addEventListener('input', function() {
    let val = this.value.replace(/\s+/g, '');
    if(!isNaN(val) && val !== '') {
        document.getElementById('amount_real').value = val;
        this.value = new Intl.NumberFormat('fr-FR').format(val).replace(/,/g, ' ');
    } else if (val === '') {
        document.getElementById('amount_real').value = '';
        this.value = '';
    }
});
</script>
@endsection