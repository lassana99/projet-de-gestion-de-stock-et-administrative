@extends('admin.layouts.master')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3" style="background-color:#6c757d;">
            <h5 class="m-0 font-weight-bold text-white">Modifier l'achat / stock #{{ $purchase->id }}</h5>
        </div>
        <form id="purchaseForm" action="{{ route('purchaseUpdate', $purchase->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

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

            @php
            function formatValue($value)
            {
                if ($value === null) return '';
                // Formatage pour l'affichage (milliers séparés par espace)
                return ($value > 1) ? number_format(round($value), 0, ',', ' ') : number_format($value, 2, ',', ' ');
            }
            @endphp

            <div class="card-body" style="background-color:#6c757d; color: white;">
                <div class="row">

                    {{-- Colonne 1 --}}
                    <div class="col-3">
                        <div class="mb-3">
                            <label for="reference" class="form-label">Référence</label>
                            <input type="text" name="reference" id="reference" class="form-control @error('reference') is-invalid @enderror" value="{{ old('reference', $purchase->reference) }}">
                            @error('reference')
                            <small class="invalid-feedback">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="purchasename" class="form-label">Produit <span class="text-danger">*</span></label>
                            <input type="text" name="purchasename" id="purchasename" class="form-control @error('purchasename') is-invalid @enderror" value="{{ old('purchasename', $purchase->purchasename) }}" required>
                            @error('purchasename')
                            <small class="invalid-feedback">{{ $message }}</small>
                            @enderror
                        </div>

                        {{-- NOUVEAU CHAMP : Date d'achat --}}
                        <div class="mb-3">
                            <label for="date_purchase" class="form-label">Date d'achat <span class="text-danger">*</span></label>
                            <input type="date" name="date_purchase" id="date_purchase" class="form-control @error('date_purchase') is-invalid @enderror" value="{{ old('date_purchase', $purchase->date_purchase ? $purchase->date_purchase->format('Y-m-d') : '') }}" required>
                            @error('date_purchase')
                            <small class="invalid-feedback">{{ $message }}</small>
                            @enderror
                        </div>

                        @php
                        $categories = ['Machine', 'Pièce de rechange et accessoire', 'Autres'];
                        $currentCategory = old('Category', $purchase->Category);
                        $isOtherCategory = !in_array($currentCategory, $categories);
                        @endphp
                        <div class="mb-3">
                            <label for="Category" class="form-label">Nom de la catégorie</label>
                            <select name="Category" id="Category" class="form-control @error('Category') is-invalid @enderror" onchange="toggleOtherCategory(this)">
                                <option value="">Choisissez le nom de la catégorie...</option>
                                @foreach($categories as $cat)
                                <option value="{{ $cat }}" {{ (old('Category', $purchase->Category) == $cat) || ($cat == 'Autres' && $isOtherCategory) ? 'selected' : '' }}>
                                    {{ $cat }}
                                </option>
                                @endforeach
                            </select>
                            <input type="text" name="other_category" id="otherCategoryInput" class="form-control mt-2 @error('other_category') is-invalid @enderror" placeholder="Saisir la catégorie" @if($isOtherCategory) style="display: block;" required @else style="display: none;" @endif value="{{ $isOtherCategory ? $currentCategory : old('other_category') }}">
                            @error('Category')
                            <small class="invalid-feedback d-block">{{ $message }}</small>
                            @enderror
                        </div>

                        @php
                        $brands = ["CAT", "Komatsu", "Hitachi", "XCMG", "Sany", "Lovol", "Volvo", "Autres"];
                        $currentBrand = old('brand', $purchase->brand);
                        $isOtherBrand = !in_array($currentBrand, $brands) && $currentBrand != '';
                        @endphp
                        <div class="mb-3">
                            <label for="brandSelect" class="form-label">Marque</label>
                            <select id="brandSelect" class="form-control @error('brand') is-invalid @enderror" onchange="toggleOtherBrand(this)">
                                <option value="" disabled {{ old('brand', $purchase->brand ?? '') == '' ? 'selected' : '' }}>Sélectionnez la marque</option>
                                @foreach ($brands as $brand)
                                <option value="{{ $brand }}" {{ ($currentBrand == $brand) || ($brand == 'Autres' && $isOtherBrand) ? 'selected' : '' }}>
                                    {{ $brand }}
                                </option>
                                @endforeach
                            </select>
                            <input type="hidden" name="brand" id="brandHidden" value="{{ old('brand', $purchase->brand) }}">
                            <input type="text" name="other_brand" id="otherBrandInput" class="form-control mt-2 @error('other_brand') is-invalid @enderror" placeholder="Saisir la marque" @if($isOtherBrand) style="display: block;" @else style="display: none;" @endif value="{{ $isOtherBrand ? $currentBrand : old('other_brand') }}">
                            @error('brand')
                            <small class="invalid-feedback d-block">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="transport_mode" class="form-label">Moyen de transport</label>
                            <select name="transport_mode" id="transport_mode" class="form-control @error('transport_mode') is-invalid @enderror">
                                <option value="">Sélectionnez un moyen de transport</option>
                                <option value="Bateau" {{ old('transport_mode', $purchase->transport_mode) == 'Bateau' ? 'selected' : '' }}>Bateau</option>
                                <option value="Vol" {{ old('transport_mode', $purchase->transport_mode) == 'Vol' ? 'selected' : '' }}>Vol</option>
                            </select>
                            @error('transport_mode')
                            <small class="invalid-feedback">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="purchaseimage" class="form-label">Image</label>
                            <img src="{{ $purchase->purchaseimage ? asset('purchaseImages/' . $purchase->purchaseimage) : asset('defaultImg/default.jpg') }}" alt="Image achat" id="output" class="img-thumbnail mt-2" style="max-width: 100%;">
                            <input type="file" name="purchaseimage" id="purchaseimage" class="form-control mt-2 @error('purchaseimage') is-invalid @enderror" accept="image/*" onchange="loadFile(event)">
                            @error('purchaseimage')
                            <small class="invalid-feedback">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    {{-- Colonne 2 --}}
                    <div class="col-9">
                        <div class="mb-3">
                            <label for="purchaseprice" class="form-label text-white">Prix d'achat unité <span class="text-danger">*</span></label>
                            <input type="text" name="purchaseprice" id="purchaseprice" class="form-control @error('purchaseprice') is-invalid @enderror" value="{{ old('purchaseprice', formatValue($purchase->purchaseprice)) }}" required>
                            @error('purchaseprice')
                            <small class="invalid-feedback">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="weight" class="form-label text-white">Poids (KG)</label>
                            <input type="text" step="0.01" name="weight" id="weight" class="form-control @error('weight') is-invalid @enderror" value="{{ old('weight', formatValue($purchase->weight)) }}">
                            @error('weight')
                            <small class="invalid-feedback">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="incoterm" class="form-label text-white">Incoterm</label>
                            <select name="incoterm" id="incoterm" class="form-control @error('incoterm') is-invalid @enderror">
                                <option value="">Sélectionnez un Incoterm</option>
                                @foreach(['EXW', 'FCA', 'FAS', 'FOB', 'CFR', 'CIF', 'CPT', 'CIP', 'DAP', 'DPU', 'DDP'] as $term)
                                    <option value="{{ $term }}" {{ old('incoterm', $purchase->incoterm) == $term ? 'selected' : '' }}>
                                        {{ $term }}
                                    </option>
                                @endforeach
                            </select>
                            @error('incoterm')
                            <small class="invalid-feedback">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="currency" class="form-label text-white">Devise</label>
                            @php
                            $currencies = ['FCFA', 'Dollar', 'Euro', 'Livre Sterling'];
                            @endphp
                            <select name="currency" id="currency" class="form-control @error('currency') is-invalid @enderror">
                                @foreach($currencies as $currency)
                                <option value="{{ $currency }}" {{ old('currency', $purchase->currency ?? '') == $currency ? 'selected' : '' }}>
                                    {{ $currency }}
                                </option>
                                @endforeach
                            </select>
                            @error('currency')
                            <small class="invalid-feedback">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="quantity" class="form-label text-white">Quantité <span class="text-danger">*</span></label>
                            <input type="text" name="quantity" id="quantity" class="form-control @error('quantity') is-invalid @enderror" value="{{ old('quantity', formatValue($purchase->quantity)) }}" required>
                            @error('quantity')
                            <small class="invalid-feedback">{{ $message }}</small>
                            @enderror
                        </div>

                        @php
                        $currentSupplierId = old('supplier_id', $purchase->supplier_id ?? '');
                        $showOtherSupplier = $currentSupplierId === 'other' || ($currentSupplierId === '' && old('suppliername', $purchase->suppliername));
                        @endphp
                        <div class="mb-3">
                            <label for="supplier_id" class="form-label">Fournisseur</label>
                            <select name="supplier_id" id="supplier_id" class="form-control @error('supplier_id') is-invalid @enderror" onchange="toggleOtherSupplier(this)">
                                <option value="">Sélectionnez un fournisseur</option>
                                @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" {{ $currentSupplierId == $supplier->id ? 'selected' : '' }}>
                                    {{ $supplier->company_name }}
                                </option>
                                @endforeach
                                <option value="other" {{ $showOtherSupplier ? 'selected' : '' }}>Autres</option>
                            </select>
                            @error('supplier_id')
                            <small class="invalid-feedback">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="mb-3" id="otherSupplierDiv" style="{{ $showOtherSupplier ? 'display:block;' : 'display:none;' }}">
                            <input type="text" name="suppliername" id="suppliername" class="form-control @error('suppliername') is-invalid @enderror" placeholder="Saisir le fournisseur..." value="{{ old('suppliername', $purchase->suppliername) }}">
                            @error('suppliername')
                            <small class="invalid-feedback">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="country" class="form-label text-white">Pays</label>
                            <select name="country" id="country" class="form-control @error('country') is-invalid @enderror">
                                @php
                                $countries = ['Afghanistan', 'Afrique du Sud', 'Albanie', 'Algérie', 'Allemagne', 'Andorre', 'Angola', 'Antigua-et-Barbuda', 'Arabie Saoudite', 'Argentine', 'Arménie', 'Australie', 'Autriche', 'Azerbaïdjan', 'Bahamas', 'Bahreïn', 'Bangladesh', 'Barbade', 'Belgique', 'Belize', 'Bénin', 'Bhoutan', 'Biélorussie', 'Bolivie', 'Bosnie-Herzégovine', 'Botswana', 'Brésil', 'Brunei', 'Bulgarie', 'Burkina Faso', 'Burundi', 'Cambodge', 'Cameroun', 'Canada', 'Cap-Vert', 'Chili', 'Chine', 'Chypre', 'Colombie', 'Comores', 'Congo (Brazzaville)', 'Congo (Kinshasa)', 'Corée du Nord', 'Corée du Sud', 'Costa Rica', 'Croatie', 'Cuba', 'Danemark', 'Djibouti', 'Dominique', 'Égypte', 'Émirats arabes unis', 'Équateur', 'Érythrée', 'Espagne', 'Estonie', 'États-Unis', 'Éthiopie', 'Fidji', 'Finlande', 'France', 'Gabon', 'Gambie', 'Géorgie', 'Ghana', 'Grèce', 'Grenade', 'Guatemala', 'Guinée', 'Guinée-Bissau', 'Guyana', 'Haïti', 'Honduras', 'Hongrie', 'Inde', 'Indonésie', 'Irak', 'Iran', 'Irlande', 'Islande', 'Israël', 'Italie', 'Jamaïque', 'Japon', 'Jordanie', 'Kazakhstan', 'Kenya', 'Kirghizistan', 'Kiribati', 'Koweït', 'Laos', 'Lettonie', 'Liban', 'Libéria', 'Libye', 'Liechtenstein', 'Lituanie', 'Luxembourg', 'Macédoine', 'Madagascar', 'Malaisie', 'Malawi', 'Maldives', 'Mali', 'Malte', 'Maroc', 'Marshall', 'Maurice', 'Mauritanie', 'Mexique', 'Micronésie', 'Moldavie', 'Monaco', 'Mongolie', 'Monténégro', 'Mozambique', 'Namibie', 'Nauru', 'Népal', 'Nicaragua', 'Niger', 'Nigeria', 'Norvège', 'Nouvelle-Zélande', 'Oman', 'Ouganda', 'Ouzbékistan', 'Pakistan', 'Palaos', 'Panama', 'Papouasie-Nouvelle-Guinée', 'Paraguay', 'Pays-Bas', 'Pérou', 'Philippines', 'Pologne', 'Portugal', 'Qatar', 'République centrafricaine', 'République tchèque', 'Roumanie', 'Royaume-Uni', 'Russie', 'Rwanda', 'Saint-Christophe-et-Niévès', 'Sainte-Lucie', 'Saint-Vincent-et-les-Grenadines', 'Salomon', 'Salvador', 'Samoa', 'Sao Tomé-et-Principe', 'Sénégal', 'Serbie', 'Seychelles', 'Sierra Leone', 'Singapour', 'Slovaquie', 'Slovénie', 'Somalie', 'Soudan', 'Sri Lanka', 'Suède', 'Suisse', 'Suriname', 'Swaziland', 'Syrie', 'Tadjikistan', 'Tanzanie', 'Tchad', 'Thaïlande', 'Timor oriental', 'Togo', 'Tonga', 'Trinité-et-Tobago', 'Tunisie', 'Turkménistan', 'Turquie', 'Tuvalu', 'Ukraine', 'Uruguay', 'Vanuatu', 'Vatican', 'Venezuela', 'Viêt Nam', 'Yémen', 'Zambie', 'Zimbabwe'];
                                @endphp
                                <option value="">Sélectionnez un pays</option>
                                @foreach ($countries as $country)
                                <option value="{{ $country }}" {{ old('country', $purchase->country) == $country ? 'selected' : '' }}>
                                    {{ $country }}
                                </option>
                                @endforeach
                            </select>
                            @error('country')
                            <small class="invalid-feedback">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label text-white">Description</label>
                            <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" rows="7">{{ old('description', $purchase->description) }}</textarea>
                            @error('description')
                            <small class="invalid-feedback">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="row d-flex justify-content-end align-items-start" style="margin-right: 140px;">
                            <div class="col-5">
                                <button type="submit" class="btn w-100 text-white mt-2" style="background-color: rgb(249, 138, 27); border: none;">Mettre à jour</button>
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