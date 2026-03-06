@extends('admin.layouts.master')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3" style="background-color:#6c757d;">
            <h5 class="m-0 font-weight-bold text-white">Mettez à jour votre produit</h5>
        </div>
        <div class="card-body" style="background-color:#6c757d;">
            <form action="{{ route('productUpdate') }}" method="post" enctype="multipart/form-data" id="productForm">
                @csrf
                @method('POST') {{-- Assurez-vous que votre route accepte POST ou PUT selon votre web.php --}}
                <input type="hidden" name="oldImage" id="oldImage" value="{{ old('oldImage', $product->image) }}">
                <input type="hidden" name="productID" value="{{ $product->id }}">

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
                    <!-- Colonne de gauche (Informations de base) -->
                    <div class="col-3">
                        <div class="mb-3">
                            <label for="reference" class="form-label text-white fw-bold">Référence <span class="text-danger">*</span></label>
                            <select name="reference" id="reference" class="form-control @error('reference') is-invalid @enderror" required>
                                <option value="">Sélectionnez une référence</option>
                                @foreach($machines as $machine)
                                    <option value="{{ $machine->purchase_reference }}" @selected(old('reference', $product->reference) == $machine->purchase_reference)>
                                        {{ $machine->purchase_reference }}
                                    </option>
                                @endforeach
                            </select>
                            @error('reference')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="suppliername" class="form-label text-white fw-bold">Fournisseur</label>
                            <input type="text" name="suppliername" id="suppliername" class="form-control @error('suppliername') is-invalid @enderror" value="{{ old('suppliername', $product->suppliername) }}" readonly style="background-color: #e9ecef;">
                            @error('suppliername')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="name" class="form-label text-white fw-bold">Nom du produit <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $product->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="image" class="form-label text-white fw-bold">Image</label>
                            <input type="file" name="image" id="image" class="form-control @error('image') is-invalid @enderror" onchange="loadFile(event)">
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <img id="output" class="img-thumbnail mt-2"
                                 src="{{ $product->image ? asset('purchaseImages/' . $product->image) : asset('defaultImg/default.jpg') }}"
                                 alt="Image du produit" style="max-width: 100%; height: 250px; object-fit: cover; border: 2px solid white;">
                        </div>
                    </div>

                    <!-- Colonne de droite (Détails et Statuts) -->
                    <div class="col">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label text-white fw-bold">État <span class="text-danger">*</span></label>
                                <select name="status" id="status" class="form-control @error('status') is-invalid @enderror" required>
                                    <option value="">Sélectionnez l'état</option>
                                    <option value="Neuve" @selected(old('status', $product->status) == 'Neuve')>Neuve</option>
                                    <option value="Occasion" @selected(old('status', $product->status) == 'Occasion')>Occasion</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="brandSelect" class="form-label text-white fw-bold">Marque <span class="text-danger">*</span></label>
                                @php
                                    $brands = ["CAT", "Komatsu", "Hitachi", "XCMG", "Sany", "Lovol", "Volvo", "Autres"];
                                    $currentBrand = old('brand', $product->brand);
                                    $isOther = !in_array($currentBrand, $brands) && $currentBrand != '';
                                @endphp
                                <select name="brand" id="brandSelect" class="form-control @error('brand') is-invalid @enderror" required onchange="toggleOtherBrand(this)">
                                    <option value="">Sélectionnez la marque</option>
                                    @foreach($brands as $brand)
                                        <option value="{{ $brand }}" @if($currentBrand == $brand || ($brand == "Autres" && $isOther)) selected @endif>{{ $brand }}</option>
                                    @endforeach
                                </select>
                                <input type="text" name="other_brand" id="otherBrandInput" class="form-control mt-2" placeholder="Saisir la marque..." style="{{ $isOther ? 'display: block;' : 'display: none;' }}" value="{{ $isOther ? $currentBrand : old('other_brand') }}">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="date_product" class="form-label text-white fw-bold">Date du produit <span class="text-danger">*</span></label>
                                <input type="date" name="date_product" id="date_product" class="form-control @error('date_product') is-invalid @enderror" value="{{ old('date_product', $product->date_product ? $product->date_product->format('Y-m-d') : '') }}" required>
                                @error('date_product')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="price" class="form-label text-white fw-bold">Prix de vente (FCFA)</label>
                                <input type="text" name="price" id="price" class="form-control @error('price') is-invalid @enderror"
                                       value="{{ old('price', number_format($product->price, 0, '', ' ')) }}">
                                @error('price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="count" class="form-label text-white fw-bold">Quantité</label>
                                <input type="number" name="count" id="count" class="form-control @error('count') is-invalid @enderror" value="{{ old('count', $product->count) }}">
                                @error('count')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label text-white fw-bold">Description</label>
                            <textarea name="description" id="description" cols="30" rows="5" class="form-control @error('description') is-invalid @enderror" placeholder="Détails techniques...">{{ old('description', $product->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row mt-4">
                            <div class="col-6">
                                <button type="submit" class="btn btn-warning w-100 text-white fw-bold" style="background-color: rgb(249, 138, 27); border: none;">
                                    <i class="fa-solid fa-sync-alt me-1"></i> Mettre à jour
                                </button>
                            </div>
                            <div class="col-6">
                                <a href="{{ route('productList') }}" class="btn btn-secondary w-100 fw-bold" style="background-color: rgb(144, 142, 140); border: none;">
                                    <i class="fa-solid fa-arrow-left me-1"></i> Annuler
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Gestion du champ "Autres" pour la marque
    function toggleOtherBrand(select) {
        let otherInput = document.getElementById('otherBrandInput');
        if (select.value === 'Autres') {
            otherInput.style.display = 'block';
            otherInput.required = true;
        } else {
            otherInput.style.display = 'none';
            otherInput.required = false;
            otherInput.value = '';
        }
    }

    // Prévisualisation de l'image
    function loadFile(event) {
        var output = document.getElementById('output');
        if(event.target.files[0]) {
            output.src = URL.createObjectURL(event.target.files[0]);
            document.getElementById('oldImage').value = '';
        }
    }

    // Formatage visuel du prix avec des espaces
    function formatPriceDisplay(input) {
        let val = input.value.replace(/\s+/g, '');
        if (!isNaN(val) && val !== '') {
            input.value = Number(val).toLocaleString('fr-FR').replace(/,/g, ' ');
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        const brandSelect = document.getElementById('brandSelect');
        toggleOtherBrand(brandSelect);

        const priceInput = document.getElementById('price');
        priceInput.addEventListener('input', function() {
            formatPriceDisplay(this);
        });

        // Nettoyage avant soumission pour envoyer un nombre pur au serveur
        document.getElementById('productForm').addEventListener('submit', function() {
            priceInput.value = priceInput.value.replace(/\s+/g, '');
        });
    });

    // Chargement automatique des informations depuis machines_profitabilities si la référence change
    const machinesData = @json($machines->keyBy('purchase_reference'));

    document.getElementById('reference').addEventListener('change', function () {
        const val = this.value;
        if(val && machinesData[val]) {
            const data = machinesData[val];
            document.getElementById('suppliername').value = data.suppliername || '';
            document.getElementById('price').value = Math.round(data.selling_price) || '';
            document.getElementById('count').value = data.quantity || '';
            document.getElementById('status').value = data.status || '';
            document.getElementById('brandSelect').value = data.brand || '';
            document.getElementById('description').value = data.request || '';
            
            // Mise à jour de la date si elle existe dans la rentabilité
            if(data.date_profitability) {
                document.getElementById('date_product').value = data.date_profitability.split('T')[0];
            }
            
            if(data.purchaseimage) {
                document.getElementById('output').src = "{{ asset('purchaseImages') }}/" + data.purchaseimage;
                document.getElementById('oldImage').value = data.purchaseimage;
            }
            
            formatPriceDisplay(document.getElementById('price'));
            toggleOtherBrand(document.getElementById('brandSelect'));
        }
    });
</script>
@endsection