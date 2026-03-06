@extends('admin.layouts.master')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4 col-lg-11 mx-auto">
        {{-- Header --}}
        <div class="card-header py-3" style="background-color:#6c757d; margin-left: -12px; margin-right: -12px;">
            <h5 class="m-0 font-weight-bold text-white text-center">Modifier le Prospect : {{ $prospect->name }}</h5>
        </div>

        <div class="card-body" style="background-color:#6c757d; margin-left: -12px; margin-right: -12px;">
            <form action="{{ route('prospectUpdate') }}" method="post" id="prospectForm">
                @csrf
                @method('PUT')

                <input type="hidden" name="id" value="{{ $prospect->id }}">

                {{-- ===================== --}}
                {{-- INFORMATIONS PROSPECT --}}
                {{-- ===================== --}}
                <div class="card mb-4 shadow-sm">
                    <div class="card-header bg-dark text-white">
                        Informations sur l’entreprise / Prospect
                    </div>
                    <div class="card-body" style="background-color:#6c757d;">
                        <div class="row">
                            {{-- Nom --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-white">Nom du prospect <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                       value="{{ old('name', $prospect->name) }}">
                                @error('name') <small class="invalid-feedback">{{ $message }}</small> @enderror
                            </div>

                            {{-- Adresse --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-white">Adresse <span class="text-danger">*</span></label>
                                <input type="text" name="address" class="form-control @error('address') is-invalid @enderror" 
                                       value="{{ old('address', $prospect->address) }}">
                                @error('address') <small class="invalid-feedback">{{ $message }}</small> @enderror
                            </div>

                            {{-- Domaine --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-white">Domaine d'activité <span class="text-danger">*</span></label>
                                <input type="text" name="domain" class="form-control @error('domain') is-invalid @enderror" 
                                       value="{{ old('domain', $prospect->domain) }}">
                                @error('domain') <small class="invalid-feedback">{{ $message }}</small> @enderror
                            </div>

                            {{-- Email --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-white">Email entreprise</label>
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                                       value="{{ old('email', $prospect->email) }}">
                                @error('email') <small class="invalid-feedback">{{ $message }}</small> @enderror
                            </div>

                            {{-- Date --}}
                            <div class="col-md-4 mb-3">
                                <label class="form-label text-white">Date <span class="text-danger">*</span></label>
                                <input type="date" name="date" class="form-control @error('date') is-invalid @enderror" 
                                       value="{{ old('date', $prospect->date ? $prospect->date->format('Y-m-d') : '') }}">
                                @error('date') <small class="invalid-feedback">{{ $message }}</small> @enderror
                            </div>

                            {{-- Site Web --}}
                            <div class="col-md-4 mb-3">
                                <label class="form-label text-white">Site web</label>
                                <input type="text" name="website" class="form-control" value="{{ old('website', $prospect->website) }}">
                            </div>

                            {{-- Opportunité --}}
                            <div class="col-md-4 mb-3">
                                <label class="form-label text-white">Opportunité / Besoin</label>
                                <input type="text" name="need" class="form-control" value="{{ old('need', $prospect->need) }}">
                            </div>

                            {{-- Statut d'achat --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-white">Statut d'achat</label>
                                <select name="statut_achat" class="form-control @error('statut_achat') is-invalid @enderror" id="statutAchatSelect">
                                    <option value="NON" {{ old('statut_achat', $prospect->statut_achat) === 'NON' ? 'selected' : '' }}>NON (Reste Prospect)</option>
                                    <option value="OUI" {{ old('statut_achat', $prospect->statut_achat) === 'OUI' ? 'selected' : '' }}>OUI (Convertir en Client)</option>
                                </select>
                                @error('statut_achat') <small class="invalid-feedback">{{ $message }}</small> @enderror
                            </div>

                            {{-- Commentaire --}}
                            <div class="col-md-12 mb-3">
                                <label class="form-label text-white">Commentaire / Action à prendre</label>
                                <textarea name="comment" class="form-control" rows="2">{{ old('comment', $prospect->comment) }}</textarea>
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
                        @forelse($prospect->contacts as $index => $contact)
                        <div class="row contact-row mb-3 pb-3 border-bottom border-secondary">
                            <div class="col-md-3">
                                <label class="form-label text-white">Nom du contact</label>
                                <input type="text" name="contact_names[]" class="form-control" value="{{ $contact->name }}" placeholder="Nom complet">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label text-white">Poste / Fonction</label>
                                <input type="text" name="contact_positions[]" class="form-control" value="{{ $contact->position }}" placeholder="Ex: Gérant">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label text-white">Téléphone</label>
                                <input type="text" name="contact_phones[]" class="form-control" value="{{ $contact->phone }}" placeholder="N° Téléphone">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label text-white">Email</label>
                                <input type="email" name="contact_emails[]" class="form-control" value="{{ $contact->email }}" placeholder="Adresse email">
                            </div>
                            <div class="col-md-1 d-flex align-items-end">
                                <button type="button" class="btn btn-danger remove-contact" style="{{ $prospect->contacts->count() <= 1 ? 'display: none;' : '' }}">
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
                                <input type="text" name="contact_positions[]" class="form-control" placeholder="Ex: Gérant">
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
                    <div class="col-6">
                        <button type="submit" class="btn w-100 text-white mt-2" style="background-color: rgb(249, 138, 27); border: none;">
                            Mettre à jour
                        </button>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('prospectList') }}" class="btn w-100 text-white mt-2 text-center" style="background-color: rgb(144, 142, 140); border: none;">
                            Retour
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Scripts --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // 1. GESTION DYNAMIQUE DES CONTACTS
    const container = document.getElementById('contacts-container');
    const addButton = document.getElementById('add-contact');

    addButton.addEventListener('click', function() {
        const rows = container.querySelectorAll('.contact-row');
        const firstRow = rows[0];
        const newRow = firstRow.cloneNode(true);
        
        // Vider les valeurs des champs clonés
        newRow.querySelectorAll('input').forEach(input => input.value = '');
        
        // Afficher le bouton supprimer sur la nouvelle ligne
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
            
            // Masquer le bouton supprimer s'il ne reste qu'une ligne
            const remainingRows = container.querySelectorAll('.contact-row');
            if (remainingRows.length === 1) {
                remainingRows[0].querySelector('.remove-contact').style.display = 'none';
            }
        }
    });

    // 2. ALERTE DE CONVERSION IMMÉDIATE (Statut OUI)
    const statutSelect = document.getElementById('statutAchatSelect');
    const prospectForm = document.getElementById('prospectForm');

    statutSelect.addEventListener('change', function() {
        if (this.value === 'OUI') {
            Swal.fire({
                title: 'Convertir en client ?',
                text: "En confirmant, ce prospect sera supprimé et vous serez immédiatement redirigé vers le formulaire de création client avec toutes les données pré-remplies.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#f98a1b',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Oui, convertir maintenant',
                cancelButtonText: 'Annuler'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Soumission automatique du formulaire pour déclencher la redirection du contrôleur
                    prospectForm.submit();
                } else {
                    // Réinitialisation si annulation
                    this.value = 'NON';
                }
            });
        }
    });
});
</script>
@endsection