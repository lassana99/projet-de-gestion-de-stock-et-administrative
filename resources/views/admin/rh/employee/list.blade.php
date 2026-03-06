@extends('admin.layouts.master')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        {{-- En-tête : Recherche, Export et Ajout --}}
        <div class="card-header py-3" style="background-color:#6c757d; border-bottom: 1px solid white;">
            <div class="d-flex justify-content-between align-items-center">
                
                {{-- Barre de recherche globale --}}
                <form action="{{ route('employeeList') }}" method="GET" class="d-flex" style="max-width: 450px;">
                    <div class="input-group">
                        <input type="text" name="searchKey" class="form-control" 
                               placeholder="Nom, Matricule, Spécialité..." 
                               value="{{ request('searchKey') }}">
                        <div class="input-group-append">
                            <button class="btn" type="submit" style="background-color: rgb(249, 138, 27); color: white; border: none;">
                                <i class="fa-solid fa-magnifying-glass"></i>
                            </button>
                            @if(request()->filled('searchKey'))
                                <a href="{{ route('employeeList') }}" class="btn btn-secondary" title="Effacer le filtre">
                                    <i class="fa-solid fa-xmark"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                </form>

                {{-- Groupe d'actions --}}
                <div class="d-flex align-items-center">
                    <a href="{{ route('employeeExportExcel', ['searchKey' => request('searchKey')]) }}" 
                       class="btn btn-light mr-3" 
                       style="font-weight: 500;">
                        <i class="fa-solid fa-file-excel text-success me-1"></i> Exporter en Excel
                    </a>

                    {{-- Masquer le bouton d'ajout pour l'admin --}}
                    @if(auth()->user()->role !== 'admin')
                        <a href="{{ route('employeeCreate') }}" class="btn text-white fw-bold shadow-sm" 
                           style="background-color: rgb(249, 138, 27); border: none;">
                            <i class="fa-solid fa-plus me-1"></i> Nouveau Personnel
                        </a>
                    @endif
                </div>
            </div>
        </div>

        {{-- Corps de la table --}}
        <div class="card-body" style="background-color:#6c757d;">
            @if(session('success')) 
                <div class="alert alert-success border-0 shadow-sm">{{ session('success') }}</div> 
            @endif

            <div class="table-responsive">
                <style>
                    /* ✅ Alignement et bordures blanches */
                    #dataTablePersonnel {
                        border: 1px solid white !important;
                        border-collapse: collapse;
                    }

                    #dataTablePersonnel thead th, 
                    #dataTablePersonnel tbody td {
                        text-align: center !important;
                        vertical-align: middle !important;
                        white-space: nowrap;
                        color: white !important;
                        border: 1px solid white !important;
                    }

                    #dataTablePersonnel tbody tr:hover {
                        background-color: #5a6268 !important;
                    }

                    /* ✅ Style optimisé pour les images */
                    .img-container {
                        width: 70px;
                        display: flex;
                        justify-content: center;
                    }

                    .img-thumbnail-list {
                        width: 60px;
                        height: 60px;
                        border-radius: 10px;
                        object-fit: cover;
                        border: 2px solid white;
                        background-color: #fff;
                        box-shadow: 0 4px 8px rgba(0,0,0,0.3);
                        transition: transform 0.3s ease-in-out;
                    }

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

                    .action-btn:hover:not(.disabled) {
                        transform: scale(1.1);
                    }

                    .action-btn.disabled {
                        background-color: #4a4a4a !important;
                        opacity: 0.5;
                        cursor: not-allowed;
                        pointer-events: none; /* Désactive le clic */
                    }
                </style>

                <table id="dataTablePersonnel" class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr class="bg-secondary text-white">
                            <th style="width: 80px;">Photo</th>
                            <th>Matricule</th>
                            <th>Nom & Prénom</th>
                            <th>Fonction / Service</th>
                            <th style="width: 140px;">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($employees as $emp)
                            <tr>
                                <td>
                                    <div class="img-container mx-auto">
                                        @if($emp->photo)
                                            <img src="{{ asset('storage/'.$emp->photo) }}" class="img-thumbnail-list shadow">
                                        @else
                                            <div class="d-inline-flex align-items-center justify-content-center bg-secondary img-thumbnail-list shadow">
                                                <i class="fa-solid fa-user text-white fa-lg"></i>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="fw-bold">{{ $emp->matricule }}</td>
                                <td class="text-left px-3">{{ $emp->full_name }}</td>
                                <td>{{ $emp->position }}</td>
                                <td>
                                    <div class="d-flex justify-content-center gap-2">
                                        
                                        {{-- LOGIQUE DU BOUTON DETAILS --}}
                                        @php
                                            // Autorisé si : pas admin OU (est admin ET email correspond)
                                            $canView = (auth()->user()->role !== 'admin') || ($emp->email === auth()->user()->email);
                                        @endphp

                                        @if($canView)
                                            <a href="{{ route('employeeDetails', $emp->id) }}" 
                                               class="action-btn" title="Voir les détails" 
                                               style="background-color: rgb(140, 142, 151);">
                                                <i class="fa-solid fa-eye"></i>
                                            </a>
                                        @else
                                            <span class="action-btn disabled" title="Accès restreint à votre profil uniquement">
                                                <i class="fa-solid fa-eye-slash"></i>
                                            </span>
                                        @endif

                                        {{-- Actions masquées si l'utilisateur est admin --}}
                                        @if(auth()->user()->role !== 'admin')
                                            {{-- Modifier --}}
                                            <a href="{{ route('employeeEdit', $emp->id) }}" 
                                               class="action-btn" title="Modifier" 
                                               style="background-color: rgb(109, 103, 97);">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </a>

                                            {{-- Supprimer --}}
                                            <form action="{{ route('employeeDelete', $emp->id) }}" method="POST" class="delete-form m-0">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="action-btn" title="Supprimer" 
                                                        style="background-color: rgb(101, 77, 79);">
                                                    <i class="fa-solid fa-trash-can"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-white-50">
                                    <i class="fa-solid fa-users-slash fa-2x mb-2"></i><br>
                                    Aucun personnel trouvé pour "{{ request('searchKey') }}".
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                {{-- Pagination --}}
                <div class="d-flex justify-content-end mt-3">
                    {{ $employees->appends(request()->all())->links() }}
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
                text: "Voulez-vous vraiment retirer ce membre du personnel ? Cette action est irréversible.", 
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