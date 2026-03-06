@extends('admin.layouts.master')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3" style="background-color:#6c757d;">
            <h5 class="m-0 font-weight-bold text-white">Ajouter un Prix Fournisseur</h5>
        </div>
        <form action="{{ route('articlePriceStore') }}" method="POST">
            @csrf

            {{-- Affichage global des erreurs --}}
            @if ($errors->any())
                <div class="alert alert-danger" style="margin: 10px 20px;">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="card-body" style="background-color:#6c757d;">
                <div class="row">
                    {{-- Colonne 1 --}}
                    <div class="col-6">
                        <div class="mb-3">
                            <label for="reference" class="form-label text-white">Référence</label>
                            <input type="text" name="reference" id="reference" class="form-control @error('reference') is-invalid @enderror" value="{{ old('reference') }}">
                            @error('reference')
                                <small class="invalid-feedback">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="designation" class="form-label text-white">Désignation / Machine <span class="text-danger">*</span></label>
                            <input type="text" name="designation" id="designation" class="form-control @error('designation') is-invalid @enderror" value="{{ old('designation') }}" required>
                            @error('designation')
                                <small class="invalid-feedback">{{ $message }}</small>
                            @enderror
                        </div>

                        {{-- Champ Prix unitaire formaté --}}
                        <div class="mb-3">
                            <label for="unit_price_display" class="form-label text-white">Prix unitaire <span class="text-danger">*</span></label>
                            <input type="text" id="unit_price_display" class="form-control" value="{{ old('unit_price') }}" placeholder required>
                            <input type="hidden" name="unit_price" id="unit_price" value="{{ old('unit_price') }}">
                            @error('unit_price')
                                <small class="invalid-feedback">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="type" class="form-label text-white">Type</label>
                            <select name="type" id="type" class="form-control @error('type') is-invalid @enderror">
                                <option value="">Sélectionnez un type</option>
                                <option value="Originale" {{ old('type') == 'Originale' ? 'selected' : '' }}>Originale</option>
                                <option value="Aftermarket" {{ old('type') == 'Aftermarket' ? 'selected' : '' }}>Aftermarket</option>
                            </select>
                            @error('type')
                                <small class="invalid-feedback">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="currency" class="form-label text-white">Devise</label>
                            <select name="currency" id="currency" class="form-control @error('currency') is-invalid @enderror">
                                @foreach(['FCFA', 'Dollar', 'Euro', 'Livre Sterling'] as $currency)
                                    <option value="{{ $currency }}" {{ old('currency') == $currency ? 'selected' : '' }}>{{ $currency }}</option>
                                @endforeach
                            </select>
                            @error('currency')
                                <small class="invalid-feedback">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    {{-- Colonne 2 --}}
                    <div class="col-6">
                        <div class="mb-3">
                            <label for="supplier_id" class="form-label text-white">Fournisseur</label>
                            <select name="supplier_id" id="supplier_id" class="form-control @error('supplier_id') is-invalid @enderror" onchange="toggleOtherSupplier(this)">
                                <option value="">Sélectionnez un fournisseur</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>{{ $supplier->company_name }}</option>
                                @endforeach
                                <option value="other" {{ old('supplier_name') ? 'selected' : '' }}>Autres</option>
                            </select>
                            @error('supplier_id')
                                <small class="invalid-feedback">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="mb-3" id="otherSupplierDiv" style="{{ old('supplier_name') ? 'display:block;' : 'display:none;' }}">
                            <input type="text" name="supplier_name" id="supplier_name" class="form-control @error('supplier_name') is-invalid @enderror" placeholder="Saisir le fournisseur..." value="{{ old('supplier_name') }}">
                            @error('supplier_name')
                                <small class="invalid-feedback">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="date" class="form-label text-white">Date</label>
                            <input type="date" name="date" id="date" class="form-control @error('date') is-invalid @enderror" value="{{ old('date') }}">
                            @error('date')
                                <small class="invalid-feedback">{{ $message }}</small>
                            @enderror
                        </div>

                        {{-- Champ Incoterm (Intercode) --}}
                        <div class="mb-3">
                            <label for="incoterm" class="form-label text-white">Incoterm</label>
                            <select name="incoterm" id="incoterm" class="form-control @error('incoterm') is-invalid @enderror">
                                <option value="">Sélectionnez un Incoterm</option>
                                @foreach(['EXW', 'FCA', 'FAS', 'FOB', 'CFR', 'CIF', 'CPT', 'CIP', 'DAP', 'DPU', 'DDP'] as $term)
                                    <option value="{{ $term }}" {{ old('incoterm') == $term ? 'selected' : '' }}>{{ $term }}</option>
                                @endforeach
                            </select>
                            @error('incoterm')
                                <small class="invalid-feedback">{{ $message }}</small>
                            @enderror
                        </div>

                        {{-- Champ Pays --}}
                        <div class="mb-3">
                            <label for="country" class="form-label text-white">Pays</label>
                            <select name="country" id="country" class="form-control @error('country') is-invalid @enderror">
                                <option value="">Sélectionnez un pays</option>
                                @php
                                    $countries = ["Afghanistan", "Afrique du Sud", "Albanie", "Algérie", "Allemagne", "Andorre", "Angola", "Arabie Saoudite", "Argentine", "Arménie", "Australie", "Autriche", "Azerbaïdjan", "Bahamas", "Bahreïn", "Bangladesh", "Belgique", "Bénin", "Bhoutan", "Biélorussie", "Birmanie", "Bolivie", "Bosnie-Herzégovine", "Botswana", "Brésil", "Brunei", "Bulgarie", "Burkina Faso", "Burundi", "Cambodge", "Cameroun", "Canada", "Chili", "Chine", "Chypre", "Colombie", "Congo", "Corée du Nord", "Corée du Sud", "Costa Rica", "Côte d'Ivoire", "Croatie", "Cuba", "Danemark", "Djibouti", "Égypte", "Émirats arabes unis", "Équateur", "Érythrée", "Espagne", "Estonie", "États-Unis", "Éthiopie", "Fidji", "Finlande", "France", "Gabon", "Gambie", "Géorgie", "Ghana", "Grèce", "Guatemala", "Guinée", "Guinée-Bissau", "Guyana", "Haïti", "Honduras", "Hongrie", "Inde", "Indonésie", "Irak", "Iran", "Irlande", "Islande", "Israël", "Italie", "Jamaïque", "Japon", "Jordanie", "Kazakhstan", "Kenya", "Kirghizistan", "Koweït", "Laos", "Lesotho", "Lettonie", "Liban", "Libéria", "Libye", "Lituanie", "Luxembourg", "Macédoine", "Madagascar", "Malaisie", "Malawi", "Maldives", "Mali", "Malte", "Maroc", "Maurice", "Mauritanie", "Mexique", "Moldavie", "Monaco", "Mongolie", "Monténégro", "Mozambique", "Namibie", "Népal", "Nicaragua", "Niger", "Nigéria", "Norvège", "Nouvelle-Zélande", "Oman", "Ouganda", "Ouzbékistan", "Pakistan", "Panama", "Papouasie-Nouvelle-Guinée", "Paraguay", "Pays-Bas", "Pérou", "Philippines", "Pologne", "Portugal", "Qatar", "République centrafricaine", "République dominicaine", "République tchèque", "Roumanie", "Royaume-Uni", "Russie", "Rwanda", "Sénégal", "Serbie", "Seychelles", "Sierra Leone", "Singapour", "Slovaquie", "Slovénie", "Somalie", "Soudan", "Sri Lanka", "Suède", "Suisse", "Syrie", "Tadjikistan", "Tanzanie", "Tchad", "Thaïlande", "Togo", "Tunisie", "Turkménistan", "Turquie", "Ukraine", "Uruguay", "Vatican", "Venezuela", "Viêt Nam", "Yémen", "Zambie", "Zimbabwe"];
                                @endphp
                                @foreach($countries as $country)
                                    <option value="{{ $country }}" {{ old('country') == $country ? 'selected' : '' }}>{{ $country }}</option>
                                @endforeach
                            </select>
                            @error('country')
                                <small class="invalid-feedback">{{ $message }}</small>
                            @enderror
                        </div>

                        {{-- Boutons --}}
                        <div class="row mt-4">
                            <div class="col-6">
                                <button type="submit" class="btn w-100 text-white" style="background-color: rgb(249, 138, 27); border: none;">Ajouter</button>
                            </div>
                            <div class="col-6">
                                <a href="{{ route('articlePriceList') }}" class="btn w-100 text-white" style="background-color: rgb(144, 142, 140); border: none;">Annuler</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
// Afficher ou cacher le champ "Autre fournisseur"
function toggleOtherSupplier(select) {
    const otherDiv = document.getElementById('otherSupplierDiv');
    const otherInput = document.getElementById('supplier_name');
    if (select.value === 'other') {
        otherDiv.style.display = 'block';
        otherInput.required = true;
    } else {
        otherDiv.style.display = 'none';
        otherInput.required = false;
        otherInput.value = '';
    }
}

// Formatage du prix unitaire
document.addEventListener('DOMContentLoaded', function () {
    const displayInput = document.getElementById('unit_price_display');
    const realInput = document.getElementById('unit_price');

    function formatNumber(value) {
        if (!value) return '';
        value = value.toString().replace(/\s+/g, '').replace(',', '.');
        let parts = value.split('.');
        parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
        return parts.join('.');
    }

    displayInput.addEventListener('input', function () {
        let val = this.value.replace(/\s+/g, '');
        if(!isNaN(val.replace(',', '.'))) {
            realInput.value = val.replace(',', '.');
            this.value = formatNumber(val);
        }
    });
});
</script>
@endsection