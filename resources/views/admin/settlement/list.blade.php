@extends('admin.layouts.master')

@section('content')
<style>
    /* ✅ Alignement et bordures blanches */
    #dataTable {
        border: 1px solid white !important;
        border-collapse: collapse;
    }

    #dataTable thead th,
    #dataTable tbody td {
        vertical-align: middle !important;
        text-align: center !important;
        border: 1px solid white !important; /* Traits blancs */
        white-space: nowrap;
        color: white !important;
    }

    /* ✅ Démarcation de la ligne au survol */
    #dataTable tbody tr:hover {
        background-color: #5a6268 !important; /* Gris légèrement différent pour le survol */
        cursor: pointer;
    }

    /* ✅ Actions bien rangées */
    .table-actions {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 8px;
    }

    .action-btn {
        width: 36px;
        height: 36px;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
        border: none;
        color: white;
        transition: transform 0.2s;
    }

    .action-btn:hover {
        transform: scale(1.1);
        color: white;
    }

    .badge {
        font-weight: 600;
        border-radius: 6px;
        padding: 0.35rem 0.75rem;
    }
</style>

<div class="container-fluid">
    <div class="card shadow mb-4">
        {{-- Header avec barre de recherche, Export et bouton Ajouter --}}
        <div class="card-header py-3" style="background-color:#6c757d; border-bottom: 1px solid white;">
            <div class="d-flex justify-content-between align-items-center">
                
                {{-- Barre de recherche GLOBALE --}}
                <form action="{{ route('settlementList') }}" method="GET" class="d-flex" style="max-width: 500px;">
                    <div class="input-group">
                        <input type="text" name="searchKey" class="form-control"
                               placeholder="Rechercher (Nom, Montant, Type, Statut...)"
                               value="{{ request('searchKey') }}">
                        <div class="input-group-append">
                            <button class="btn" type="submit"
                                    style="background-color: rgb(249, 138, 27); color: white; border: none;">
                                <i class="fa-solid fa-magnifying-glass"></i>
                            </button>
                            
                            @if(request()->filled('searchKey'))
                                <a href="{{ route('settlementList') }}" class="btn btn-secondary" title="Effacer le filtre" style="border: none;">
                                    <i class="fa-solid fa-xmark"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                </form>

                <div class="d-flex align-items-center">
                    {{-- Bouton Exportation Excel Direct --}}
                    <a href="{{ route('settlementExportExcel', ['searchKey' => request('searchKey')]) }}" 
                       class="btn btn-light mr-3" 
                       style="font-weight: 500;">
                        <i class="fa-solid fa-file-excel text-success me-1"></i> Exporter en Excel
                    </a>

                    <a href="{{ route('settlementCreate') }}" class="btn text-white fw-bold shadow-sm" 
                       style="background-color: rgb(249, 138, 27); border: none;">
                        <i class="fa-solid fa-plus"></i> Nouveau Règlement
                    </a>
                </div>
            </div>
        </div>

        <div class="card-body" style="background-color:#6c757d;">
            {{-- Alertes de session --}}
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="table-responsive">
                <table class="table table-bordered text-white" id="dataTable" width="100%" cellspacing="0" style="background-color:#6c757d; border: 1px solid white !important;">
                    <thead>
                        <tr class="bg-secondary text-white">
                            <th>Type</th>
                            <th>Nom du Tiers</th>
                            <th>Montant</th>
                            <th>Échéance</th>
                            <th>Statut</th>
                            <th style="width: 140px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($settlements as $s)
                        <tr>
                            <td>
                                <span class="badge {{ $s->type == 'debt' ? 'bg-primary' : 'bg-danger' }}">
                                    {{ $s->type == 'debt' ? 'Créance' : 'Dette' }}
                                </span>
                            </td>
                            <td class="fw-bold"><strong>{{ $s->entity_name }}</strong></td>
                            
                            <td class="fw-bold">
                                <div><strong>{{ number_format($s->amount, 0, '', ' ') }} {{ $s->currency }}</strong></div>
                                @if($s->currency !== 'FCFA')
                                    <div style="font-size: 0.8rem; color: #ffc107;">
                                        ({{ number_format($s->amount_fcfa, 0, '', ' ') }} FCFA)
                                    </div>
                                @endif
                            </td>
                            
                            @php
                                $days = (int) now()->startOfDay()->diffInDays($s->due_date->startOfDay(), false);
                            @endphp
                            
                            <td class="{{ ($s->status == 'pending') ? ($days < 0 ? 'text-danger font-weight-bold' : 'text-warning font-weight-bold') : '' }}">
                                {{ $s->due_date->format('d/m/Y') }}
                                @if($s->status == 'pending')
                                    <i class="fa-solid fa-triangle-exclamation ml-1"></i>
                                @endif
                            </td>

                            <td>
                                <span class="badge {{ $s->status == 'paid' ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $s->status == 'paid' ? 'Réglé' : 'En attente' }}
                                </span>
                            </td>
                            <td>
                                <div class="table-actions">
                                    <a href="{{ route('settlementDetails', $s->id) }}" class="action-btn" 
                                       style="background-color: rgb(140, 142, 151);" title="Détails">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>

                                    <a href="{{ route('settlementEdit', $s->id) }}" class="action-btn" 
                                       style="background-color: rgb(109, 103, 97);" title="Modifier">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>

                                    <button type="button" class="action-btn delete-settlement-btn" 
                                            style="background-color: rgb(101, 77, 79);" 
                                            title="Supprimer"
                                            data-url="{{ route('settlementDelete', $s->id) }}" 
                                            data-name="{{ $s->entity_name }}">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">
                                Aucun résultat trouvé pour "{{ request('searchKey') }}".
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="d-flex justify-content-end mt-3">
                    {{ $settlements->appends(request()->all())->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Scripts JS --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.delete-settlement-btn').forEach(button => {
            button.addEventListener('click', function() {
                const url = this.getAttribute('data-url');
                const name = this.getAttribute('data-name');

                Swal.fire({
                    title: 'Confirmer la suppression ?',
                    text: "Voulez-vous vraiment supprimer le règlement de \"" + name + "\" ? Cette action est irréversible.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#90908c',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Oui, supprimer',
                    cancelButtonText: 'Annuler'
                }).then((result) => {
                    if (result.isConfirmed) {
                        let form = document.createElement('form');
                        form.action = url;
                        form.method = 'POST';
                        form.innerHTML = `
                            @csrf
                            @method('DELETE')
                        `;
                        document.body.appendChild(form);
                        form.submit();
                    }
                });
            });
        });

        setTimeout(() => {
            document.querySelectorAll('.alert').forEach(alert => {
                alert.style.transition = 'opacity 0.5s ease';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            });
        }, 5000);
    });
</script>

@include('sweetalert::alert')
@endsection