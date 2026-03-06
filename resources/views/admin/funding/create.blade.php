@extends('admin.layouts.master')

@section('content')

<style>
  .required-star {
    color: red;
    font-weight: bold;
    margin-left: 4px;
  }
</style>

<div class="container-fluid">
  <div class="card shadow col-md-8 mx-auto" style="background-color:#6c757d; margin-left: -12px; margin-right: -12px;">
    <div class="card-header text-white" style="background-color:#6c757d; margin-left: -12px; margin-right: -12px;">
      <h5 class="m-0">Ajouter un prêt bancaire</h5>
    </div>

    {{-- Affichage des erreurs --}}
    @if ($errors->any())
    <div class="alert alert-danger mx-3 my-3">
      <ul>
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
    @endif

    <form id="fundingForm" action="{{ route('funding.store') }}" method="POST" class="p-4" style="background-color:#6c757d; margin-left: -12px; margin-right: -12px;">
      @csrf

      <div class="mb-3">
        <label for="motif" class="form-label text-white">
          Request <span class="required-star">*</span>
        </label>
        <input type="text" name="motif" id="motif" class="form-control @error('motif') is-invalid @enderror" value="{{ old('motif') }}" required>
        @error('motif')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>

      <div class="mb-3">
        <label for="nom_de_banque" class="form-label text-white">
          Banque <span class="required-star">*</span>
        </label>
        @php
          $banquesMali = [
            'BCEAO',
            'Banque Malienne de Solidarité (BMS-SA)',
            'Banque Internationale pour le Mali (BIM)',
            'Banque Nationale de Développement Agricole (BNDA)',
            'Coris Bank International Mali',
            'Ecobank Mali',
            'Société Générale Mali',
            'UBA Mali',
            'Union Commerciale du Mali (UCM)',
            'Autres'
          ];
        @endphp
        <select name="nom_de_banque" id="nom_de_banque" class="form-control @error('nom_de_banque') is-invalid @enderror" onchange="toggleOtherBank(this)" required>
          <option value="">Sélectionnez une banque</option>
          @foreach ($banquesMali as $banque)
            <option value="{{ $banque }}" {{ old('nom_de_banque') == $banque ? 'selected' : '' }}>
              {{ $banque }}
            </option>
          @endforeach
        </select>
        @error('nom_de_banque')<div class="invalid-feedback">{{ $message }}</div>@enderror

        <input type="text" name="other_banque" id="other_banque" class="form-control mt-2" placeholder="Saisir une autre banque..." style="display:none;" value="{{ old('other_banque') }}">
        @error('other_banque')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>

      <div class="mb-3">
        <label for="montant_emprunte" class="form-label text-white">
          Montant emprunté <span class="required-star">*</span>
        </label>
        <input type="text" name="montant_emprunte" id="montant_emprunte" class="form-control @error('montant_emprunte') is-invalid @enderror" value="{{ old('montant_emprunte') }}" required>
        @error('montant_emprunte')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>

      <div class="mb-3">
        <label for="nombre_de_jours" class="form-label text-white">
          Nombre de jours <span class="required-star">*</span>
        </label>
        <input type="number" min="1" name="nombre_de_jours" id="nombre_de_jours" class="form-control @error('nombre_de_jours') is-invalid @enderror" value="{{ old('nombre_de_jours') }}" required>
        @error('nombre_de_jours')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>

      <div class="mb-3">
        <label for="taux" class="form-label text-white">
          Taux (%) <span class="required-star">*</span>
        </label>
        <input type="number" step="0.0001" min="0" name="taux" id="taux" class="form-control @error('taux') is-invalid @enderror" value="{{ old('taux') }}" required>
        @error('taux')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>

      <div class="mb-3">
        <label for="nombre_d_items" class="form-label text-white">
          Nombre d'items <span class="required-star">*</span>
        </label>
        <input type="number" min="1" name="nombre_d_items" id="nombre_d_items" class="form-control @error('nombre_d_items') is-invalid @enderror" value="{{ old('nombre_d_items') }}" required>
        @error('nombre_d_items')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>

      <div class="mb-3">
        <label for="date" class="form-label text-white">
          Date <span class="required-star">*</span>
        </label>
        <input type="date" name="date" id="date" class="form-control @error('date') is-invalid @enderror" value="{{ old('date') }}" required>
        @error('date')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>

      <div class="mb-3">
        <label for="montant_a_payer" class="form-label text-white">Montant à payer</label>
        <input type="text" id="montant_a_payer" class="form-control" readonly>
      </div>

      <div class="mb-3">
        <label for="montant_a_payer_par_item" class="form-label text-white">Montant à payer par item</label>
        <input type="text" id="montant_a_payer_par_item" class="form-control" readonly>
      </div>

      <div class="d-flex justify-content-between mt-3">
        <button type="submit" class="btn btn-success" style="background-color: rgb(249, 138, 27); border: none;">Créer</button>
        <button type="button" id="btn_show_montants" class="btn btn-info" style="background-color: rgb(144, 142, 140);">Afficher le montant à payer</button>
        <a href="{{ route('funding.list') }}" class="btn btn-secondary" style="background-color: rgb(144, 142, 140); border: none;">Annuler</a>
      </div>
    </form>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

{{-- ⚠️ JS inchangé --}}
<script>
  function toggleOtherBank(select) {
    const otherBankInput = document.getElementById('other_banque');
    if (select.value === 'Autres') {
      otherBankInput.style.display = 'block';
      otherBankInput.required = true;
    } else {
      otherBankInput.style.display = 'none';
      otherBankInput.required = false;
      otherBankInput.value = '';
    }
  }

  function formatNumber(num) {
    let parts = num.toString().split('.');
    parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
    return parts.join('.');
  }

  document.addEventListener('DOMContentLoaded', () => {
    const banqueSelect = document.getElementById('nom_de_banque');
    toggleOtherBank(banqueSelect);
    banqueSelect.addEventListener('change', function() {
      toggleOtherBank(this);
    });

    const montantEmprunteInput = document.getElementById('montant_emprunte');
    montantEmprunteInput.addEventListener('blur', function() {
      let val = this.value.replace(/\s+/g, '').replace(/,/g, '.');
      if (!isNaN(val) && val !== '') {
        val = Math.round(parseFloat(val));
        this.value = formatNumber(val);
      } else {
        this.value = '';
      }
    });

    document.getElementById('btn_show_montants').addEventListener('click', () => {
      let montantEmprunte = parseFloat(document.getElementById('montant_emprunte').value.replace(/\s+/g, '')) || 0;
      let nombreDeJours = parseInt(document.getElementById('nombre_de_jours').value) || 0;
      let taux = parseFloat(document.getElementById('taux').value) || 0;
      let nombreDItems = parseInt(document.getElementById('nombre_d_items').value) || 1;

      let montantAPayer = ((montantEmprunte * nombreDeJours * (taux / 100)) / 36000) * nombreDeJours;

      document.getElementById('montant_a_payer').value = formatNumber(Math.round(montantAPayer));
      document.getElementById('montant_a_payer_par_item').value = formatNumber(Math.round(montantAPayer / nombreDItems));
    });

    document.getElementById('fundingForm').addEventListener('submit', function(e) {
      e.preventDefault();
      Swal.fire({
        icon: 'success',
        title: 'Succès',
        text: 'Le prêt bancaire a été créé avec succès.',
        confirmButtonColor: '#90908c',
        confirmButtonText: 'OK'
      }).then(() => this.submit());
    });
  });
</script>
@endsection
