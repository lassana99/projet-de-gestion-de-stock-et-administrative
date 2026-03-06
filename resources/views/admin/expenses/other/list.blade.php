@extends('admin.layouts.master')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        {{-- En-tête : Recherche, Export et Ajout --}}
        <div class="card-header py-3" style="background-color:#6c757d; border-bottom: 1px solid white;">
            <div class="d-flex justify-content-between align-items-center">
                
                {{-- Barre de recherche globale --}}
                <form action="{{ route('otherExpenseList') }}" method="GET" class="d-flex" style="max-width: 450px;">
                    <div class="input-group">
                        <input type="text" name="searchKey" class="form-control" 
                               placeholder="Rechercher Nom, N°, Motif, Désignation..." 
                               value="{{ request('searchKey') }}">
                        <div class="input-group-append">
                            <button class="btn" type="submit" style="background-color: rgb(249, 138, 27); color: white; border: none;">
                                <i class="fa-solid fa-magnifying-glass"></i>
                            </button>
                            @if(request()->filled('searchKey'))
                                <a href="{{ route('otherExpenseList') }}" class="btn btn-secondary" title="Effacer le filtre">
                                    <i class="fa-solid fa-xmark"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                </form>

                {{-- Groupe d'actions --}}
                <div class="d-flex align-items-center">
                    <a href="{{ route('otherExpenseExportExcel', ['searchKey' => request('searchKey')]) }}" 
                       class="btn btn-light mr-3" 
                       style="font-weight: 500;">
                        <i class="fa-solid fa-file-excel text-success me-1"></i> Exporter en Excel
                    </a>

                    <a href="{{ route('otherExpenseCreate') }}" class="btn text-white fw-bold shadow-sm" 
                       style="background-color: rgb(249, 138, 27); border: none;">
                        <i class="fa-solid fa-plus me-1"></i> Nouvelle Dépense
                    </a>
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
                    /* Alignement et bordures blanches pour cohérence globale */
                    #dataTableOther {
                        border: 1px solid white !important;
                        border-collapse: collapse;
                    }

                    #dataTableOther thead th, 
                    #dataTableOther tbody td {
                        text-align: center !important;
                        vertical-align: middle !important;
                        white-space: nowrap;
                        color: white !important;
                        border: 1px solid white !important;
                    }

                    #dataTableOther tbody tr:hover {
                        background-color: #5a6268 !important;
                        cursor: pointer;
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
                    }

                    .action-btn:hover {
                        transform: scale(1.1);
                    }
                </style>

                <table id="dataTableOther" class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr class="bg-secondary text-white">
                            <th>Numéro</th>
                            <th>Date</th>
                            <th>Bénéficiaire</th>
                            <th>Motif</th>
                            <th>Montant (FCFA)</th>
                            <th style="width: 140px;">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($expenses as $exp)
                            <tr>
                                <td class="fw-bold">{{ $exp->number }}</td>
                                <td>{{ $exp->date->format('d/m/Y') }}</td>
                                <td class="text-left px-3">{{ $exp->full_name }}</td>
                                <td>{{ $exp->getRealReason() }}</td>
                                <td class="fw-bold" style="color: #ffc107;">
                                    {{ $exp->formattedAmount() }}
                                </td>
                                <td>
                                    <div class="d-flex justify-content-center gap-2">
                                        {{-- Bouton Voir --}}
                                        <a href="{{ route('otherExpenseDetails', $exp->id) }}" 
                                           class="action-btn" title="Détails" 
                                           style="background-color: rgb(140, 142, 151);">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>

                                        {{-- Bouton Modifier --}}
                                        <a href="{{ route('otherExpenseEdit', $exp->id) }}" 
                                           class="action-btn" title="Modifier" 
                                           style="background-color: rgb(109, 103, 97);">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>

                                        {{-- Bouton Supprimer --}}
                                        <form action="{{ route('otherExpenseDelete', $exp->id) }}" method="POST" class="delete-form m-0">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="action-btn" title="Supprimer" 
                                                    style="background-color: rgb(101, 77, 79);">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-white-50">
                                    <i class="fa-solid fa-inbox fa-2x mb-2"></i><br>
                                    Aucune dépense trouvée pour "{{ request('searchKey') }}".
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                {{-- Pagination --}}
                <div class="d-flex justify-content-end mt-3">
                    {{ $expenses->appends(request()->all())->links() }}
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
                text: "Cette action est irréversible. Voulez-vous continuer ?", 
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