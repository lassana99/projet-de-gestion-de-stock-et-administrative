@extends('admin.layouts.master')

@section('content')
<div class="container-fluid">
    {{-- On centre la fiche pour une meilleure présentation --}}
    <div class="card shadow mb-4 col-lg-10 mx-auto">
        
        {{-- En-tête de la fiche --}}
        <div class="card-header py-3" style="background-color:#6c757d; margin: 0 -12px; border-bottom: 1px solid white;">
            <h5 class="m-0 font-weight-bold text-white text-center">
                <i class="fa-solid fa-calendar-check me-2"></i> Fiche Détails du Congé
            </h5>
        </div>

        <div class="card-body" style="background-color:#6c757d; margin: 0 -12px;">
            <div class="row">
                
                {{-- Informations sur l'employé (Colonne Gauche) --}}
                <div class="col-md-6 mb-4">
                    <label class="fw-bold text-white"><i class="fa-solid fa-user me-1"></i> Employé concerné</label>
                    <div class="bg-light text-dark p-2 rounded fw-bold">
                        {{ $leave->employee->full_name }} <small class="text-muted">({{ $leave->employee->matricule }})</small>
                    </div>
                </div>

                {{-- Type de congé (Colonne Droite) --}}
                <div class="col-md-6 mb-4">
                    <label class="fw-bold text-white"><i class="fa-solid fa-layer-group me-1"></i> Type de Congé</label>
                    <div class="bg-light text-dark p-2 rounded">
                        {{ $leave->leave_type }}
                    </div>
                </div>

                {{-- Période (Dates début/fin et durée) --}}
                <div class="col-md-4 mb-4">
                    <label class="fw-bold text-white"><i class="fa-solid fa-calendar-day me-1"></i> Date de début</label>
                    <div class="bg-light text-dark p-2 rounded text-center">
                        {{ $leave->start_date->format('d/m/Y') }}
                    </div>
                </div>

                <div class="col-md-4 mb-4">
                    <label class="fw-bold text-white"><i class="fa-solid fa-calendar-day me-1"></i> Date de fin</label>
                    <div class="bg-light text-dark p-2 rounded text-center">
                        {{ $leave->end_date->format('d/m/Y') }}
                    </div>
                </div>

                <div class="col-md-4 mb-4">
                    <label class="fw-bold text-white"><i class="fa-solid fa-hourglass-half me-1"></i> Durée totale</label>
                    <div class="bg-light text-dark p-2 rounded fw-bold text-center">
                        {{ $leave->days_count }} jour(s)
                    </div>
                </div>

                {{-- Statut Actuel avec correction de couleur et de casse (Majuscules accentuées) --}}
                <div class="col-md-12 mb-4">
                    <label class="fw-bold text-white"><i class="fa-solid fa-circle-info me-1"></i> Statut de la demande</label>
                    
                    @php
                        // On utilise mb_strtoupper pour gérer correctement les accents comme le "É"
                        $statusUpper = mb_strtoupper($leave->status, 'UTF-8');
                    @endphp

                    <div class="p-3 rounded text-center fw-bold shadow-sm 
                        {{ $leave->status == 'Approuvé' ? 'bg-success text-white' : ($leave->status == 'Refusé' ? '' : 'bg-warning text-dark') }}"
                        style="{{ $leave->status == 'Refusé' ? 'background-color: #d9534f; color: white;' : '' }}">
                        
                        <i class="fa-solid {{ $leave->status == 'Approuvé' ? 'fa-check-double' : ($leave->status == 'Refusé' ? 'fa-xmark' : 'fa-clock') }} me-2"></i>
                        DEMANDE {{ $statusUpper }}
                    </div>
                </div>

                {{-- Motif / Justification (Bloc Large) --}}
                <div class="col-md-12 mb-4">
                    <label class="fw-bold text-white"><i class="fa-solid fa-comment-dots me-1"></i> Motif / Justification</label>
                    <div class="bg-light text-dark p-3 rounded" style="min-height: 100px; white-space: pre-wrap;">{{ $leave->reason ?? 'Aucune justification fournie' }}</div>
                </div>
            </div>

            <hr style="border-top: 1px solid rgba(255,255,255,0.3);">

            {{-- Zone d'actions : Uniquement le bouton Retour centré --}}
            <div class="row mt-4 justify-content-center">
                <div class="col-md-4 mb-2">
                    <a href="{{ route('leaveList') }}" class="btn w-100 text-white fw-bold shadow-sm" style="background-color: rgb(144, 142, 140); border: none;">
                        <i class="fa-solid fa-arrow-left me-1"></i> Retour à la liste
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection