@extends('admin.layouts.master')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4 col-lg-11 mx-auto">

        {{-- Header --}}
        <div class="card-header py-3" style="background-color:#6c757d; margin-left: -12px; margin-right: -12px;">
            <h5 class="m-0 font-weight-bold text-white">
                Modifier le fournisseur
            </h5>
        </div>

        <form action="{{ route('supplierUpdate') }}" method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" name="id" value="{{ $supplier->id }}">

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
                                       value="{{ old('company_name', $supplier->company_name) }}">
                                @error('company_name') <small class="invalid-feedback">{{ $message }}</small> @enderror
                            </div>

                            {{-- Pays --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-white">Pays d'origine</label>
                                <select name="country_origin" class="form-control">
                                    <option value="">Sélectionnez un pays</option>
                                    @foreach($countries as $country)
                                        <option value="{{ $country }}"
                                            {{ old('country_origin', $supplier->country_origin) == $country ? 'selected' : '' }}>
                                            {{ $country }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Activité --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-white">Activité</label>
                                <input type="text" name="specialty" class="form-control"
                                       value="{{ old('specialty', $supplier->specialty) }}">
                            </div>

                            {{-- Marque --}}
                            @php
                                $brands = ["CAT", "Komatsu", "Hitachi", "XCMG", "Sany", "Lovol", "Volvo", "Autres"];
                                $currentBrand = old('brand', $supplier->brand);
                                $isOther = !in_array($currentBrand, $brands) && $currentBrand;
                            @endphp

                            <div class="col-md-6 mb-3">
                                <label class="form-label text-white">Marque <span class="text-danger">*</span></label>
                                <select name="brand" id="brandSelect" class="form-control" onchange="toggleOtherBrand(this)">
                                    <option value="">Sélectionnez la marque</option>
                                    @foreach($brands as $brand)
                                        <option value="{{ $brand }}" {{ ($currentBrand == $brand || ($brand == 'Autres' && $isOther)) ? 'selected' : '' }}>
                                            {{ $brand }}
                                        </option>
                                    @endforeach
                                </select>
                                <input type="text" name="other_brand" id="otherBrandInput" class="form-control mt-2"
                                       style="display:none" value="{{ $isOther ? $currentBrand : old('other_brand') }}">
                            </div>

                            {{-- Site Web --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-white">Site web</label>
                                <input type="text" name="website" class="form-control"
                                       value="{{ old('website', $supplier->website) }}">
                            </div>

                            {{-- Échéance Paiement --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-white">Échéance de paiement</label>
                                <select name="payment_deadline" class="form-control">
                                    <option value="">Sélectionner une échéance</option>
                                    @foreach(['Immédiat/Paiement avant commande', '15 jours', '30 jours fin de mois', '60 jours fin de mois', 'Paiement à la réception'] as $deadline)
                                        <option value="{{ $deadline }}" {{ old('payment_deadline', $supplier->payment_deadline) == $deadline ? 'selected' : '' }}>{{ $deadline }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- NIF --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-white">Enregistrement / NIF</label>
                                <input type="text" name="nif" class="form-control"
                                       value="{{ old('nif', $supplier->nif) }}">
                            </div>

                            {{-- DATE --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-white">Date</label>
                                <input type="date" name="date"
                                       class="form-control @error('date') is-invalid @enderror"
                                       value="{{ old('date', ($supplier->date ? $supplier->date->format('Y-m-d') : '')) }}">
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
                        {{-- On boucle sur les contacts existants --}}
                        @forelse($supplier->contacts as $index => $contact)
                        <div class="row contact-row mb-3 pb-3 border-bottom border-secondary">
                            <div class="col-md-3">
                                <label class="form-label text-white">Nom du contact</label>
                                <input type="text" name="contact_names[]" class="form-control" 
                                       value="{{ $contact->name }}" placeholder="Nom complet">
                            </div>

                            {{-- Nouveau champ Poste --}}
                            <div class="col-md-2">
                                <label class="form-label text-white">Poste / Fonction</label>
                                <input type="text" name="contact_positions[]" class="form-control" 
                                       value="{{ $contact->position }}" placeholder="Ex: Commercial">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label text-white">Téléphone</label>
                                <input type="text" name="contact_phones[]" class="form-control" 
                                       value="{{ $contact->phone }}" placeholder="N° Téléphone">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label text-white">Email</label>
                                <input type="email" name="contact_emails[]" class="form-control" 
                                       value="{{ $contact->email }}" placeholder="Adresse email">
                            </div>

                            <div class="col-md-1 d-flex align-items-end">
                                <button type="button" class="btn btn-danger remove-contact" 
                                        style="{{ $supplier->contacts->count() <= 1 ? 'display: none;' : '' }}">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </div>
                        </div>
                        @empty
                        <div class="row contact-row mb-3 pb-3 border-bottom border-secondary">
                            <div class="col-md-3">
                                <label class="form-label text-white">Nom du contact</label>
                                <input type="text" name="contact_names[]" class="form-control" placeholder="Nom complet">
                            </div>
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
                                <button type="button" class="btn btn-danger remove-contact" style="display: none;">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </div>
                        </div>
                        @endforelse
                    </div>
                </div>

                {{-- ACTIONS --}}
                <div class="row">
                    <div class="col-md-6">
                        <button type="submit" class="btn w-100 text-white shadow-sm"
                                style="background-color: rgb(249, 138, 27); border: none;">
                            Mettre à jour le fournisseur
                        </button>
                    </div>
                    <div class="col-md-6">
                        <a href="{{ route('supplierList') }}" class="btn w-100 text-white shadow-sm"
                           style="background-color:rgb(144,142,140);">
                            Annuler / Retour
                        </a>
                    </div>
                </div>

            </div>
        </form>
    </div>
</div>

<script>
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

document.addEventListener('DOMContentLoaded', function () {
    const container = document.getElementById('contacts-container');
    const addButton = document.getElementById('add-contact');

    addButton.addEventListener('click', function() {
        const rows = container.querySelectorAll('.contact-row');
        const firstRow = rows[0];
        const newRow = firstRow.cloneNode(true);

        newRow.querySelectorAll('input').forEach(input => input.value = '');

        const removeBtn = newRow.querySelector('.remove-contact');
        removeBtn.style.display = 'block';

        container.appendChild(newRow);
    });

    container.addEventListener('click', function(e) {
        if (e.target.closest('.remove-contact')) {
            const rows = container.querySelectorAll('.contact-row');
            if (rows.length > 1) {
                e.target.closest('.contact-row').remove();
            }
            
            const remainingRows = container.querySelectorAll('.contact-row');
            if (remainingRows.length === 1) {
                remainingRows[0].querySelector('.remove-contact').style.display = 'none';
            }
        }
    });

    toggleOtherBrand(document.getElementById('brandSelect'));
});
</script>
@endsection