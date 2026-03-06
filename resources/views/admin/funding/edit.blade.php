@extends('admin.layouts.master')

@section('content')
@php
use \Carbon\Carbon;

function formatNumberNoDecimal($value) {
    if ($value === null) return '';
    return number_format(intval($value), 0, ',', ' ');
}

$formattedBanque = old('nom_de_banque', $funding->nom_de_banque);
$otherBanqueValue = old('other_banque', '');
$banquesMali = [
    'BCEAO','Banque Malienne de Solidarité (BMS-SA)','Banque Internationale pour le Mali (BIM)',
    'Banque Nationale de Développement Agricole (BNDA)','Coris Bank International Mali',
    'Ecobank Mali','Société Générale Mali','UBA Mali','Union Commerciale du Mali (UCM)','Autres'
];

$showOtherBanque = ($formattedBanque === 'Autres') || (!in_array($formattedBanque,$banquesMali) && $formattedBanque !== '');
@endphp

<div class="container-fluid">
  <div class="card shadow col-md-8 mx-auto" style="background-color:#6c757d; margin-left:-12px; margin-right:-12px;">
    <div class="card-header text-white" style="background-color:#6c757d; margin-left:-12px; margin-right:-12px;">
      <h5 class="m-0">Modifier le prêt #{{ $funding->id }}</h5>
    </div>

    @if ($errors->any())
    <div class="alert alert-danger mx-3 my-3">
      <ul>
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
    @endif

    <form id="fundingForm" action="{{ route('funding.update',$funding->id) }}" method="POST"
          class="p-4" style="background-color:#6c757d; margin-left:-12px; margin-right:-12px;">
      @csrf
      @method('PUT')

      <div class="mb-3">
        <label class="form-label text-white">
          Motif <span class="text-danger">*</span>
        </label>
        <input type="text" name="motif" class="form-control"
               value="{{ old('motif',$funding->motif) }}" required>
      </div>

      <div class="mb-3">
        <label class="form-label text-white">
          Nom de la banque <span class="text-danger">*</span>
        </label>
        <select name="nom_de_banque" id="nom_de_banque" class="form-control"
                onchange="toggleOtherBank(this)" required>
          <option value="">Sélectionnez une banque</option>
          @foreach ($banquesMali as $banque)
            <option value="{{ $banque }}" {{ $formattedBanque === $banque ? 'selected' : '' }}>
              {{ $banque }}
            </option>
          @endforeach
        </select>

        <input type="text" name="other_banque" id="other_banque"
               class="form-control mt-2"
               placeholder="Saisir une autre banque..."
               style="{{ $showOtherBanque ? 'display:block;' : 'display:none;' }}"
               value="{{ $otherBanqueValue }}">
      </div>

      @php
        $formattedMontantEmprunte = formatNumberNoDecimal(old('montant_emprunte',$funding->montant_emprunte));
        $formattedNombreDeJours = formatNumberNoDecimal(old('nombre_de_jours',$funding->nombre_de_jours));
        $formattedTaux = formatNumberNoDecimal(old('taux',$funding->taux));
        $formattedNombreDItems = formatNumberNoDecimal(old('nombre_d_items',$funding->nombre_d_items));
      @endphp

      <div class="mb-3">
        <label class="form-label text-white">
          Montant emprunté <span class="text-danger">*</span>
        </label>
        <input type="text" id="montant_emprunte" name="montant_emprunte"
               class="form-control" value="{{ $formattedMontantEmprunte }}" required>
      </div>

      <div class="mb-3">
        <label class="form-label text-white">
          Nombre de jours <span class="text-danger">*</span>
        </label>
        <input type="number" min="1" name="nombre_de_jours"
               class="form-control" value="{{ $formattedNombreDeJours }}" required>
      </div>

      <div class="mb-3">
        <label class="form-label text-white">
          Taux (%) <span class="text-danger">*</span>
        </label>
        <input type="number" step="0.0001" min="0" name="taux"
               class="form-control" value="{{ $formattedTaux }}" required>
      </div>

      <div class="mb-3">
        <label class="form-label text-white">
          Nombre d'items <span class="text-danger">*</span>
        </label>
        <input type="number" min="1" name="nombre_d_items"
               class="form-control" value="{{ $formattedNombreDItems }}" required>
      </div>

      <div class="mb-3">
        <label class="form-label text-white">
          Date <span class="text-danger">*</span>
        </label>
        <input type="date" name="date" class="form-control"
               value="{{ old('date', Carbon::parse($funding->date)->format('Y-m-d')) }}" required>
      </div>

      <div class="mb-3">
        <label class="form-label text-white">Montant à payer</label>
        <input type="text" id="montant_a_payer" class="form-control" readonly>
      </div>

      <div class="mb-3">
        <label class="form-label text-white">Montant à payer par item</label>
        <input type="text" id="montant_a_payer_par_item" class="form-control" readonly>
      </div>

      {{-- BOUTONS : STRICTEMENT IDENTIQUES --}}
      <div class="d-flex justify-content-between mt-3">
        <button type="submit" class="btn btn-success"
                style="background-color: rgb(249, 138, 27); border: none;">
          Modifier
        </button>

        <button type="button" id="btn_show_montants" class="btn btn-info"
                style="background-color: rgb(144, 142, 140);">
          Afficher le montant à payer
        </button>

        <a href="{{ route('funding.list') }}" class="btn btn-secondary"
           style="background-color: rgb(144, 142, 140); border: none;">
          Annuler
        </a>
      </div>
    </form>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
function toggleOtherBank(select) {
  const other = document.getElementById('other_banque');
  if (select.value === 'Autres') {
    other.style.display = 'block';
    other.required = true;
  } else {
    other.style.display = 'none';
    other.required = false;
    other.value = '';
  }
}

document.addEventListener('DOMContentLoaded', () => {

  toggleOtherBank(document.getElementById('nom_de_banque'));

  document.getElementById('btn_show_montants').addEventListener('click', () => {
    const montant = parseFloat(document.getElementById('montant_emprunte').value.replace(/\s/g,'')) || 0;
    const jours = parseInt(document.querySelector('[name="nombre_de_jours"]').value) || 0;
    const taux = parseFloat(document.querySelector('[name="taux"]').value) || 0;
    const items = parseInt(document.querySelector('[name="nombre_d_items"]').value) || 1;

    const montantAPayer = ((montant * jours * (taux/100)) / 36000) * jours;
    const parItem = montantAPayer / items;

    document.getElementById('montant_a_payer').value = Math.round(montantAPayer).toLocaleString('fr-FR');
    document.getElementById('montant_a_payer_par_item').value = Math.round(parItem).toLocaleString('fr-FR');
  });

  document.getElementById('fundingForm').addEventListener('submit', function(e){
    e.preventDefault();
    Swal.fire({
      icon:'success',
      title:'Succès',
      text:'Le prêt bancaire a été modifié avec succès.',
      confirmButtonColor:'#90908c'
    }).then(() => this.submit());
  });

});
</script>
@endsection
