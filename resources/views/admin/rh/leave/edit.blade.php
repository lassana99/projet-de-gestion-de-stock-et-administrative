@extends('admin.layouts.master')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        {{-- En-tête --}}
        <div class="card-header py-3" style="background-color:#6c757d;">
            <h5 class="m-0 font-weight-bold text-white">Modifier la demande de congé #{{ $leave->id }}</h5>
        </div>

        {{-- Formulaire --}}
        <form action="{{ route('leaveUpdate', $leave->id) }}" method="POST">
            @csrf
            @method('PUT')

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

                <div class="row text-white">
                    {{-- Colonne Gauche --}}
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="fw-bold">Employé <span class="text-danger">*</span></label>
                            <select name="employee_id" class="form-control @error('employee_id') is-invalid @enderror" required>
                                <option value="">-- Sélectionner l'employé --</option>
                                @foreach($employees as $emp)
                                    <option value="{{ $emp->id }}" {{ old('employee_id', $leave->employee_id) == $emp->id ? 'selected' : '' }}>
                                        {{ $emp->full_name }} ({{ $emp->matricule }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="fw-bold">Type de congé <span class="text-danger">*</span></label>
                            <select name="leave_type" class="form-control @error('leave_type') is-invalid @enderror" required>
                                @php
                                    $types = ['Annuel', 'Maladie', 'Maternité/Paternité', 'Exceptionnel'];
                                @endphp
                                @foreach($types as $type)
                                    <option value="{{ $type }}" {{ old('leave_type', $leave->leave_type) == $type ? 'selected' : '' }}>
                                        {{ $type }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Le champ Statut est masqué pour l'admin --}}
                        @if(Auth::user()->role !== 'admin')
                            <div class="mb-3">
                                <label class="fw-bold">Statut de la demande <span class="text-danger">*</span></label>
                                <select name="status" class="form-control @error('status') is-invalid @enderror" required>
                                    <option value="En attente" {{ old('status', $leave->status) == 'En attente' ? 'selected' : '' }}>En attente</option>
                                    <option value="Approuvé" {{ old('status', $leave->status) == 'Approuvé' ? 'selected' : '' }}>Approuvé</option>
                                    <option value="Refusé" {{ old('status', $leave->status) == 'Refusé' ? 'selected' : '' }}>Refusé</option>
                                </select>
                            </div>
                        @else
                            {{-- Champ caché pour conserver la valeur actuelle si l'admin modifie le reste --}}
                            <input type="hidden" name="status" value="{{ $leave->status }}">
                        @endif
                    </div>

                    {{-- Colonne Droite --}}
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-6">
                                <div class="mb-3">
                                    <label class="fw-bold">Date début <span class="text-danger">*</span></label>
                                    <input type="date" name="start_date" class="form-control @error('start_date') is-invalid @enderror" 
                                           value="{{ old('start_date', $leave->start_date->format('Y-m-d')) }}" required>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mb-3">
                                    <label class="fw-bold">Date fin <span class="text-danger">*</span></label>
                                    <input type="date" name="end_date" class="form-control @error('end_date') is-invalid @enderror" 
                                           value="{{ old('end_date', $leave->end_date->format('Y-m-d')) }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="fw-bold">Motif / Justification</label>
                            <textarea name="reason" class="form-control @error('reason') is-invalid @enderror" rows="4" 
                                      placeholder="Expliquez la raison du congé...">{{ old('reason', $leave->reason) }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- Boutons d'action --}}
                <div class="row mt-4">
                    <div class="col-6">
                        <button type="submit" class="btn w-100 text-white shadow-sm" style="background-color: rgb(249, 138, 27); border: none; font-weight: bold;">
                            <i class="fa-solid fa-rotate me-1"></i> Mettre à jour la demande
                        </button>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('leaveList') }}" class="btn w-100 text-white shadow-sm" style="background-color: #908e8c; border: none; font-weight: bold;">
                            Annuler
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection