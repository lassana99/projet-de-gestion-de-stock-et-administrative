@extends('admin.layouts.master')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        {{-- En-tête --}}
        <div class="card-header py-3" style="background-color:#6c757d;">
            <h5 class="m-0 font-weight-bold text-white">Modifier le Paiement INPS : {{ $payment->number }}</h5>
        </div>

        {{-- Formulaire --}}
        <form action="{{ route('inpsUpdate', $payment->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="card-body" style="background-color:#6c757d;">
                
                {{-- Affichage des erreurs --}}
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
                    {{-- Colonne 1 : Personnel et Période --}}
                    <div class="col-md-6">
                        <div class="mb-3 text-white">
                            <label class="fw-bold">Personnes concernées <span class="text-danger">*</span></label>
                            <select name="employee_ids[]" class="form-control" multiple style="height: 150px;" required>
                                @foreach($employees as $emp)
                                    <option value="{{ $emp->id }}" 
                                        {{ in_array($emp->id, $payment->employees->pluck('id')->toArray()) ? 'selected' : '' }}>
                                        {{ $emp->full_name }} ({{ $emp->matricule }})
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-white-50">Maintenez la touche Ctrl (ou Cmd) pour modifier la sélection.</small>
                        </div>

                        <div class="row">
                            <div class="col-6 mb-3 text-white">
                                <label class="fw-bold">Date de Début <span class="text-danger">*</span></label>
                                <input type="date" name="start_date" class="form-control" value="{{ old('start_date', $payment->start_date->format('Y-m-d')) }}" required>
                            </div>
                            <div class="col-6 mb-3 text-white">
                                <label class="fw-bold">Date de Fin <span class="text-danger">*</span></label>
                                <input type="date" name="end_date" class="form-control" value="{{ old('end_date', $payment->end_date->format('Y-m-d')) }}" required>
                            </div>
                        </div>
                    </div>

                    {{-- Colonne 2 : Financier --}}
                    <div class="col-md-6">
                        <div class="mb-3 text-white">
                            <label class="fw-bold">Montant Total (FCFA) <span class="text-danger">*</span></label>
                            <input type="text" id="amount_display" class="form-control" 
                                   value="{{ number_format($payment->amount_fcfa, 0, '', ' ') }}" required>
                            <input type="hidden" name="amount_fcfa" id="amount_real" value="{{ $payment->amount_fcfa }}">
                        </div>

                        <div class="mb-3 text-white">
                            <label class="fw-bold">Date de Paiement <span class="text-danger">*</span></label>
                            <input type="date" name="payment_date" class="form-control" value="{{ old('payment_date', $payment->payment_date->format('Y-m-d')) }}" required>
                        </div>

                        <div class="mb-3 text-white">
                            <label class="fw-bold">Modalité de Payement <span class="text-danger">*</span></label>
                            <select name="payment_mode" class="form-control" required>
                                @foreach(['Espèces', 'Virement bancaire', 'Chèque', 'Mobile money'] as $mode)
                                    <option value="{{ $mode }}" {{ old('payment_mode', $payment->payment_mode) == $mode ? 'selected' : '' }}>
                                        {{ $mode }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Détails complémentaires --}}
                    <div class="col-12 mt-2 text-white">
                        <label class="fw-bold">Détails complémentaires</label>
                        <textarea name="additional_details" class="form-control" rows="3">{{ old('additional_details', $payment->additional_details) }}</textarea>
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
                        <a href="{{ route('inpsList') }}" class="btn w-100 text-white shadow-sm" style="background-color: #908e8c; border: none;">
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