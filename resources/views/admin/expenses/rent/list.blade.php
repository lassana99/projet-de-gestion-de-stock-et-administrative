@extends('admin.layouts.master')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">

        {{-- En-tête : Recherche, Export et Ajout --}}
        <div class="card-header py-3" style="background-color:#6c757d; border-bottom: 1px solid white;">
            <div class="d-flex justify-content-between align-items-center">

                {{-- Barre de recherche globale --}}
                <form action="{{ route('rentList') }}" method="GET" class="d-flex" style="max-width: 450px;">
                    <div class="input-group">
                        <input type="text" name="searchKey" class="form-control"
                               placeholder="Numéro, Mois, Structure, Référence..."
                               value="{{ request('searchKey') }}">
                        <div class="input-group-append">
                            <button class="btn" type="submit"
                                    style="background-color: rgb(249, 138, 27); color: white; border: none;">
                                <i class="fa-solid fa-magnifying-glass"></i>
                            </button>
                            @if(request()->filled('searchKey'))
                                <a href="{{ route('rentList') }}" class="btn btn-secondary" title="Effacer le filtre">
                                    <i class="fa-solid fa-xmark"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                </form>

                {{-- Groupe d'actions --}}
                <div class="d-flex align-items-center">
                    <a href="{{ route('rentExportExcel', ['searchKey' => request('searchKey')]) }}" 
                       class="btn btn-light mr-3" 
                       style="font-weight: 500; border: 1px solid #ced4da;">
                        <i class="fa-solid fa-file-excel text-success me-1"></i> Exporter en Excel
                    </a>

                    <a href="{{ route('rentCreate') }}" class="btn text-white fw-bold shadow-sm"
                       style="background-color: rgb(249, 138, 27); border: none;">
                        <i class="fa-solid fa-plus me-1"></i> Nouveau Loyer
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
                    /* Alignement et bordures blanches pour cohérence avec Prix Fournisseurs */
                    #dataTableRent {
                        border: 1px solid white !important;
                        border-collapse: collapse;
                    }

                    #dataTableRent thead th, 
                    #dataTableRent tbody td {
                        text-align: center !important;
                        vertical-align: middle !important;
                        white-space: nowrap;
                        color: white !important;
                        border: 1px solid white !important;
                    }

                    #dataTableRent tbody tr:hover {
                        background-color: #5a6268 !important; /* Gris survol */
                        cursor: pointer;
                    }

                    .table-actions {
                        display: flex;
                        justify-content: center;
                        align-items: center;
                        gap: 8px;
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

                    .badge-status {
                        font-weight: 700;
                        text-transform: uppercase;
                        font-size: 0.75rem;
                        padding: 0.4rem 0.6rem;
                        border-radius: 4px;
                    }
                </style>

                <table id="dataTableRent" class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr class="bg-secondary text-white">
                            <th>Numéro</th>
                            <th>Mois</th>
                            <th>Structure</th>
                            <th>Référence</th>
                            <th>Date émission</th>
                            <th>Montant (FCFA)</th>
                            <th>Mode Payement</th>
                            <th>Statut</th>
                            <th style="width: 140px;">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($rents as $rent)
                            <tr>
                                <td class="fw-bold">{{ $rent->number }}</td>
                                <td class="fw-bold">{{ $rent->month }}</td>
                                <td>{{ $rent->structure }}</td>
                                <td>{{ $rent->reference ?? '-' }}</td>
                                <td>{{ $rent->issue_date ? $rent->issue_date->format('d/m/Y') : '-' }}</td>
                                <td class="fw-bold" style="color: #ffc107;">
                                    {{ number_format($rent->amount_fcfa, 0, ',', ' ') }}
                                </td>
                                <td>
                                    @if($rent->payment_mode === 'Autres')
                                        {{ $rent->payment_mode_other }}
                                    @else
                                        {{ $rent->payment_mode }}
                                    @endif
                                </td>
                                <td>
                                    @if($rent->status == 'Payé')
                                        <span class="badge bg-success text-white shadow-sm">Payé</span>
                                    @elseif($rent->status == 'Payé partiellement')
                                        <span class="badge bg-warning text-dark shadow-sm">Partiel</span>
                                    @else
                                        <span class="badge bg-danger text-white shadow-sm">Non Payé</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="table-actions">
                                        {{-- Bouton Détails --}}
                                        <a href="{{ route('rentDetails', $rent->id) }}"
                                           class="action-btn" title="Détails"
                                           style="background-color: rgb(140, 142, 151);">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>

                                        {{-- Bouton Modifier --}}
                                        <a href="{{ route('rentEdit', $rent->id) }}"
                                           class="action-btn" title="Modifier"
                                           style="background-color: rgb(109, 103, 97);">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>

                                        {{-- Bouton Supprimer --}}
                                        <form action="{{ route('rentDelete', $rent->id) }}" method="POST" class="delete-form m-0">
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
                                <td colspan="9" class="text-center py-5 text-white-50">
                                    <i class="fa-solid fa-inbox fa-2x mb-2"></i><br>
                                    Aucun loyer enregistré pour "{{ request('searchKey') }}".
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                {{-- Pagination personnalisée --}}
                <div class="d-flex justify-content-end mt-3">
                    {{ $rents->appends(request()->all())->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Scripts : SweetAlert2 pour la confirmation de suppression --}}
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.querySelectorAll('.delete-form').forEach(form => {
        form.addEventListener('submit', function(event) {
            event.preventDefault();
            Swal.fire({
                title: 'Confirmer la suppression',
                text: "Voulez-vous vraiment retirer ce loyer de la liste ? Cette opération est définitive.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#90908c', // Gris sombre
                cancelButtonColor: '#d33',     // Rouge
                confirmButtonText: 'Oui, supprimer',
                cancelButtonText: 'Annuler'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });

    // Auto-fermeture des messages flash après 5 secondes
    setTimeout(() => {
        document.querySelectorAll('.alert').forEach(alert => {
            alert.style.transition = 'opacity 0.8s ease';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 800);
        });
    }, 5000);
</script>
@endsection