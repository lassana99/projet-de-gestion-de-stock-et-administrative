@extends('admin.layouts.master')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3" style="background-color:#6c757d;">
            <h5 class="m-0 font-weight-bold text-white">Ajouter un nouvel achat / stock</h5>
        </div>
        <form id="purchaseForm" action="{{ route('purchaseStore') }}" method="POST" enctype="multipart/form-data">
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


            <div class="card-body" style="background-color:#6c757d; color: white;">
                <div class="row">


                    {{-- Colonne 1 --}}
                    <div class="col-3">
                        <div class="mb-3">
                            <label for="reference" class="form-label">Référence</label>
                            <input type="text" name="reference" id="reference" class="form-control @error('reference') is-invalid @enderror" value="{{ old('reference') }}">
                            @error('reference')
                                <small class="invalid-feedback">{{ $message }}</small>
                            @enderror
                        </div>


                        <div class="mb-3">
                            <label for="purchasename" class="form-label">Produit <span class="text-danger">*</span></label>
                            <input type="text" name="purchasename" id="purchasename" class="form-control @error('purchasename') is-invalid @enderror" value="{{ old('purchasename') }}" required>
                            @error('purchasename')
                                <small class="invalid-feedback">{{ $message }}</small>
                            @enderror
                        </div>

                        {{-- NOUVEAU CHAMP : Date d'achat --}}
                        <div class="mb-3">
                            <label for="date_purchase" class="form-label">Date d'achat <span class="text-danger">*</span></label>
                            <input type="date" name="date_purchase" id="date_purchase" class="form-control @error('date_purchase') is-invalid @enderror" value="{{ old('date_purchase', date('Y-m-d')) }}" required>
                            @error('date_purchase')
                                <small class="invalid-feedback">{{ $message }}</small>
                            @enderror
                        </div>


                        <div class="mb-3">
                            <label for="Category" class="form-label">Nom de la catégorie</label>
                            <select name="Category" id="Category" class="form-control @error('Category') is-invalid @enderror" onchange="toggleOtherCategory(this)">
                                <option value="">Choisissez le nom de la catégorie...</option>
                                <option value="Machine" {{ old('Category') == 'Machine' ? 'selected' : '' }}>Machine</option>
                                <option value="Pièce de rechange et accessoire" {{ old('Category') == 'Pièce de rechange et accessoire' ? 'selected' : '' }}>Pièce de rechange et accessoire</option>
                                <option value="Autres" {{ old('Category') == 'Autres' ? 'selected' : '' }}>Autres</option>
                            </select>
                            @error('Category')
                                <small class="invalid-feedback">{{ $message }}</small>
                            @enderror


                            <input type="text" name="other_category" id="otherCategoryInput" class="form-control mt-2 @error('other_category') is-invalid @enderror" placeholder="Saisir la catégorie..."
                                @if(old('Category') == 'Autres') style="display: block;" required @else style="display: none;" @endif
                                value="{{ old('other_category') }}">
                            @error('other_category')
                                <small class="invalid-feedback d-block">{{ $message }}</small>
                            @enderror
                        </div>


                        <div class="mb-3">
                            <label for="status" class="form-label">État</label>
                            <select name="status" id="status" class="form-control @error('status') is-invalid @enderror" required>
                                <option value="">Sélectionnez l'état</option>
                                <option value="Neuve" {{ old('status') == 'Neuve' ? 'selected' : '' }}>Neuve</option>
                                <option value="Occasion" {{ old('status') == 'Occasion' ? 'selected' : '' }}>Occasion</option>
                            </select>
                            @error('status')
                                <small class="invalid-feedback">{{ $message }}</small>
                            @enderror
                        </div>


                        <div class="mb-3">
                            <label for="brandSelect" class="form-label">Marque</label>
                            <select id="brandSelect" class="form-control @error('brand') is-invalid @enderror" onchange="toggleOtherBrand(this)">
                                <option value="" disabled {{ old('brand', '') == '' ? 'selected' : '' }}>Sélectionnez la marque</option>
                                @foreach(["CAT", "Komatsu", "Hitachi", "XCMG", "Sany", "Lovol", "Volvo", "Autres"] as $brand)
                                    <option value="{{ $brand }}" {{ old('brand', '') == $brand ? 'selected' : '' }}>{{ $brand }}</option>
                                @endforeach
                            </select>


                            <input type="hidden" name="brand" id="brandHidden" value="{{ old('brand', '') }}">


                            <input type="text" name="other_brand" id="otherBrandInput" class="form-control mt-2 @error('other_brand') is-invalid @enderror" placeholder="Saisir la marque..."
                                @if(old('brand') == 'Autres') style="display: block;" @else style="display: none;" @endif
                                value="{{ old('other_brand') }}">
                            @error('brand')
                                <small class="invalid-feedback d-block">{{ $message }}</small>
                            @enderror
                        </div>


                        {{-- Nouveau champ Moyen de transport --}}
                        <div class="mb-3">
                            <label for="transport_mode" class="form-label">Moyen de transport</label>
                            <select name="transport_mode" id="transport_mode" class="form-control @error('transport_mode') is-invalid @enderror">
                                <option value="">Sélectionnez un moyen de transport</option>
                                <option value="Bateau" {{ old('transport_mode') == 'Bateau' ? 'selected' : '' }}>Bateau</option>
                                <option value="Vol" {{ old('transport_mode') == 'Vol' ? 'selected' : '' }}>Vol</option>
                            </select>
                            @error('transport_mode')
                                <small class="invalid-feedback">{{ $message }}</small>
                            @enderror
                        </div>


                        <div class="mb-3">
                            <label for="purchaseimage" class="form-label">Image</label>
                            <input type="file" name="purchaseimage" id="purchaseimage" class="form-control mt-1 @error('purchaseimage') is-invalid @enderror" accept="image/*" onchange="loadFile(event)">
                            @error('purchaseimage')
                                <small class="invalid-feedback">{{ $message }}</small>
                            @enderror
                            <img class="img-thumbnail mt-2" src="{{ asset('defaultImg/default.jpg') }}" alt="Image preview" id="output" style="max-width:100%;">
                        </div>
                    </div>


                    {{-- Colonne 2 --}}
                    <div class="col-9">
                        <div class="mb-3">
                            <label for="purchaseprice" class="form-label">Prix d'achat unité <span class="text-danger">*</span></label>
                            <input type="text" name="purchaseprice" id="purchaseprice" class="form-control @error('purchaseprice') is-invalid @enderror" value="{{ old('purchaseprice') }}" required>
                            @error('purchaseprice')
                                <small class="invalid-feedback">{{ $message }}</small>
                            @enderror
                        </div>


                        <div class="mb-3">
                            <label for="weight" class="form-label">Poids (KG)</label>
                            <input type="number" step="0.01" name="weight" id="weight" class="form-control @error('weight') is-invalid @enderror" value="{{ old('weight') }}">
                            @error('weight')
                                <small class="invalid-feedback">{{ $message }}</small>
                            @enderror
                        </div>

                        {{-- Champ Incoterm ajouté ici --}}
                        <div class="mb-3">
                            <label for="incoterm" class="form-label">Incoterm</label>
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


                        <div class="mb-3">
                            <label for="currency" class="form-label">Devise</label>
                            <select name="currency" id="currency" class="form-control @error('currency') is-invalid @enderror">
                                <option value="FCFA" {{ old('currency') == 'FCFA' ? 'selected' : '' }}>FCFA</option>
                                <option value="Dollar" {{ old('currency') == 'Dollar' ? 'selected' : '' }}>Dollar</option>
                                <option value="Euro" {{ old('currency') == 'Euro' ? 'selected' : '' }}>Euro</option>
                                <option value="Livre Sterling" {{ old('currency') == 'Livre Sterling' ? 'selected' : '' }}>Livre Sterling</option>
                            </select>
                            @error('currency')
                                <small class="invalid-feedback">{{ $message }}</small>
                            @enderror
                        </div>


                        <div class="mb-3">
                            <label for="quantity" class="form-label">Quantité <span class="text-danger">*</span></label>
                            <input type="number" name="quantity" id="quantity" class="form-control @error('quantity') is-invalid @enderror" value="{{ old('quantity') }}" required>
                            @error('quantity')
                                <small class="invalid-feedback">{{ $message }}</small>
                            @enderror
                        </div>


                        {{-- Champ Fournisseur - Liste déroulante avec option Autres --}}
                        <div class="mb-3">
                            <label for="supplier_id" class="form-label">Fournisseur</label>
                            <select name="supplier_id" id="supplier_id" class="form-control @error('supplier_id') is-invalid @enderror" onchange="toggleOtherSupplier(this)">
                                <option value="">Sélectionnez un fournisseur</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                        {{ $supplier->company_name }}
                                    </option>
                                @endforeach
                                <option value="other" {{ old('supplier_id') == 'other' ? 'selected' : '' }}>Autres</option>
                            </select>
                            @error('supplier_id')
                                <small class="invalid-feedback">{{ $message }}</small>
                            @enderror
                        </div>


                        {{-- Champ texte pour saisir un fournisseur personnalisé sans label --}}
                        <div class="mb-3" id="otherSupplierDiv" style="display:none;">
                            <input type="text" name="suppliername" id="suppliername" class="form-control @error('suppliername') is-invalid @enderror" placeholder="Saisir le fournisseur..." value="{{ old('suppliername') }}">
                            @error('suppliername')
                                <small class="invalid-feedback">{{ $message }}</small>
                            @enderror
                        </div>


                        <div class="mb-3">
                            <label for="country" class="form-label">Pays</label>
                            <select name="country" id="country" class="form-control @error('country') is-invalid @enderror">
                                @php
                                    $countries = [
                                        'Afghanistan', 'Afrique du Sud', 'Albanie', 'Algérie', 'Allemagne', 'Andorre', 'Angola', 'Antigua-et-Barbuda',
                                        'Arabie Saoudite', 'Argentine', 'Arménie', 'Australie', 'Autriche', 'Azerbaïdjan', 'Bahamas', 'Bahreïn',
                                        'Bangladesh', 'Barbade', 'Belgique', 'Belize', 'Bénin', 'Bhoutan', 'Biélorussie', 'Bolivie',
                                        'Bosnie-Herzégovine', 'Botswana', 'Brésil', 'Brunei', 'Bulgarie', 'Burkina Faso', 'Burundi',
                                        'Cambodge', 'Cameroun', 'Canada', 'Cap-Vert', 'Chili', 'Chine', 'Chypre', 'Colombie', 'Comores',
                                        'Congo (Brazzaville)', 'Congo (Kinshasa)', 'Corée du Nord', 'Corée du Sud', 'Costa Rica', 'Croatie',
                                        'Cuba', 'Danemark', 'Djibouti', 'Dominique', 'Égypte', 'Émirats arabes unis', 'Équateur', 'Érythrée',
                                        'Espagne', 'Estonie', 'États-Unis', 'Éthiopie', 'Fidji', 'Finlande', 'France', 'Gabon', 'Gambie',
                                        'Géorgie', 'Ghana', 'Grèce', 'Grenade', 'Guatemala', 'Guinée', 'Guinée-Bissau', 'Guyana', 'Haïti',
                                        'Honduras', 'Hongrie', 'Inde', 'Indonésie', 'Irak', 'Iran', 'Irlande', 'Islande', 'Israël', 'Italie',
                                        'Jamaïque', 'Japon', 'Jordanie', 'Kazakhstan', 'Kenya', 'Kirghizistan', 'Kiribati', 'Koweït', 'Laos',
                                        'Lettonie', 'Liban', 'Libéria', 'Libye', 'Liechtenstein', 'Lituanie', 'Luxembourg', 'Macédoine',
                                        'Madagascar', 'Malaisie', 'Malawi', 'Maldives', 'Mali', 'Malte', 'Maroc', 'Marshall', 'Maurice',
                                        'Mauritanie', 'Mexique', 'Micronésie', 'Moldavie', 'Monaco', 'Mongolie', 'Monténégro', 'Mozambique',
                                        'Namibie', 'Nauru', 'Népal', 'Nicaragua', 'Niger', 'Nigeria', 'Norvège', 'Nouvelle-Zélande',
                                        'Oman', 'Ouganda', 'Ouzbékistan', 'Pakistan', 'Palaos', 'Panama', 'Papouasie-Nouvelle-Guinée',
                                        'Paraguay', 'Pays-Bas', 'Pérou', 'Philippines', 'Pologne', 'Portugal', 'Qatar', 'République centrafricaine',
                                        'République tchèque', 'Roumanie', 'Royaume-Uni', 'Russie', 'Rwanda', 'Saint-Christophe-et-Niévès',
                                        'Sainte-Lucie', 'Saint-Vincent-et-les-Grenadines', 'Salomon', 'Salvador', 'Samoa', 'Sao Tomé-et-Principe',
                                        'Sénégal', 'Serbie', 'Seychelles', 'Sierra Leone', 'Singapour', 'Slovaquie', 'Slovénie', 'Somalie',
                                        'Soudan', 'Sri Lanka', 'Suède', 'Suisse', 'Suriname', 'Swaziland', 'Syrie', 'Tadjikistan', 'Tanzanie',
                                        'Tchad', 'Thaïlande', 'Timor oriental', 'Togo', 'Tonga', 'Trinité-et-Tobago', 'Tunisie', 'Turkménistan',
                                        'Turquie', 'Tuvalu', 'Ukraine', 'Uruguay', 'Vanuatu', 'Vatican', 'Venezuela', 'Viêt Nam', 'Yémen',
                                        'Zambie', 'Zimbabwe'
                                    ];
                                @endphp
                                <option value="">Sélectionnez un pays</option>
                                @foreach ($countries as $country)
                                    <option value="{{ $country }}" {{ old('country') == $country ? 'selected' : '' }}>{{ $country }}</option>
                                @endforeach
                            </select>
                            @error('country')
                                <small class="invalid-feedback">{{ $message }}</small>
                            @enderror
                        </div>


                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" rows="5">{{ old('description') }}</textarea>
                            @error('description')
                                <small class="invalid-feedback">{{ $message }}</small>
                            @enderror
                        </div>


                        <div class="row d-flex justify-content-end align-items-start" style="margin-right: 140px;">
                            <div class="col-5">
                                <button type="submit" class="btn w-100 text-white mt-2" style="background-color: rgb(249, 138, 27); border: none;">Ajouter</button>
                            </div>
                            <div class="col-5">
                                <a href="{{ route('purchaseList') }}" class="btn w-100 text-white mt-2 text-center" style="background-color: rgb(144, 142, 140); border: none;">Annuler</a>
                            </div>
                        </div>


                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    function toggleOtherBrand(select) {
        const otherInput = document.getElementById('otherBrandInput');
        const brandHidden = document.getElementById('brandHidden');
        if (select.value === 'Autres') {
            otherInput.style.display = 'block';
            otherInput.required = true;
            brandHidden.value = '';
        } else {
            otherInput.style.display = 'none';
            otherInput.required = false;
            otherInput.value = '';
            brandHidden.value = select.value;
        }
    }

    function toggleOtherCategory(select) {
        const otherInput = document.getElementById('otherCategoryInput');
        if (select.value === 'Autres') {
            otherInput.style.display = 'block';
            otherInput.required = true;
        } else {
            otherInput.style.display = 'none';
            otherInput.required = false;
            otherInput.value = '';
        }
    }

    function toggleOtherSupplier(select) {
        const otherSupplierDiv = document.getElementById('otherSupplierDiv');
        if (select.value === 'other') {
            otherSupplierDiv.style.display = 'block';
            document.getElementById('suppliername').required = true;
        } else {
            otherSupplierDiv.style.display = 'none';
            document.getElementById('suppliername').required = false;
            document.getElementById('suppliername').value = '';
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        const brandSelect = document.getElementById('brandSelect');
        const otherBrandInput = document.getElementById('otherBrandInput');
        const brandHidden = document.getElementById('brandHidden');
        if (brandSelect.value === 'Autres') {
            otherBrandInput.style.display = 'block';
            otherBrandInput.required = true;
            brandHidden.value = '';
        } else {
            otherBrandInput.style.display = 'none';
            otherBrandInput.required = false;
            brandHidden.value = brandSelect.value;
        }
        brandSelect.addEventListener('change', function () {
            if (this.value !== 'Autres') {
                brandHidden.value = this.value;
            }
            toggleOtherBrand(this);
        });


        const categorySelect = document.getElementById('Category');
        toggleOtherCategory(categorySelect);
        categorySelect.addEventListener('change', function () {
            toggleOtherCategory(this);
        });


        const supplierSelect = document.getElementById('supplier_id');
        toggleOtherSupplier(supplierSelect);
        supplierSelect.addEventListener('change', function () {
            toggleOtherSupplier(this);
        });


        const purchasepriceInput = document.getElementById('purchaseprice');
        purchasepriceInput.addEventListener('blur', function () {
            let val = this.value.replace(/\s+/g, '').replace(/,/g, '.');
            if (!isNaN(val) && val !== '') {
                val = Math.round(parseFloat(val));
                this.value = val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
            } else {
                this.value = '';
            }
        });
    });


    var loadFile = function(event) {
        const output = document.getElementById('output');
        output.src = URL.createObjectURL(event.target.files[0]);
        output.onload = function() {
            URL.revokeObjectURL(output.src);
        }
    };
</script>
@endsection