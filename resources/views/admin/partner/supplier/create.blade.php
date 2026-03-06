@extends('admin.layouts.master')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4 col-lg-11 mx-auto">

        {{-- Header --}}
        <div class="card-header py-3" style="background-color:#6c757d; margin-left: -12px; margin-right: -12px;">
            <h5 class="m-0 font-weight-bold text-white">
                Ajouter un nouveau fournisseur
            </h5>
        </div>

        <form action="{{ route('supplierCreate') }}" method="post">
            @csrf

            <div class="card-body" style="background-color:#6c757d; margin-left: -12px; margin-right: -12px;">

                {{-- ===================== --}}
                {{-- INFORMATIONS ENTREPRISE --}}
                {{-- ===================== --}}
                <div class="card mb-4 shadow-sm">
                    <div class="card-header bg-dark text-white">
                        Informations sur l’entreprise
                    </div>

                    <div class="card-body" style="background-color:#6c757d;">
                        <div class="row">
                            {{-- Nom société --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-white">Nom de la société <span class="text-danger">*</span></label>
                                <input type="text" name="company_name"
                                       class="form-control @error('company_name') is-invalid @enderror"
                                       value="{{ old('company_name') }}">
                                @error('company_name') <small class="invalid-feedback">{{ $message }}</small> @enderror
                            </div>

                            {{-- Pays --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-white">Pays d'origine</label>
                                <select name="country_origin"
                                        class="form-control @error('country_origin') is-invalid @enderror">
                                    <option value="">Sélectionnez un pays</option>
                                    @foreach($countries as $country)
                                        <option value="{{ $country }}" {{ old('country_origin') == $country ? 'selected' : '' }}>
                                            {{ $country }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Activité --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-white">Activité</label>
                                <input type="text" name="specialty"
                                       class="form-control @error('specialty') is-invalid @enderror"
                                       value="{{ old('specialty') }}">
                            </div>

                            {{-- Marque --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-white">Marque <span class="text-danger">*</span></label>
                                <select name="brand" id="brandSelect"
                                        class="form-control @error('brand') is-invalid @enderror"
                                        onchange="toggleOtherBrand(this)">
                                    <option value="">Sélectionnez la marque</option>
                                    <option value="CAT">CAT</option>
                                    <option value="Komatsu">Komatsu</option>
                                    <option value="Hitachi">Hitachi</option>
                                    <option value="XCMG">XCMG</option>
                                    <option value="Sany">Sany</option>
                                    <option value="Lovol">Lovol</option>
                                    <option value="Volvo">Volvo</option>
                                    <option value="Autres">Autres</option>
                                </select>

                                <input type="text" name="other_brand" id="otherBrandInput"
                                       class="form-control mt-2"
                                       placeholder="Saisir la marque..."
                                       style="display:none"
                                       value="{{ old('other_brand') }}">
                                @error('brand') <small class="invalid-feedback">{{ $message }}</small> @enderror
                            </div>

                            {{-- Site Web --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-white">Site web</label>
                                <input type="text" name="website"
                                       class="form-control @error('website') is-invalid @enderror"
                                       value="{{ old('website') }}">
                            </div>

                            {{-- Échéance Paiement --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-white">Échéance de paiement</label>
                                <select name="payment_deadline"
                                        class="form-control @error('payment_deadline') is-invalid @enderror">
                                    <option value="">Sélectionner une échéance</option>
                                    <option value="Immédiat/Paiement avant commande">Immédiat/Paiement avant commande</option>
                                    <option value="15 jours">15 jours</option>
                                    <option value="30 jours fin de mois">30 jours fin de mois</option>
                                    <option value="60 jours fin de mois">60 jours fin de mois</option>
                                    <option value="Paiement à la réception">Paiement à la réception</option>
                                </select>
                            </div>

                            {{-- NIF --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-white">Enregistrement / NIF</label>
                                <input type="text" name="nif"
                                       class="form-control @error('nif') is-invalid @enderror"
                                       value="{{ old('nif') }}">
                            </div>

                            {{-- DATE --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-white">Date</label>
                                <input type="date" name="date"
                                       class="form-control @error('date') is-invalid @enderror"
                                       value="{{ old('date', date('Y-m-d')) }}">
                                @error('date') <small class="invalid-feedback">{{ $message }}</small> @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ===================== --}}
                {{-- PERSONNES À CONTACTER --}}
                {{-- ===================== --}}
                <div class="card mb-4 shadow-sm">
                    <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                        <span>Personnes à contacter</span>
                        <button type="button" class="btn btn-sm btn-light fw-bold" id="add-contact">
                            <i class="fa-solid fa-plus"></i> Ajouter un contact
                        </button>
                    </div>

                    <div class="card-body" style="background-color:#6c757d;" id="contacts-container">
                        {{-- Première ligne de contact --}}
                        <div class="row contact-row mb-3 pb-3 border-bottom border-secondary">
                            <div class="col-md-3">
                                <label class="form-label text-white">Nom du contact</label>
                                <input type="text" name="contact_names[]" class="form-control" placeholder="Nom complet">
                            </div>

                            {{-- Nouveau champ Poste --}}
                            <div class="col-md-2">
                                <label class="form-label text-white">Poste / Fonction</label>
                                <input type="text" name="contact_positions[]" class="form-control" placeholder="Ex: Commercial">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label text-white">Téléphone</label>
                                <input type="text" name="contact_phones[]" class="form-control" placeholder="N° Téléphone">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label text-white">Email</label>
                                <input type="email" name="contact_emails[]" class="form-control" placeholder="Adresse email">
                            </div>

                            <div class="col-md-1 d-flex align-items-end">
                                {{-- Bouton supprimer caché pour la première ligne --}}
                                <button type="button" class="btn btn-danger remove-contact" style="display: none;">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ACTIONS --}}
                <div class="row">
                    <div class="col-md-6">
                        <button type="submit"
                                class="btn w-100 text-white shadow-sm"
                                style="background-color: rgb(249, 138, 27); border: none;">
                            Enregistrer le fournisseur
                        </button>
                    </div>
                    <div class="col-md-6">
                        <a href="{{ route('supplierList') }}"
                           class="btn w-100 text-white shadow-sm"
                           style="background-color:rgb(144,142,140);">
                            Annuler
                        </a>
                    </div>
                </div>

            </div>
        </form>
    </div>
</div>

<script>
// Gestion de la marque "Autres"
function toggleOtherBrand(select) {
    const input = document.getElementById('otherBrandInput');
    if (select.value === 'Autres') {
        input.style.display = 'block';
        input.required = true;
    } else {
        input.style.display = 'none';
        input.required = false;
        input.value = '';
    }
}

// Gestion dynamique des contacts
document.addEventListener('DOMContentLoaded', function () {
    const container = document.getElementById('contacts-container');
    const addButton = document.getElementById('add-contact');

    // Ajouter un contact
    addButton.addEventListener('click', function() {
        // Cloner la première ligne
        const firstRow = container.querySelector('.contact-row');
        const newRow = firstRow.cloneNode(true);

        // Vider les valeurs des champs clonés
        newRow.querySelectorAll('input').forEach(input => {
            input.value = '';
        });

        // Afficher le bouton supprimer sur la nouvelle ligne
        const removeBtn = newRow.querySelector('.remove-contact');
        removeBtn.style.display = 'block';

        // Ajouter la nouvelle ligne au conteneur
        container.appendChild(newRow);
    });

    // Supprimer un contact (Délégation d'événement)
    container.addEventListener('click', function(e) {
        if (e.target.closest('.remove-contact')) {
            const rows = container.querySelectorAll('.contact-row');
            // Empêcher de supprimer s'il ne reste qu'une ligne
            if (rows.length > 1) {
                e.target.closest('.contact-row').remove();
            }
        }
    });

    // Initialisation marque "Autres"
    const select = document.getElementById('brandSelect');
    const input = document.getElementById('otherBrandInput');
    if (select.value === 'Autres' || input.value !== '') {
        input.style.display = 'block';
        input.required = true;
    }
});
</script>
@endsection