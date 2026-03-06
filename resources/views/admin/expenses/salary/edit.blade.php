@extends('admin.layouts.master')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3" style="background-color:#6c757d;">
            <h5 class="m-0 font-weight-bold text-white">Modifier Salaire : {{ $salary->number }}</h5>
        </div>
        <form action="{{ route('salaryUpdate', $salary->id) }}" method="POST">
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
                    {{-- Colonne 1 : Informations Personnel --}}
                    <div class="col-md-6">
                        <div class="mb-3 text-white">
                            <label class="fw-bold">Nom et Prénom <span class="text-danger">*</span></label>
                            <select name="full_name" id="employee_select" class="form-control" required>
                                <option value="">-- Sélectionner l'employé --</option>
                                @foreach($employees as $emp)
                                    <option value="{{ $emp->full_name }}" 
                                        {{ old('full_name', $salary->full_name) == $emp->full_name ? 'selected' : '' }}
                                        data-position="{{ $emp->position }}" 
                                        data-idtype="{{ $emp->id_type }}" 
                                        data-idtypeother="{{ $emp->id_type_other }}" 
                                        data-idnumber="{{ $emp->id_number }}">
                                        {{ $emp->full_name }} ({{ $emp->matricule }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3 text-white">
                            <label class="fw-bold">Fonction / Service <span class="text-danger">*</span></label>
                            <input type="text" name="position" id="position" class="form-control" value="{{ old('position', $salary->position) }}" required>
                        </div>

                        <div class="mb-3 text-white">
                            <label class="fw-bold">Type de pièce <span class="text-danger">*</span></label>
                            <select name="id_type" id="id_type" class="form-control" onchange="toggleIdOther(this)" required>
                                <option value="CIB" {{ old('id_type', $salary->id_type) == 'CIB' ? 'selected' : '' }}>CIB</option>
                                <option value="Passeport" {{ old('id_type', $salary->id_type) == 'Passeport' ? 'selected' : '' }}>Passeport</option>
                                <option value="Autres" {{ old('id_type', $salary->id_type) == 'Autres' ? 'selected' : '' }}>Autres</option>
                            </select>
                        </div>

                        <div class="mb-3" id="id_type_other_div" style="{{ old('id_type', $salary->id_type) == 'Autres' ? 'display:block' : 'display:none' }}">
                            <label class="text-white fw-bold">Précisez le type <span class="text-danger">*</span></label>
                            <input type="text" name="id_type_other" id="id_type_other" class="form-control" value="{{ old('id_type_other', $salary->id_type_other) }}">
                        </div>

                        <div class="mb-3 text-white">
                            <label class="fw-bold">Numéro de pièce <span class="text-danger">*</span></label>
                            <input type="text" name="id_number" id="id_number" class="form-control" value="{{ old('id_number', $salary->id_number) }}" required>
                        </div>
                    </div>

                    {{-- Colonne 2 : Informations Paiement --}}
                    <div class="col-md-6">
                        <div class="mb-3 text-white">
                            <label class="fw-bold">Montant (FCFA) <span class="text-danger">*</span></label>
                            <input type="text" id="amount_display" class="form-control" value="{{ number_format($salary->amount_fcfa, 0, '', ' ') }}" required>
                            <input type="hidden" name="amount_fcfa" id="amount_real" value="{{ $salary->amount_fcfa }}">
                        </div>

                        <div class="mb-3 text-white">
                            <label class="fw-bold">Date de paiement <span class="text-danger">*</span></label>
                            <input type="date" name="payment_date" class="form-control" value="{{ old('payment_date', $salary->payment_date->format('Y-m-d')) }}" required>
                        </div>

                        <div class="mb-3 text-white">
                            <label class="fw-bold">Modalité de Payement <span class="text-danger">*</span></label>
                            <select name="payment_mode" class="form-control" required>
                                @foreach(['Espèces', 'Virement bancaire', 'Chèque', 'Mobile money'] as $mode)
                                    <option value="{{ $mode }}" {{ old('payment_mode', $salary->payment_mode) == $mode ? 'selected' : '' }}>{{ $mode }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3 text-white">
                            <label class="fw-bold">Détails complémentaires</label>
                            <textarea name="additional_details" class="form-control" rows="4">{{ old('additional_details', $salary->additional_details) }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- Boutons d'action --}}
                <div class="row mt-4">
                    <div class="col-6">
                        <button type="submit" class="btn w-100 text-white fw-bold shadow-sm" style="background-color: rgb(249, 138, 27); border:none;">
                            <i class="fa-solid fa-rotate me-1"></i> Mettre à jour
                        </button>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('salaryList') }}" class="btn w-100 text-white fw-bold shadow-sm" style="background-color: #908e8c; border:none;">
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
 * Logique d'autoremplissage lors du changement d'employé
 */
document.getElementById('employee_select').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    
    if (selectedOption.value !== "") {
        const position = selectedOption.getAttribute('data-position');
        const idType = selectedOption.getAttribute('data-idtype');
        const idTypeOther = selectedOption.getAttribute('data-idtypeother');
        const idNumber = selectedOption.getAttribute('data-idnumber');

        document.getElementById('position').value = position;
        document.getElementById('id_type').value = idType;
        document.getElementById('id_number').value = idNumber;

        const otherDiv = document.getElementById('id_type_other_div');
        const otherInput = document.getElementById('id_type_other');
        
        if (idType === 'Autres') {
            otherDiv.style.display = 'block';
            otherInput.value = idTypeOther;
            otherInput.required = true;
        } else {
            otherDiv.style.display = 'none';
            otherInput.value = '';
            otherInput.required = false;
        }
    }
});

/**
 * Gère l'affichage manuel du champ "Autres"
 */
function toggleIdOther(select) {
    const div = document.getElementById('id_type_other_div');
    const input = document.getElementById('id_type_other');
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
 * Formatage du montant
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