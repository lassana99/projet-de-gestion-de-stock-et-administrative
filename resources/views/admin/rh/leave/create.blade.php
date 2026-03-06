@extends('admin.layouts.master')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        {{-- En-tête --}}
        <div class="card-header py-3" style="background-color:#6c757d;">
            <h5 class="m-0 font-weight-bold text-white">Nouvelle Demande de Congé</h5>
        </div>

        {{-- Formulaire --}}
        <form action="{{ route('leaveStore') }}" method="POST">
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
                    {{-- Colonne 1 : Informations de l'employé et Type --}}
                    <div class="col-md-6">
                        <div class="mb-3 text-white">
                            <label class="fw-bold">Employé <span class="text-danger">*</span></label>
                            <select name="employee_id" class="form-control" required>
                                <option value="">-- Sélectionner l'employé --</option>
                                @foreach($employees as $emp) 
                                    <option value="{{ $emp->id }}" {{ old('employee_id') == $emp->id ? 'selected' : '' }}>
                                        {{ $emp->full_name }} ({{ $emp->matricule }})
                                    </option> 
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3 text-white">
                            <label class="fw-bold">Type de congé <span class="text-danger">*</span></label>
                            <select name="leave_type" class="form-control" required>
                                <option value="Annuel" {{ old('leave_type') == 'Annuel' ? 'selected' : '' }}>Annuel</option>
                                <option value="Maladie" {{ old('leave_type') == 'Maladie' ? 'selected' : '' }}>Maladie</option>
                                <option value="Maternité/Paternité" {{ old('leave_type') == 'Maternité/Paternité' ? 'selected' : '' }}>Maternité/Paternité</option>
                                <option value="Exceptionnel" {{ old('leave_type') == 'Exceptionnel' ? 'selected' : '' }}>Exceptionnel</option>
                            </select>
                        </div>

                        <div class="mb-3 text-white">
                            <label class="fw-bold">Motif</label>
                            <textarea name="reason" class="form-control" rows="3" placeholder="Précisez le motif ici...">{{ old('reason') }}</textarea>
                        </div>
                    </div>

                    {{-- Colonne 2 : Dates --}}
                    <div class="col-md-6">
                        <div class="mb-3 text-white">
                            <label class="fw-bold">Date de début <span class="text-danger">*</span></label>
                            <input type="date" name="start_date" class="form-control" value="{{ old('start_date') }}" required>
                        </div>

                        <div class="mb-3 text-white">
                            <label class="fw-bold">Date de fin <span class="text-danger">*</span></label>
                            <input type="date" name="end_date" class="form-control" value="{{ old('end_date') }}" required>
                        </div>
                    </div>
                </div>

                {{-- Boutons d'action --}}
                <div class="mt-4 row">
                    <div class="col-6">
                        <button type="submit" class="btn w-100 text-white shadow-sm" style="background-color:rgb(249, 138, 27); border:none; font-weight:bold;">
                            <i class="fa-solid fa-paper-plane me-1"></i> Soumettre
                        </button>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('leaveList') }}" class="btn w-100 btn-secondary shadow-sm" style="font-weight:bold; background-color: rgb(144, 142, 140); border:none;">
                            Annuler
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection