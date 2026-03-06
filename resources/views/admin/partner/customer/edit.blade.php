@extends('admin.layouts.master')

@section('content')
    <div class="container-fluid">
        <div class="card shadow mb-4 col-lg-11 mx-auto">
            {{-- Header --}}
            <div class="card-header py-3" style="background-color:#6c757d; margin-left: -12px; margin-right: -12px;">
                <h5 class="m-0 font-weight-bold text-white text-center">Modifier le Client : {{ $customer->name }}</h5>
            </div>

            <div class="card-body" style="background-color:#6c757d; margin-left: -12px; margin-right: -12px;">
                <form action="{{ route('customerUpdate') }}" method="post">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="id" value="{{ $customer->id }}">

                    {{-- ===================== --}}
                    {{-- INFORMATIONS CLIENT --}}
                    {{-- ===================== --}}
                    <div class="card mb-4 shadow-sm">
                        <div class="card-header bg-dark text-white">
                            Informations sur l’entreprise / Client
                        </div>
                        <div class="card-body" style="background-color:#6c757d;">
                            <div class="row">
                                {{-- Nom du client --}}
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-white">Nom du client <span class="text-danger">*</span></label>
                                    <input type="text" name="name"
                                           class="form-control @error('name') is-invalid @enderror"
                                           value="{{ old('name', $customer->name) }}">
                                    @error('name') <small class="invalid-feedback">{{ $message }}</small> @enderror
                                </div>

                                {{-- Adresse --}}
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-white">Adresse <span class="text-danger">*</span></label>
                                    <input type="text" name="address"
                                           class="form-control @error('address') is-invalid @enderror"
                                           value="{{ old('address', $customer->address) }}">
                                    @error('address') <small class="invalid-feedback">{{ $message }}</small> @enderror
                                </div>

                                {{-- Domaine d'activité --}}
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-white">Domaine d'activité <span class="text-danger">*</span></label>
                                    <input type="text" name="domain"
                                           class="form-control @error('domain') is-invalid @enderror"
                                           value="{{ old('domain', $customer->domain) }}">
                                    @error('domain') <small class="invalid-feedback">{{ $message }}</small> @enderror
                                </div>

                                {{-- Email --}}
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-white">Email entreprise</label>
                                    <input type="email" name="email"
                                           class="form-control @error('email') is-invalid @enderror"
                                           value="{{ old('email', $customer->email) }}">
                                    @error('email') <small class="invalid-feedback">{{ $message }}</small> @enderror
                                </div>

                                {{-- RCCM --}}
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-white">RCCM</label>
                                    <input type="text" name="rccm"
                                           class="form-control @error('rccm') is-invalid @enderror"
                                           value="{{ old('rccm', $customer->rccm) }}">
                                    @error('rccm') <small class="invalid-feedback">{{ $message }}</small> @enderror
                                </div>

                                {{-- NIF --}}
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-white">Enregistrement / NIF</label>
                                    <input type="text" name="nif"
                                           class="form-control @error('nif') is-invalid @enderror"
                                           value="{{ old('nif', $customer->nif) }}">
                                    @error('nif') <small class="invalid-feedback">{{ $message }}</small> @enderror
                                </div>

                                {{-- Date --}}
                                <div class="col-md-4 mb-3">
                                    <label class="form-label text-white">Date <span class="text-danger">*</span></label>
                                    <input type="date" name="date"
                                           class="form-control @error('date') is-invalid @enderror"
                                           value="{{ old('date', $customer->date ? $customer->date->format('Y-m-d') : '') }}">
                                    @error('date') <small class="invalid-feedback">{{ $message }}</small> @enderror
                                </div>

                                {{-- Échéance Paiement --}}
                                <div class="col-md-4 mb-3">
                                    <label class="form-label text-white">Échéance de paiement</label>
                                    @php $deadlines = ['Immédiat/Paiement avant commande', '15 jours', '30 jours fin de mois', '60 jours fin de mois', 'Paiement à la réception']; @endphp
                                    <select name="payment_deadline" class="form-control">
                                        <option value="">Sélectionner une échéance</option>
                                        @foreach($deadlines as $dl)
                                            <option value="{{ $dl }}" {{ old('payment_deadline', $customer->payment_deadline) == $dl ? 'selected' : '' }}>{{ $dl }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Mode de paiement --}}
                                <div class="col-md-4 mb-3">
                                    <label class="form-label text-white">Mode de paiement</label>
                                    @php $methods = ['Cash', 'Orange' => 'Orange Money', 'WPay' => 'Wave', 'Sama' => 'Sama Money', 'Finance' => 'Financement', 'Virement' => 'Virement bancaire']; @endphp
                                    <select name="payment_method" class="form-control">
                                        <option value="">Choisissez le mode...</option>
                                        @foreach($methods as $value => $label)
                                            <option value="{{ is_int($value) ? $label : $value }}" {{ old('payment_method', $customer->payment_method) == (is_int($value) ? $label : $value) ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
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
                            @forelse($customer->contacts as $index => $contact)
                            <div class="row contact-row mb-3 pb-3 border-bottom border-secondary">
                                <div class="col-md-3">
                                    <label class="form-label text-white">Nom du contact</label>
                                    <input type="text" name="contact_names[]" class="form-control" 
                                           value="{{ $contact->name }}" placeholder="Nom complet">
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label text-white">Poste / Fonction</label>
                                    <input type="text" name="contact_positions[]" class="form-control" 
                                           value="{{ $contact->position }}" placeholder="Ex: Gérant">
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
                                            style="{{ $customer->contacts->count() <= 1 ? 'display: none;' : '' }}">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                            @empty
                            {{-- Ligne vide si aucun contact n'existe --}}
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
                            <button type="submit" class="btn w-100 text-white mt-2"
                                    style="background-color: rgb(249, 138, 27); border: none;">
                                Mettre à jour
                            </button>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('customerList') }}"
                               class="btn w-100 text-white mt-2 text-center"
                               style="background-color: rgb(144, 142, 140); border: none;">
                                Retour
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const container = document.getElementById('contacts-container');
        const addButton = document.getElementById('add-contact');

        // Ajouter un contact
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

        // Supprimer un contact (Délégation d'événement)
        container.addEventListener('click', function(e) {
            if (e.target.closest('.remove-contact')) {
                const rows = container.querySelectorAll('.contact-row');
                if (rows.length > 1) {
                    e.target.closest('.contact-row').remove();
                }
                
                // Cacher le bouton supprimer s'il ne reste qu'une ligne
                const remainingRows = container.querySelectorAll('.contact-row');
                if (remainingRows.length === 1) {
                    remainingRows[0].querySelector('.remove-contact').style.display = 'none';
                }
            }
        });
    });
    </script>
@endsection