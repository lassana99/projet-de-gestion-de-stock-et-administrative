@extends('admin.layouts.master')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        {{-- En-tête : Recherche, Export et Ajout --}}
        <div class="card-header py-3" style="background-color:#6c757d; border-bottom: 1px solid white;">
            <div class="d-flex justify-content-between align-items-center">
                <form action="{{ route('leaveList') }}" method="GET" class="d-flex" style="max-width: 450px;">
                    <div class="input-group">
                        <input type="text" name="searchKey" class="form-control" placeholder="Rechercher employé..." value="{{ request('searchKey') }}">
                        <div class="input-group-append">
                            <button class="btn" type="submit" style="background-color:rgb(249, 138, 27); color:white; border: none;">
                                <i class="fa-solid fa-magnifying-glass"></i>
                            </button>
                            @if(request()->filled('searchKey'))
                                <a href="{{ route('leaveList') }}" class="btn btn-secondary" title="Effacer le filtre" style="border: none;">
                                    <i class="fa-solid fa-xmark"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                </form>
                <div class="d-flex align-items-center">
                    <a href="{{ route('leaveExportExcel', ['searchKey' => request('searchKey')]) }}" class="btn btn-light mr-3" style="font-weight: 500;">
                        <i class="fa-solid fa-file-excel text-success me-1"></i> Exporter
                    </a>
                    <a href="{{ route('leaveCreate') }}" class="btn text-white fw-bold shadow-sm" style="background-color:rgb(249, 138, 27); border: none;">
                        <i class="fa-solid fa-plus"></i> Nouvelle Demande
                    </a>
                </div>
            </div>
        </div>

        <div class="card-body" style="background-color:#6c757d;">
            @if(session('success')) 
                <div class="alert alert-success border-0 shadow-sm">{{ session('success') }}</div> 
            @endif

            <div class="table-responsive">
                <style>
                    #dataTableLeaves { border: 1px solid white !important; border-collapse: collapse; }
                    #dataTableLeaves thead th, #dataTableLeaves tbody td { 
                        text-align: center !important; 
                        vertical-align: middle !important; 
                        white-space: nowrap; 
                        color: white !important; 
                        border: 1px solid white !important; 
                    }
                    #dataTableLeaves tbody tr:hover { background-color: #5a6268 !important; cursor: pointer; }
                    
                    .action-btn {
                        width: 34px;
                        height: 34px;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        border-radius: 6px;
                        border: none;
                        transition: transform 0.2s;
                        color: white !important;
                        text-decoration: none;
                    }
                    .action-btn:hover:not(.disabled) { transform: scale(1.1); }

                    /* Style pour les boutons désactivés */
                    .action-btn.disabled {
                        background-color: #4a4a4a !important;
                        opacity: 0.5;
                        cursor: not-allowed;
                        pointer-events: none;
                    }
                </style>

                <table id="dataTableLeaves" class="table table-bordered text-white" width="100%">
                    <thead>
                        <tr class="bg-secondary text-white">
                            <th>Employé</th>
                            <th>Type</th>
                            <th>Période</th>
                            <th>Jours</th>
                            <th>Statut</th>
                            {{-- Colonne Décision réservée au superadmin --}}
                            @if(Auth::user()->role == 'superadmin')
                                <th>Décision</th>
                            @endif
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($leaves as $leave)
                            @php
                                // Logique de restriction : 
                                // Si c'est un admin, il ne peut agir que si l'email du congé est le sien.
                                // Si c'est un superadmin ou autre, il peut tout faire.
                                $isOwner = (Auth::user()->email === ($leave->employee->email ?? ''));
                                $canPerformAction = (Auth::user()->role !== 'admin' || $isOwner);
                            @endphp
                        <tr class="text-center">
                            <td><strong>{{ $leave->employee->full_name }}</strong></td>
                            <td>{{ $leave->leave_type }}</td>
                            <td>Du {{ $leave->start_date->format('d/m/Y') }} au {{ $leave->end_date->format('d/m/Y') }}</td>
                            <td><span class="badge bg-light text-dark">{{ $leave->days_count }}</span></td>
                            <td>
                                <span class="badge @if($leave->status == 'Approuvé') bg-success @elseif($leave->status == 'Refusé') bg-danger @else bg-warning text-dark @endif">
                                    {{ $leave->status }}
                                </span>
                            </td>

                            {{-- Colonne Décision (Superadmin uniquement) --}}
                            @if(Auth::user()->role == 'superadmin')
                            <td>
                                <div class="d-flex justify-content-center gap-2">
                                    <form action="{{ route('leaveUpdateStatus', $leave->id) }}" method="POST" class="d-inline">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="status" value="Approuvé">
                                        <button class="btn btn-sm btn-success shadow-sm" title="Approuver">
                                            <i class="fa-solid fa-check"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('leaveUpdateStatus', $leave->id) }}" method="POST" class="d-inline">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="status" value="Refusé">
                                        <button class="btn btn-sm btn-danger shadow-sm" title="Refuser">
                                            <i class="fa-solid fa-xmark"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                            @endif

                            {{-- Colonne Actions --}}
                            <td>
                                <div class="d-flex justify-content-center gap-2">
                                    {{-- Visualisation --}}
                                    <a href="{{ route('leaveDetails', $leave->id) }}" 
                                       class="action-btn {{ !$canPerformAction ? 'disabled' : '' }}" 
                                       title="{{ $canPerformAction ? 'Voir' : 'Accès restreint' }}" 
                                       style="background-color: rgb(140, 142, 151);">
                                        <i class="fa-solid {{ $canPerformAction ? 'fa-eye' : 'fa-eye-slash' }}"></i>
                                    </a>

                                    {{-- Modification --}}
                                    <a href="{{ route('leaveEdit', $leave->id) }}" 
                                       class="action-btn {{ !$canPerformAction ? 'disabled' : '' }}" 
                                       title="{{ $canPerformAction ? 'Modifier' : 'Modification interdite' }}" 
                                       style="background-color: rgb(109, 103, 97);">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>

                                    {{-- Suppression --}}
                                    @if($canPerformAction)
                                        <form action="{{ route('leaveDelete', $leave->id) }}" method="POST" class="delete-form m-0">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="action-btn" title="Supprimer" 
                                                    style="background-color: rgb(101, 77, 79);">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </form>
                                    @else
                                        <button class="action-btn disabled" title="Suppression interdite" style="background-color: rgb(101, 77, 79);">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="{{ Auth::user()->role == 'superadmin' ? 7 : 6 }}" class="text-center py-4">
                                Aucun congé enregistré pour "{{ request('searchKey') }}".
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="d-flex justify-content-end mt-3">
                    {{ $leaves->appends(request()->all())->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Scripts : SweetAlert2 pour la suppression --}}
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.querySelectorAll('.delete-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Confirmer la suppression', 
                text: "Voulez-vous vraiment supprimer cette demande de congé ? Cette action est irréversible.", 
                icon: 'warning',
                showCancelButton: true, 
                confirmButtonColor: '#90908c', 
                cancelButtonColor: '#d33', 
                confirmButtonText: 'Oui, supprimer',
                cancelButtonText: 'Annuler'
            }).then((result) => { 
                if (result.isConfirmed) form.submit(); 
            });
        });
    });

    // Auto-fermeture des alertes
    setTimeout(() => {
        document.querySelectorAll('.alert').forEach(alert => {
            alert.style.transition = 'opacity 0.8s ease';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 800);
        });
    }, 5000);
</script>
@endsection