@extends('admin.layouts.master')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        {{-- En-tête --}}
        <div class="card-header py-3" style="background-color:#6c757d;">
            <h5 class="m-0 text-white font-weight-bold">Modifier la Dépense : {{ $expense->number }}</h5>
        </div>

        {{-- Formulaire --}}
        <form action="{{ route('otherExpenseUpdate', $expense->id) }}" method="POST">
            @csrf
            @method('PUT')
            
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
                            <input type="text" name="full_name" class="form-control" value="{{ old('full_name', $expense->full_name) }}" required>
                        </div>

                        <div class="mb-3 text-white">
                            <label class="form-label fw-bold">Montant (FCFA) <span class="text-danger">*</span></label>
                            <input type="text" id="amount_display" class="form-control" 
                                   value="{{ number_format($expense->amount_fcfa, 0, '', ' ') }}" required>
                            <input type="hidden" name="amount_fcfa" id="amount_real" value="{{ old('amount_fcfa', $expense->amount_fcfa) }}">
                        </div>

                        <div class="mb-3 text-white">
                            <label class="form-label fw-bold">Date <span class="text-danger">*</span></label>
                            <input type="date" name="date" class="form-control" value="{{ old('date', $expense->date->format('Y-m-d')) }}" required>
                        </div>

                        {{-- Désignation (CHAMP FACULTATIF / NULLABLE) --}}
                        <div class="mb-3 text-white">
                            <label class="form-label fw-bold">Désignation</label>
                            <textarea name="designation" class="form-control" rows="4" placeholder="Désignation de la dépense (optionnel)">{{ old('designation', $expense->designation) }}</textarea>
                        </div>
                    </div>

                    {{-- Colonne 2 : Détails du Paiement --}}
                    <div class="col-md-6">
                        <div class="mb-3 text-white">
                            <label class="form-label fw-bold">Motif du Paiement <span class="text-danger">*</span></label>
                            <select name="payment_reason" id="payment_reason" class="form-control" onchange="toggleReasonField(this)" required>
                                @php
                                    $reasons = ['Avance sur salaire', 'Per diem / Mission', 'Remboursement', 'Carburant Voiture', 'Carburant Moto', 'Réparation Véhicule', 'Autres'];
                                @endphp
                                @foreach($reasons as $reason)
                                    <option value="{{ $reason }}" {{ old('payment_reason', $expense->payment_reason) == $reason ? 'selected' : '' }}>
                                        {{ $reason }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Motif Autres --}}
                        <div class="mb-3" id="reason_other_div" style="{{ old('payment_reason', $expense->payment_reason) == 'Autres' ? 'display:block;' : 'display:none;' }}">
                            <label class="text-white fw-bold">Précisez le motif <span class="text-danger">*</span></label>
                            <input type="text" name="payment_reason_other" id="payment_reason_other" class="form-control" value="{{ old('payment_reason_other', $expense->payment_reason_other) }}">
                        </div>

                        <div class="mb-3 text-white">
                            <label class="form-label fw-bold">Modalité de Payement <span class="text-danger">*</span></label>
                            <select name="payment_mode" class="form-control" required>
                                @foreach(['Espèces', 'Virement bancaire', 'Chèque', 'Mobile money'] as $mode)
                                    <option value="{{ $mode }}" {{ old('payment_mode', $expense->payment_mode) == $mode ? 'selected' : '' }}>
                                        {{ $mode }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3 text-white">
                            <label class="form-label fw-bold">Détails complémentaires (Si nécessaire)</label>
                            <textarea name="additional_details" class="form-control" rows="4">{{ old('additional_details', $expense->additional_details) }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- Boutons --}}
                <div class="row mt-4">
                    <div class="col-6">
                        <button type="submit" class="btn w-100 text-white fw-bold shadow-sm" style="background-color: rgb(249, 138, 27); border: none;">
                            <i class="fa-solid fa-rotate me-1"></i> Mettre à jour
                        </button>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('otherExpenseList') }}" class="btn w-100 text-white fw-bold shadow-sm" style="background-color: #908e8c; border: none;">
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
 * Formatage automatique du montant avec espaces
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