@extends('admin.layouts.master')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4 col-lg-11 mx-auto">

        {{-- Header --}}
        <div class="card-header py-3" style="background-color:#6c757d; margin-left: -12px; margin-right: -12px;">
            <h5 class="m-0 font-weight-bold text-white">
                Ajouter un nouveau prospect
            </h5>
        </div>

        <form action="{{ route('prospectCreate') }}" method="post">
            @csrf

            <div class="card-body" style="background-color:#6c757d; margin-left: -12px; margin-right: -12px;">

                {{-- ===================== --}}
                {{-- INFORMATIONS PROSPECT --}}
                {{-- ===================== --}}
                <div class="card mb-4 shadow-sm">
                    <div class="card-header bg-dark text-white">
                        Informations sur l’entreprise / Prospect
                    </div>

                    <div class="card-body" style="background-color:#6c757d;">
                        <div class="row">
                            {{-- Nom du prospect --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-white">Nom du prospect <span class="text-danger">*</span></label>
                                <input type="text" name="name"
                                       class="form-control @error('name') is-invalid @enderror"
                                       placeholder="Nom du prospect..." value="{{ old('name') }}">
                                @error('name') <small class="invalid-feedback">{{ $message }}</small> @enderror
                            </div>

                            {{-- Adresse --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-white">Adresse <span class="text-danger">*</span></label>
                                <input type="text" name="address"
                                       class="form-control @error('address') is-invalid @enderror"
                                       placeholder="Adresse..." value="{{ old('address') }}">
                                @error('address') <small class="invalid-feedback">{{ $message }}</small> @enderror
                            </div>

                            {{-- Domaine d'activité --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-white">Domaine d'activité <span class="text-danger">*</span></label>
                                <input type="text" name="domain"
                                       class="form-control @error('domain') is-invalid @enderror"
                                       placeholder="Domaine d'activité..." value="{{ old('domain') }}">
                                @error('domain') <small class="invalid-feedback">{{ $message }}</small> @enderror
                            </div>

                            {{-- Email Entreprise --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-white">Email entreprise</label>
                                <input type="email" name="email"
                                       class="form-control @error('email') is-invalid @enderror"
                                       placeholder="Email..." value="{{ old('email') }}">
                                @error('email') <small class="invalid-feedback">{{ $message }}</small> @enderror
                            </div>

                            {{-- Date --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-white">Date <span class="text-danger">*</span></label>
                                <input type="date" name="date"
                                       class="form-control @error('date') is-invalid @enderror"
                                       value="{{ old('date', $date ?? date('Y-m-d')) }}">
                                @error('date') <small class="invalid-feedback">{{ $message }}</small> @enderror
                            </div>

                            {{-- Site web --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-white">Site web</label>
                                <input type="text" name="website"
                                       class="form-control @error('website') is-invalid @enderror"
                                       placeholder="Site web..." value="{{ old('website') }}">
                                @error('website') <small class="invalid-feedback">{{ $message }}</small> @enderror
                            </div>

                            {{-- Opportunité --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-white">Opportunité / Besoin</label>
                                <input type="text" name="need"
                                       class="form-control @error('need') is-invalid @enderror"
                                       placeholder="Opportunité..." value="{{ old('need') }}">
                                @error('need') <small class="invalid-feedback">{{ $message }}</small> @enderror
                            </div>

                            {{-- Ancien champ contact unique (caché ou optionnel) --}}
                            <input type="hidden" name="contact" value="Multi-contacts">

                            {{-- Commentaire --}}
                            <div class="col-md-12 mb-3">
                                <label class="form-label text-white">Commentaire / Action à prendre</label>
                                <textarea name="comment" rows="2"
                                          class="form-control @error('comment') is-invalid @enderror"
                                          placeholder="Commentaire...">{{ old('comment') }}</textarea>
                                @error('comment') <small class="invalid-feedback">{{ $message }}</small> @enderror
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
                    </div>
                </div>

                <!-- Statut d'achat caché (valeur par défaut NON) -->
                <input type="hidden" name="statut_achat" value="NON">

                {{-- ACTIONS --}}
                <div class="row">
                    <div class="col-md-6">
                        <button type="submit" class="btn w-100 text-white shadow-sm"
                                style="background-color: rgb(249, 138, 27); border: none;">
                            Ajouter le prospect
                        </button>
                    </div>
                    <div class="col-md-6">
                        <a href="{{ route('prospectList') }}" class="btn w-100 text-white shadow-sm"
                           style="background-color:rgb(144, 142, 140); border: none;">
                            Retour
                        </a>
                    </div>
                </div>

            </div>
        </form>
    </div>
</div>

<script>
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
});
</script>
@endsection