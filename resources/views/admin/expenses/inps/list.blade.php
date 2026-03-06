@extends('admin.layouts.master')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        {{-- En-tête : Recherche, Export et Ajout --}}
        <div class="card-header py-3" style="background-color:#6c757d; border-bottom: 1px solid white;">
            <div class="d-flex justify-content-between align-items-center">
                
                {{-- Barre de recherche globale --}}
                <form action="{{ route('inpsList') }}" method="GET" class="d-flex" style="max-width: 450px;">
                    <div class="input-group">
                        <input type="text" name="searchKey" class="form-control" 
                               placeholder="Rechercher N°, Employé..." 
                               value="{{ request('searchKey') }}">
                        <div class="input-group-append">
                            <button class="btn" type="submit" style="background-color: rgb(249, 138, 27); color: white; border: none;">
                                <i class="fa-solid fa-magnifying-glass"></i>
                            </button>
                            @if(request()->filled('searchKey'))
                                <a href="{{ route('inpsList') }}" class="btn btn-secondary" title="Effacer le filtre">
                                    <i class="fa-solid fa-xmark"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                </form>

                {{-- Groupe d'actions --}}
                <div class="d-flex align-items-center">
                    <a href="{{ route('inpsExportExcel', ['searchKey' => request('searchKey')]) }}" 
                       class="btn btn-light mr-3" 
                       style="font-weight: 500;">
                        <i class="fa-solid fa-file-excel text-success me-1"></i> Exporter
                    </a>

                    <a href="{{ route('inpsCreate') }}" class="btn text-white fw-bold shadow-sm" 
                       style="background-color: rgb(249, 138, 27); border: none;">
                        <i class="fa-solid fa-plus me-1"></i> Nouveau
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
                    /* Alignement et bordures blanches */
                    #dataTableInps {
                        border: 1px solid white !important;
                        border-collapse: collapse;
                    }

                    #dataTableInps thead th, 
                    #dataTableInps tbody td {
                        text-align: center !important; 
                        vertical-align: middle !important; 
                        white-space: nowrap; 
                        color: white !important; 
                        border: 1px solid white !important; 
                    }

                    #dataTableInps tbody tr:hover {
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
                    
                    .badge-info-custom {
                        background-color: #36b9cc !important; /* Bleu turquoise pro */
                        color: white !important;
                        font-weight: 600;
                        padding: 0.5rem 0.8rem;
                        border-radius: 20px;
                    }
                </style>

                <table id="dataTableInps" class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr class="bg-secondary text-white">
                            <th>Numéro</th>
                            <th>Période</th>
                            <th>Personnes concernées</th>
                            <th>Montant (FCFA)</th>
                            <th>Date de paiement</th>
                            <th style="width: 140px;">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($payments as $p)
                            <tr>
                                <td class="fw-bold">{{ $p->number }}</td>
                                <td>Du {{ $p->start_date->format('d/m/Y') }} au {{ $p->end_date->format('d/m/Y') }}</td>
                                <td>
                                    {{-- Retour au badge bleu avec le texte complet --}}
                                    <span class="badge badge-info-custom shadow-sm">
                                        <i class="fa-solid fa-users me-1"></i> {{ $p->employees->count() }} personnes
                                    </span>
                                </td>
                                <td class="fw-bold">
                                    {{ $p->formattedAmount() }}
                                </td>
                                <td>{{ $p->payment_date->format('d/m/Y') }}</td>
                                <td>
                                    <div class="d-flex justify-content-center gap-2">
                                        {{-- Voir --}}
                                        <a href="{{ route('inpsDetails', $p->id) }}" 
                                           class="action-btn" title="Détails" 
                                           style="background-color: rgb(140, 142, 151);">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>

                                        {{-- Modifier --}}
                                        <a href="{{ route('inpsEdit', $p->id) }}" 
                                           class="action-btn" title="Modifier" 
                                           style="background-color: rgb(109, 103, 97);">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>

                                        {{-- Supprimer --}}
                                        <form action="{{ route('inpsDelete', $p->id) }}" method="POST" class="delete-form m-0">
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
                                    Aucun paiement INPS trouvé pour "{{ request('searchKey') }}".
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                {{-- Pagination --}}
                <div class="d-flex justify-content-end mt-3">
                    {{ $payments->appends(request()->all())->links() }}
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
                text: "Voulez-vous vraiment supprimer ce paiement INPS ? Cette action est irréversible.", 
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