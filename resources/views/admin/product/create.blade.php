@extends('admin.layouts.master')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3" style="background-color:#6c757d;">
            <h5 class="m-0 font-weight-bold text-white">Ajouter un nouveau produit</h5>
        </div>
        <form id="productForm" action="{{ route('productCreate') }}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="card-body" style="background-color:#6c757d;">
                <div class="row">
                    <!-- Colonne de gauche (col-3) -->
                    <div class="col-3">
                        <!-- Référence -->
                        <div class="mb-3">
                            <label for="reference" class="form-label text-white fw-bold">Référence <span class="text-danger">*</span></label>
                            <select name="reference" id="reference" class="form-control @error('reference') is-invalid @enderror" required>
                                <option value="">Sélectionnez une référence</option>
                                @foreach($machines as $machine)
                                    <option value="{{ $machine->purchase_reference }}" @selected(old('reference') == $machine->purchase_reference)>
                                        {{ $machine->purchase_reference }}
                                    </option>
                                @endforeach
                            </select>
                            @error('reference')
                            <small class="invalid-feedback">{{ $message }}</small>
                            @enderror
                        </div>

                        <!-- Fournisseur -->
                        <div class="mb-3">
                            <label for="suppliername" class="form-label text-white fw-bold">Fournisseur</label>
                            <input type="text" name="suppliername" id="suppliername" class="form-control @error('suppliername') is-invalid @enderror" value="{{ old('suppliername') }}" readonly style="background-color: #e9ecef;">
                            @error('suppliername')
                            <small class="invalid-feedback">{{ $message }}</small>
                            @enderror
                        </div>

                        <!-- Nom du produit -->
                        <div class="mb-3">
                            <label for="name" class="form-label text-white fw-bold">Nom du produit <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" placeholder="Nom..." value="{{ old('name') }}" required>
                            @error('name')
                            <small class="invalid-feedback">{{ $message }}</small>
                            @enderror
                        </div>

                        <!-- Image -->
                        <div class="mb-3">
                            <label for="image" class="form-label text-white fw-bold">Image</label>
                            <input type="file" name="image" id="image" class="form-control @error('image') is-invalid @enderror" onchange="loadFile(event)">
                            @error('image')
                            <small class="invalid-feedback">{{ $message }}</small>
                            @enderror
                            <input type="hidden" name="oldImage" id="oldImage" value="{{ old('oldImage') }}">
                            <img id="output" class="img-thumbnail mt-2" src="{{ old('oldImage') ? asset('purchaseImages/' . old('oldImage')) : asset('defaultImg/default.jpg') }}" alt="Image produit" style="max-width: 100%; height: 200px; object-fit: cover; border: 2px solid white;">
                        </div>
                    </div>

                    <!-- Colonne de droite (Détails) -->
                    <div class="col">
                        <div class="row">
                            <!-- État -->
                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label text-white fw-bold">État <span class="text-danger">*</span></label>
                                <select name="status" id="status" class="form-control @error('status') is-invalid @enderror" required>
                                    <option value="">Sélectionnez l'état</option>
                                    <option value="Neuve" @selected(old('status') == 'Neuve')>Neuve</option>
                                    <option value="Occasion" @selected(old('status') == 'Occasion')>Occasion</option>
                                </select>
                                @error('status')
                                <small class="invalid-feedback">{{ $message }}</small>
                                @enderror
                            </div>

                            <!-- Marque -->
                            <div class="col-md-6 mb-3">
                                <label for="brandSelect" class="form-label text-white fw-bold">Marque <span class="text-danger">*</span></label>
                                <select name="brand" id="brandSelect" class="form-control @error('brand') is-invalid @enderror" required onchange="toggleOtherBrand(this)">
                                    <option value="">Sélectionnez la marque</option>
                                    @foreach(["CAT", "Komatsu", "Hitachi", "XCMG", "Sany", "Lovol", "Volvo", "Autres"] as $brand)
                                        <option value="{{ $brand }}" @selected(old('brand') == $brand)>{{ $brand }}</option>
                                    @endforeach
                                </select>
                                <input type="text" name="other_brand" id="otherBrandInput" class="form-control mt-2" placeholder="Saisir la marque..." style="display:none;" value="{{ old('other_brand') }}">
                            </div>
                        </div>

                        <div class="row">
                            <!-- NOUVEAU CHAMP : Date du produit -->
                            <div class="col-md-4 mb-3">
                                <label for="date_product" class="form-label text-white fw-bold">Date du produit <span class="text-danger">*</span></label>
                                <input type="date" name="date_product" id="date_product" class="form-control @error('date_product') is-invalid @enderror" value="{{ old('date_product', date('Y-m-d')) }}" required>
                                @error('date_product')
                                <small class="invalid-feedback">{{ $message }}</small>
                                @enderror
                            </div>

                            <!-- Prix -->
                            <div class="col-md-4 mb-3">
                                <label for="price" class="form-label text-white fw-bold">Prix de vente (FCFA)</label>
                                <input type="text" name="price" id="price" class="form-control @error('price') is-invalid @enderror" value="{{ old('price') }}" placeholder="0">
                                @error('price')
                                <small class="invalid-feedback">{{ $message }}</small>
                                @enderror
                            </div>

                            <!-- Quantité -->
                            <div class="col-md-4 mb-3">
                                <label for="count" class="form-label text-white fw-bold">Quantité en stock</label>
                                <input type="number" name="count" id="count" class="form-control @error('count') is-invalid @enderror" value="{{ old('count') }}" placeholder="0">
                                @error('count')
                                <small class="invalid-feedback">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label for="description" class="form-label text-white fw-bold">Description</label>
                            <textarea name="description" id="description" cols="30" rows="4" class="form-control @error('description') is-invalid @enderror" placeholder="Détails techniques...">{{ old('description') }}</textarea>
                            @error('description')
                            <small class="invalid-feedback">{{ $message }}</small>
                            @enderror
                        </div>

                        <!-- Boutons -->
                        <div class="row mt-4">
                            <div class="col-6">
                                <button type="submit" class="btn btn-warning w-100 text-white fw-bold" style="background-color: rgb(249, 138, 27); border: none;">
                                    <i class="fa-solid fa-plus-circle me-1"></i> Créer le produit
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
            </div>
        </form>
    </div>
