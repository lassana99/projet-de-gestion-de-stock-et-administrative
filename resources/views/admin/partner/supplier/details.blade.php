@extends('admin.layouts.master')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4 col-lg-11 mx-auto" style="background-color:#6c757d; border: 1px solid white;">

        {{-- Header --}}
        <div class="card-header py-3" style="background-color:#6c757d; border-bottom: 1px solid white; margin-left: -12px; margin-right: -12px;">
            <h5 class="m-0 font-weight-bold text-white">
                Détails du fournisseur : {{ $supplier->company_name }}
            </h5>
        </div>

        <div class="card-body" style="background-color:#6c757d; margin-left: -12px; margin-right: -12px;">

            {{-- ===================== --}}
            {{-- INFORMATIONS ENTREPRISE --}}
            {{-- ===================== --}}
            <div class="card mb-4 shadow-sm" style="border: 1px solid rgba(255,255,255,0.2);">
                <div class="card-header text-white fw-bold h5" style="background-color:#5a6268;">
                    Informations sur l’entreprise
                </div>

                <div class="card-body" style="background-color:#6c757d;">
                    <div class="row">

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-white">Nom de la société</label>
                            <div class="form-control bg-light text-dark">
                                {{ $supplier->company_name }}
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-white">Pays d'origine</label>
                            <div class="form-control bg-light text-dark">
                                {{ $supplier->country_origin ?? '-' }}
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-white">Activité</label>
                            <div class="form-control bg-light text-dark">
                                {{ $supplier->specialty ?? '-' }}
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-white">Marque</label>
                            <div class="form-control bg-light text-dark">
                                {{ $supplier->brand ?? '-' }}
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-white">Site web</label>
                            <div class="form-control bg-light text-dark">
                                @if($supplier->website)
                                    <a href="{{ $supplier->website }}" target="_blank" class="text-primary decoration-none">
                                        <i class="fa-solid fa-globe mr-1"></i> {{ $supplier->website }}
                                    </a>
                                @else
                                    -
                                @endif
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-white">Échéance de paiement</label>
                            <div class="form-control bg-light text-dark">
                                {{ $supplier->payment_deadline ?? '-' }}
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-white">Enregistrement / NIF</label>
                            <div class="form-control bg-light text-dark">
                                {{ $supplier->nif ?? '-' }}
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-white">Date</label>
                            <div class="form-control bg-light text-dark">
                                {{ $supplier->date ? $supplier->date->format('d/m/Y') : '-' }}
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            {{-- ===================== --}}
            {{-- PERSONNES À CONTACTER --}}
            {{-- ===================== --}}
            <div class="card mb-4 shadow-sm" style="border: 1px solid rgba(255,255,255,0.2);">
                <div class="card-header text-white fw-bold h5" style="background-color:#5a6268;">
                    Personnes à contacter ({{ $supplier->contacts->count() }})
                </div>

                <div class="card-body" style="background-color:#6c757d;">
                    @forelse($supplier->contacts as $index => $contact)
                        <div class="row mb-4 {{ !$loop->last ? 'border-bottom border-secondary pb-3' : '' }}">
                            <div class="col-md-1 d-none d-md-flex align-items-center justify-content-center">
                                <span class="badge bg-dark text-white rounded-circle" style="width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;">
                                    {{ $index + 1 }}
                                </span>
                            </div>
                            
                            <div class="col-md-3 mb-2">
                                <label class="form-label fw-bold text-white ">Nom du contact</label>
                                <div class="form-control bg-light text-dark">
                                    {{ $contact->name ?? '-' }}
                                </div>
                            </div>

                            {{-- Nouveau champ Poste affiché ici --}}
                            <div class="col-md-2 mb-2">
                                <label class="form-label fw-bold text-white ">Poste / Fonction</label>
                                <div class="form-control bg-light text-dark">
                                    {{ $contact->position ?? '-' }}
                                </div>
                            </div>

                            <div class="col-md-3 mb-2">
                                <label class="form-label fw-bold text-white ">Téléphone</label>
                                <div class="form-control bg-light text-dark">
                                    {{ $contact->phone ?? '-' }}
                                </div>
                            </div>

                            <div class="col-md-3 mb-2">
                                <label class="form-label fw-bold text-white ">Email</label>
                                <div class="form-control bg-light text-dark">
                                    {{ $contact->email ?? '-' }}
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-3">
                            <p class="text-white mb-0">Aucun contact enregistré pour ce fournisseur.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- ACTIONS (BOUTONS) --}}
            <div class="mt-4 d-flex justify-content-between">
                {{-- Bouton Modifier à gauche --}}
                <a href="{{ route('supplierEdit', $supplier->id) }}"
                   class="btn text-white shadow-sm"
                   style="background-color: rgb(249, 138, 27); border: none; padding: 10px 25px;">
                    <i class="fa-solid fa-pen-to-square mr-1"></i> Modifier
                </a>

                {{-- Bouton Retour à droite --}}
                <a href="{{ route('supplierList') }}"
                   class="btn text-white shadow-sm"
                   style="background-color: rgb(144, 142, 140); border: none; padding: 10px 25px;">
                    <i class="fa-solid fa-arrow-left mr-1"></i> Retour à la liste
                </a>
            </div>

        </div>
    </div>
</div>
@endsection