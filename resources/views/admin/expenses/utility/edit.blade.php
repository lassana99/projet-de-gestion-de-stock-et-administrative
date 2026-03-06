@extends('admin.layouts.master')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3" style="background-color:#6c757d;">
            <h5 class="m-0 font-weight-bold text-white">Modifier : {{ $utility->number }}</h5>
        </div>
        <form action="{{ route('utilityUpdate', $utility->id) }}" method="POST">
            @csrf @method('PUT')
            <div class="card-body" style="background-color:#6c757d;">
                <div class="row">
                    <div class="col-6">
                        <div class="mb-3">
                            <label class="text-white">Mois <span class="text-danger">*</span></label>
                            <select name="month" class="form-control" required>
                                @foreach($months as $m) 
                                    <option value="{{ $m }}" {{ old('month', $utility->month) == $m ? 'selected' : '' }}>{{ $m }}</option> 
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="text-white">Description <span class="text-danger">*</span></label>
                            <select name="description" id="description" class="form-control" onchange="toggleDesc(this)" required>
                                @foreach($descriptions as $d) 
                                    <option value="{{ $d }}" {{ old('description', $utility->description) == $d ? 'selected' : '' }}>{{ $d }}</option> 
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="text-white">Montant (FCFA) <span class="text-danger">*</span></label>
                            <input type="text" id="amount_display" class="form-control" value="{{ number_format($utility->amount_fcfa, 0, '', ' ') }}" required>
                            <input type="hidden" name="amount_fcfa" id="amount_real" value="{{ old('amount_fcfa', $utility->amount_fcfa) }}">
                        </div>
                        <div class="mb-3" id="desc_other_div" style="{{ old('description', $utility->description) == 'Autres' ? 'display:block;' : 'display:none;' }}">
                            <label class="text-white">Précisez la description</label>
                            <input type="text" name="description_other" id="description_other" class="form-control" value="{{ old('description_other', $utility->description_other) }}">
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="mb-3">
                            <label class="text-white">Référence</label>
                            <input type="text" name="reference" class="form-control" value="{{ old('reference', $utility->reference) }}">
                        </div>
                        <div class="mb-3">
                            <label class="text-white">Date d'émission <span class="text-danger">*</span></label>
                            <input type="date" name="issue_date" class="form-control" value="{{ old('issue_date', $utility->issue_date ? $utility->issue_date->format('Y-m-d') : '') }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="text-white">Mode de Payement <span class="text-danger">*</span></label>
                            <select name="payment_mode" class="form-control" required>
                                @foreach(['Espèces', 'Virement bancaire', 'Chèque', 'Mobile money'] as $mode)
                                    <option value="{{ $mode }}" {{ old('payment_mode', $utility->payment_mode) == $mode ? 'selected' : '' }}>{{ $mode }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row mt-4">
                    <div class="col-6"><button type="submit" class="btn w-100 text-white fw-bold" style="background-color: rgb(249, 138, 27); border: none;">Mettre à jour</button></div>
                    <div class="col-6"><a href="{{ route('utilityList') }}" class="btn w-100 text-white fw-bold" style="background-color: #908e8c; border: none;">Annuler</a></div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function toggleDesc(select) {
    const div = document.getElementById('desc_other_div');
    const input = document.getElementById('description_other');
    div.style.display = select.value === 'Autres' ? 'block' : 'none';
    input.required = select.value === 'Autres';
}
document.getElementById('amount_display').addEventListener('input', function() {
    let val = this.value.replace(/\s+/g, '');
    if(!isNaN(val)) {
        document.getElementById('amount_real').value = val;
        this.value = new Intl.NumberFormat('fr-FR').format(val).replace(/,/g, ' ');
    }
});
</script>
@endsection