</div>

<script>
// Gestion de l'affichage du champ "Autres" pour la marque
function toggleOtherBrand(select) {
    var otherInput = document.getElementById('otherBrandInput');
    if (select.value === 'Autres') {
        otherInput.style.display = 'block';
        otherInput.required = true;
    } else {
        otherInput.style.display = 'none';
        otherInput.required = false;
        otherInput.value = '';
    }
}

// Données des machines injectées depuis PHP
const machinesData = @json($machines->keyBy('purchase_reference'));

// Listener sur le changement de référence
document.getElementById('reference').addEventListener('change', function () {
    let val = this.value;
    if (val && machinesData[val]) {
        let data = machinesData[val];
        
        // Remplissage automatique
        document.getElementById('suppliername').value = data.suppliername || '';
        document.getElementById('price').value = Math.round(data.selling_price) || '';
        document.getElementById('count').value = data.quantity || '';
        document.getElementById('status').value = data.status || '';
        document.getElementById('brandSelect').value = data.brand || '';
        document.getElementById('description').value = data.request || ''; // On utilise la request comme base de desc.
        
        // Remplissage de la date (récupère la date de rentabilité si dispo)
        if(data.date_profitability) {
            // La date arrive souvent en format ISO (YYYY-MM-DDTHH:mm:ss...)
            let formattedDate = data.date_profitability.split('T')[0];
            document.getElementById('date_product').value = formattedDate;
        }

        // Image
        if (data.purchaseimage) {
            document.getElementById('output').src = "{{ asset('purchaseImages') }}/" + data.purchaseimage;
            document.getElementById('oldImage').value = data.purchaseimage;
        } else {
            document.getElementById('output').src = "{{ asset('defaultImg/default.jpg') }}";
            document.getElementById('oldImage').value = '';
        }
        
        toggleOtherBrand(document.getElementById('brandSelect'));
        formatPriceDisplay();
    }
});

// Prévisualisation de l'image
function loadFile(event) {
    var output = document.getElementById('output');
    if(event.target.files[0]) {
        output.src = URL.createObjectURL(event.target.files[0]);
        document.getElementById('oldImage').value = '';
    }
}

// Fonction pour formater le prix avec des espaces (visuel)
function formatPriceDisplay() {
    let priceInput = document.getElementById('price');
    let val = priceInput.value.replace(/\s+/g, '');
    if (!isNaN(val) && val !== '') {
        priceInput.value = Number(val).toLocaleString('fr-FR').replace(/,/g, ' ');
    }
}

document.addEventListener('DOMContentLoaded', function () {
    toggleOtherBrand(document.getElementById('brandSelect'));
    
    // Formatage du prix au fur et à mesure
    document.getElementById('price').addEventListener('input', formatPriceDisplay);

    // Nettoyage avant soumission
    document.getElementById('productForm').addEventListener('submit', function() {
        let priceInput = document.getElementById('price');
        priceInput.value = priceInput.value.replace(/\s+/g, ''); // Enlever les espaces pour le serveur
    });
});
</script>
@endsection