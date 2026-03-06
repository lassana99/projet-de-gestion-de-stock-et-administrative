@extends('admin.layouts.master')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        {{-- En-tête --}}
        <div class="card-header py-3" style="background-color:#6c757d;">
            <h5 class="m-0 font-weight-bold text-white">Modifier le Loyer #{{ $rent->number }}</h5>
        </div>

        {{-- Formulaire --}}
        <form action="{{ route('rentUpdate', $rent->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="card-body" style="background-color:#6c757d;">
                
                {{-- Affichage des erreurs de validation --}}
                @if ($errors->any())
                    <div class="alert alert-danger" style="margin-bottom: 20px;">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="row">
                    {{-- Colonne Gauche --}}
                    <div class="col-6">
                        {{-- Numéro (Lecture seule car auto-généré) --}}
                        <div class="mb-3">
                            <label class="form-label text-white">Numéro</label>
                            <input type="text" name="number" class="form-control" value="{{ $rent->number }}" readonly 
                                   style="background-color: #e9ecef; font-weight: bold; color: #495057;">
                            <small class="text-white-50">Le numéro automatique ne peut pas être modifié.</small>
                        </div>

                        {{-- Mois (Liste déroulante) --}}
                        <div class="mb-3">
                            <label class="form-label text-white">Mois <span class="text-danger">*</span></label>
                            <select name="month" class="form-control @error('month') is-invalid @enderror" required>
                                @foreach($months as $m)
                                    <option value="{{ $m }}" {{ old('month', $rent->month) == $m ? 'selected' : '' }}>
                                        {{ $m }}
                                    </option>
                                @endforeach
                            </select>
                            @error('month') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        {{-- Référence --}}
                        <div class="mb-3">
                            <label class="form-label text-white">Référence</label>
                            <input type="text" name="reference" class="form-control @error('reference') is-invalid @enderror" 
                                   value="{{ old('reference', $rent->reference) }}" placeholder="Ex: REF-2024-001">
                            @error('reference') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        {{-- Montant formaté --}}
                        <div class="mb-3">
                            <label class="form-label text-white">Montant (FCFA) <span class="text-danger">*</span></label>
                            <input type="text" id="amount_display" class="form-control" 
                                   value="{{ number_format($rent->amount_fcfa, 0, '', ' ') }}" required>
                            <input type="hidden" name="amount_fcfa" id="amount_real" value="{{ old('amount_fcfa', $rent->amount_fcfa) }}">
                            @error('amount_fcfa') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                    </div>

                    {{-- Colonne Droite --}}
                    <div class="col-6">
                        {{-- Structure --}}
                        <div class="mb-3">
                            <label class="form-label text-white">Structure <span class="text-danger">*</span></label>
                            <input type="text" name="structure" class="form-control @error('structure') is-invalid @enderror" 
                                   value="{{ old('structure', $rent->structure) }}" required>
                            @error('structure') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        {{-- Date d'émission --}}
                        <div class="mb-3">
                            <label class="form-label text-white">Date d'émission <span class="text-danger">*</span></label>
                            <input type="date" name="issue_date" class="form-control @error('issue_date') is-invalid @enderror" 
                                   value="{{ old('issue_date', $rent->issue_date ? $rent->issue_date->format('Y-m-d') : '') }}" required>
                            @error('issue_date') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        {{-- Mode de Payement --}}
                        <div class="mb-3">
                            <label class="form-label text-white">Mode de Payement <span class="text-danger">*</span></label>
                            <select name="payment_mode" id="payment_mode" class="form-control" onchange="toggleOther(this)" required>
                                @foreach(['Espèces', 'Virement bancaire', 'Chèque', 'Mobile money', 'Autres'] as $mode)
                                    <option value="{{ $mode }}" {{ old('payment_mode', $rent->payment_mode) == $mode ? 'selected' : '' }}>
                                        {{ $mode }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Mode "Autres" conditionnel --}}
                        <div class="mb-3" id="other_mode_div" style="{{ old('payment_mode', $rent->payment_mode) == 'Autres' ? 'display:block;' : 'display:none;' }}">
                            <label class="form-label text-white">Précisez le mode</label>
                            <input type="text" name="payment_mode_other" id="payment_mode_other" 
                                   class="form-control" value="{{ old('payment_mode_other', $rent->payment_mode_other) }}">
                        </div>

                        {{-- Statut --}}
                        <div class="mb-3">
                            <label class="form-label text-white">Statut <span class="text-danger">*</span></label>
                            <select name="status" class="form-control" required>
                                @foreach(['Payé', 'Payé partiellement', 'Non Payé'] as $status)
                                    <option value="{{ $status }}" {{ old('status', $rent->status) == $status ? 'selected' : '' }}>
                                        {{ $status }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Boutons d'action --}}
                <div class="row mt-4">
                    <div class="col-6">
                        <button type="submit" class="btn w-100 text-white shadow-sm" style="background-color: rgb(249, 138, 27); border: none; font-weight: bold;">
                            <i class="fa-solid fa-rotate me-1"></i> Mettre à jour
                        </button>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('rentList') }}" class="btn w-100 text-white shadow-sm" style="background-color: #908e8c; border: none; font-weight: bold;">
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
 * Gère l'affichage dynamique du champ "Autres"
 */
function toggleOther(select) {
    const div = document.getElementById('other_mode_div');
    const input = document.getElementById('payment_mode_other');
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
 * Formatage du montant avec espaces au fur et à mesure de la saisie
 */
document.addEventListener('DOMContentLoaded', function() {
    const displayInput = document.getElementById('amount_display');
    const realInput = document.getElementById('amount_real');

    displayInput.addEventListener('input', function() {
        let rawValue = this.value.replace(/\s+/g, '');
        
        if(!isNaN(rawValue) && rawValue !== '') {
            realInput.value = rawValue;
            this.value = new Intl.NumberFormat('fr-FR').format(rawValue).replace(/,/g, ' ');
        } else if (rawValue === '') {
            realInput.value = '';
            this.value = '';
        }
    });
});
</script>
@endsection