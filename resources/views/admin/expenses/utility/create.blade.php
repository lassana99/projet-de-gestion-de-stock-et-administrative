@extends('admin.layouts.master')
@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3" style="background-color:#6c757d;">
            <h5 class="m-0 font-weight-bold text-white">Enregistrer Eau / Élec / Internet</h5>
        </div>
        <form action="{{ route('utilityStore') }}" method="POST">
            @csrf
            <div class="card-body" style="background-color:#6c757d;">
                <div class="row">
                    <div class="col-6">
                        <div class="mb-3">
                            <label class="text-white">Mois <span class="text-danger">*</span></label>
                            <select name="month" class="form-control" required>
                                <option value="">Choisir le mois</option>
                                @foreach($months as $m) <option value="{{ $m }}">{{ $m }}</option> @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="text-white">Description <span class="text-danger">*</span></label>
                            <select name="description" id="description" class="form-control" onchange="toggleDesc(this)" required>
                                <option value="">Choisir...</option>
                                @foreach($descriptions as $d) <option value="{{ $d }}">{{ $d }}</option> @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="text-white">Montant (FCFA) <span class="text-danger">*</span></label>
                            <input type="text" id="amount_display" class="form-control" required>
                            <input type="hidden" name="amount_fcfa" id="amount_real">
                        </div>
                        <div class="mb-3" id="desc_other_div" style="display:none;">
                            <label class="text-white">Précisez la description</label>
                            <input type="text" name="description_other" id="description_other" class="form-control">
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="mb-3">
                            <label class="text-white">Référence</label>
                            <input type="text" name="reference" class="form-control" placeholder="Ex: N° Facture">
                        </div>
                        <div class="mb-3">
                            <label class="text-white">Date d'émission <span class="text-danger">*</span></label>
                            <input type="date" name="issue_date" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="text-white">Mode de Payement <span class="text-danger">*</span></label>
                            <select name="payment_mode" class="form-control" required>
                                <option value="Espèces">Espèces</option>
                                <option value="Virement bancaire">Virement bancaire</option>
                                <option value="Chèque">Chèque</option>
                                <option value="Mobile money">Mobile money</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row mt-4">
                    <div class="col-6"><button type="submit" class="btn w-100 text-white" style="background-color: rgb(249, 138, 27); border: none;">Ajouter</button></div>
                    <div class="col-6"><a href="{{ route('utilityList') }}" class="btn w-100 text-white" style="background-color: #908e8c; border: none;">Annuler</a></div>
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
    if(!isNaN(val) && val !== '') {
        document.getElementById('amount_real').value = val;
        this.value = new Intl.NumberFormat('fr-FR').format(val).replace(/,/g, ' ');
    }
});
</script>
@endsection