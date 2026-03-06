@extends('admin.layouts.master')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        {{-- En-tête --}}
        <div class="card-header py-3" style="background-color:#6c757d;">
            <h5 class="m-0 font-weight-bold text-white">Nouveau Paiement INPS</h5>
        </div>

        {{-- Formulaire --}}
        <form action="{{ route('inpsStore') }}" method="POST">
            @csrf
            <div class="card-body" style="background-color:#6c757d;">
                
                {{-- Affichage des erreurs de validation --}}
                @if ($errors->any())
                    <div class="alert alert-danger" style="margin-bottom: 20px;">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="row">
                    {{-- Colonne 1 : Personnes et Période --}}
                    <div class="col-md-6">
                        <div class="mb-3 text-white">
                            <label class="fw-bold">Personnes concernées <span class="text-danger">*</span></label>
                            <select name="employee_ids[]" class="form-control select2" multiple required>
                                @foreach($employees as $emp)
                                    <option value="{{ $emp->id }}" {{ (is_array(old('employee_ids')) && in_array($emp->id, old('employee_ids'))) ? 'selected' : '' }}>
                                        {{ $emp->full_name }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-white-50">Maintenez Ctrl pour sélectionner plusieurs personnes</small>
                        </div>

                        <div class="row">
                            <div class="col-6">
                                <div class="mb-3 text-white">
                                    <label class="fw-bold">Date Début <span class="text-danger">*</span></label>
                                    <input type="date" name="start_date" class="form-control" value="{{ old('start_date') }}" required>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mb-3 text-white">
                                    <label class="fw-bold">Date Fin <span class="text-danger">*</span></label>
                                    <input type="date" name="end_date" class="form-control" value="{{ old('end_date') }}" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Colonne 2 : Paiement et Modalités --}}
                    <div class="col-md-6">
                        <div class="mb-3 text-white">
                            <label class="fw-bold">Montant (FCFA) <span class="text-danger">*</span></label>
                            <input type="number" name="amount_fcfa" class="form-control" value="{{ old('amount_fcfa') }}" required>
                        </div>

                        <div class="mb-3 text-white">
                            <label class="fw-bold">Date de Paiement <span class="text-danger">*</span></label>
                            <input type="date" name="payment_date" class="form-control" value="{{ old('payment_date') }}" required>
                        </div>

                        <div class="mb-3 text-white">
                            <label class="fw-bold">Modalité <span class="text-danger">*</span></label>
                            <select name="payment_mode" class="form-control" required>
                                <option value="Espèces" {{ old('payment_mode') == 'Espèces' ? 'selected' : '' }}>Espèces</option>
                                <option value="Virement bancaire" {{ old('payment_mode') == 'Virement bancaire' ? 'selected' : '' }}>Virement bancaire</option>
                                <option value="Chèque" {{ old('payment_mode') == 'Chèque' ? 'selected' : '' }}>Chèque</option>
                                <option value="Mobile money" {{ old('payment_mode') == 'Mobile money' ? 'selected' : '' }}>Mobile money</option>
                            </select>
                        </div>
                    </div>

                    {{-- Bas de page : Détails --}}
                    <div class="col-12 mt-2">
                        <div class="mb-3 text-white">
                            <label class="fw-bold">Détails complémentaires</label>
                            <textarea name="additional_details" class="form-control" rows="3" placeholder="Informations supplémentaires...">{{ old('additional_details') }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- Boutons d'action --}}
                <div class="mt-4 row">
                    <div class="col-6">
                        <button type="submit" class="btn w-100 text-white shadow-sm" style="background-color:rgb(249, 138, 27); border:none; font-weight:bold;">
                            <i class="fa-solid fa-plus me-1"></i> Ajouter
                        </button>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('inpsList') }}" class="btn w-100 btn-secondary shadow-sm" style="font-weight:bold; background-color: rgb(144, 142, 140); border:none;">
                            Annuler
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection