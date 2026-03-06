@extends('admin.layouts.master')
@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3" style="background-color:#6c757d;">
            <h5 class="m-0 font-weight-bold text-white">Modifier l'Impôt #{{ $tax->number }}</h5>
        </div>
        <form action="{{ route('taxUpdate', $tax->id) }}" method="POST">
            @csrf @method('PUT')
            <div class="card-body" style="background-color:#6c757d;">
                <div class="row">
                    <div class="col-6">
                        <div class="mb-3">
                            <label class="text-white">Mois <span class="text-danger">*</span></label>
                            <select name="month" class="form-control" required>
                                @foreach($months as $m) 
                                    <option value="{{ $m }}" {{ $tax->month == $m ? 'selected' : '' }}>{{ $m }}</option> 
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="text-white">Description <span class="text-danger">*</span></label>
                            <select name="description" id="description" class="form-control" onchange="toggleDesc(this)" required>
                                @foreach($descriptions as $d) 
                                    <option value="{{ $d }}" {{ $tax->description == $d ? 'selected' : '' }}>{{ $d }}</option> 
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="text-white">Montant (FCFA) <span class="text-danger">*</span></label>
                            <input type="text" id="amount_display" class="form-control" value="{{ number_format($tax->amount_fcfa, 0, '', ' ') }}" required>
                            <input type="hidden" name="amount_fcfa" id="amount_real" value="{{ $tax->amount_fcfa }}">
                        </div>
                        <div class="mb-3" id="desc_other_div" style="{{ $tax->description == 'Autres' ? 'display:block;' : 'display:none;' }}">
                            <label class="text-white">Précisez la description</label>
                            <input type="text" name="description_other" id="description_other" class="form-control" value="{{ $tax->description_other }}">
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="mb-3">
                            <label class="text-white">Référence</label>
                            <input type="text" name="reference" class="form-control" value="{{ $tax->reference }}">
                        </div>
                        <div class="mb-3">
                            <label class="text-white">Date d'émission <span class="text-danger">*</span></label>
                            <input type="date" name="issue_date" class="form-control" value="{{ $tax->issue_date->format('Y-m-d') }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="text-white">Mode de Payement <span class="text-danger">*</span></label>
                            <select name="payment_mode" class="form-control" required>
                                @foreach(['Espèces', 'Virement bancaire', 'Chèque', 'Mobile money'] as $mode)
                                    <option value="{{ $mode }}" {{ $tax->payment_mode == $mode ? 'selected' : '' }}>{{ $mode }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row mt-4">
                    <div class="col-6"><button type="submit" class="btn w-100 text-white" style="background-color: rgb(249, 138, 27);">Mettre à jour</button></div>
                    <div class="col-6"><a href="{{ route('taxList') }}" class="btn w-100 text-white" style="background-color: #908e8c;">Annuler</a></div>
                </div>
            </div>
        </form>
    </div>
</div>
<script>
function toggleDesc(select) {
    const div = document.getElementById('desc_other_div');
    div.style.display = select.value === 'Autres' ? 'block' : 'none';
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