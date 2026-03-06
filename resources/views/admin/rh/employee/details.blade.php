@extends('admin.layouts.master')

@section('content')
<div class="container-fluid">
    <div class="row">
        {{-- Informations Employé (Profil) --}}
        <div class="col-md-4">
            <div class="card shadow mb-4 text-center" style="background-color:#6c757d; color:white;">
                <div class="card-header py-3" style="background-color:#6c757d;">
                    <h5 class="m-0 font-weight-bold">Profil du Personnel</h5>
                </div>
                <div class="card-body" style="background-color:#6c757d;">
                    {{-- Photo de profil --}}
                    @if($employee->photo)
                        <img src="{{ asset('storage/'.$employee->photo) }}" class="img-profile rounded-circle mb-3" 
                             style="width:140px; height:140px; object-fit:cover; border:4px solid white; box-shadow: 0 4px 8px rgba(0,0,0,0.2);">
                    @else
                        <div class="bg-secondary rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width:140px; height:140px; border:4px solid white;">
                            <i class="fa-solid fa-user fa-5x text-white"></i>
                        </div>
                    @endif

                    <h4 class="mb-1 fw-bold">{{ $employee->full_name }}</h4>
                    <span class="badge bg-warning text-dark px-3 py-2 mb-3" style="font-size: 0.85rem;">
                        <i class="fa-solid fa-hashtag me-1"></i> {{ $employee->matricule }}
                    </span>
                    
                    <hr class="bg-white opacity-25">

                    {{-- Détails du Personnel --}}
                    <div class="text-start px-2" style="font-size: 0.95rem;">
                        {{-- Section Contact --}}
                        <p class="mb-2 text-warning fw-bold small text-uppercase">Coordonnées</p>
                        <p class="mb-2">
                            <i class="fa-solid fa-phone me-2"></i> <strong>Tél :</strong>
                            <span class="text-light">{{ $employee->phone }}</span>
                        </p>
                        <p class="mb-2">
                            <i class="fa-solid fa-envelope me-2"></i> <strong>Email :</strong>
                            <span class="text-light">{{ $employee->email }}</span>
                        </p>

                        <hr class="bg-white opacity-25">
                        
                        {{-- Section Professionnelle --}}
                        <p class="mb-2 text-warning fw-bold small text-uppercase">Professionnel</p>
                        <p class="mb-2">
                            <i class="fa-solid fa-briefcase me-2"></i> <strong>Poste :</strong>
                            <span class="text-light">{{ $employee->position }}</span>
                        </p>
                        <p class="mb-2">
                            <i class="fa-solid fa-graduation-cap me-2"></i> <strong>Diplôme :</strong>
                            <span class="text-light">{{ $employee->diploma }} ({{ $employee->specialty }})</span>
                        </p>
                        <p class="mb-2">
                            <i class="fa-solid fa-clock-rotate-left me-2"></i> <strong>Expérience :</strong>
                            <span class="text-light">{{ $employee->experience_years }}</span>
                        </p>

                        <hr class="bg-white opacity-25">

                        {{-- Section Vie Privée --}}
                        <p class="mb-2 text-warning fw-bold small text-uppercase">Vie Privée & Identité</p>
                        <p class="mb-2">
                            <i class="fa-solid fa-heart me-2"></i> <strong>Situation :</strong>
                            <span class="text-light">{{ $employee->marital_status }} ({{ $employee->children_count }} enfant(s))</span>
                        </p>
                        <p class="mb-2">
                            <i class="fa-solid fa-id-card me-2"></i> <strong>ID :</strong>
                            <span class="text-light">{{ $employee->getRealIdType() }} - {{ $employee->id_number }}</span>
                        </p>
                        <p class="mb-2">
                            <i class="fa-solid fa-truck-medical me-2"></i> <strong>Urgence :</strong><br>
                            <span class="ms-4 text-light">{{ $employee->emergency_contact }}</span>
                        </p>
                    </div>

                    <div class="mt-4">
                        <a href="{{ route('employeeEdit', $employee->id) }}" class="btn w-100 fw-bold shadow-sm" style="background-color: rgb(249, 138, 27); color: white; border: none;">
                            <i class="fa-solid fa-pen-to-square me-1"></i> Modifier les informations
                        </a>
                    </div>
                </div>
            </div>

            <a href="{{ route('employeeList') }}" class="btn btn-secondary w-100 shadow-sm fw-bold mb-4" style="background-color: rgb(144, 142, 140); border: none; padding: 10px;">
                <i class="fa-solid fa-arrow-left me-1"></i> Retour à la liste
            </a>
        </div>

        {{-- Gestion des Congés (Historique) --}}
        <div class="col-md-8">
            <div class="card shadow mb-4" style="background-color:#6c757d;">
                <div class="card-header d-flex justify-content-between align-items-center" style="background-color:#6c757d; color: white;">
                    <h5 class="m-0 font-weight-bold"><i class="fa-solid fa-calendar-check me-2"></i> Historique des Congés</h5>
                </div>
                <div class="card-body" style="background-color:#6c757d;">
                    <div class="table-responsive">
                        <table class="table text-white table-bordered border-secondary">
                            <thead>
                                <tr class="text-center" style="background-color: rgba(255,255,255,0.1);">
                                    <th>Type</th>
                                    <th>Début</th>
                                    <th>Fin</th>
                                    <th>Jours</th>
                                    <th>Statut</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($employee->leaves as $leave)
                                <tr class="text-center align-middle">
                                    <td>{{ $leave->leave_type }}</td>
                                    <td>{{ $leave->start_date->format('d/m/Y') }}</td>
                                    <td>{{ $leave->end_date->format('d/m/Y') }}</td>
                                    <td>{{ $leave->days_count }}</td>
                                    <td>
                                        @if($leave->status == 'Approuvé')
                                            <span class="badge bg-success">Approuvé</span>
                                        @elseif($leave->status == 'Refusé')
                                            <span class="badge bg-danger">Refusé</span>
                                        @else
                                            <span class="badge bg-warning text-dark">En attente</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <i class="fa-solid fa-folder-open fa-3x mb-3 opacity-25"></i><br>
                                        <span class="opacity-50">Aucun congé enregistré pour cet employé.</span>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection