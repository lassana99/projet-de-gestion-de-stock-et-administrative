@extends('admin.layouts.master')

@section('content')
@php
function formatValue($value) {
    if ($value === null || $value === 0) return '0';
    return ($value > 1)
        ? number_format(round($value), 0, ',', ' ')
        : number_format($value, 2, ',', ' ');
}
@endphp

<div class="container-fluid">
    <div class="card shadow mb-4">
        {{-- En-tête avec Recherche et Boutons --}}
        <div class="card-header py-3" style="background-color:#6c757d;">
            <div class="d-flex justify-content-between align-items-center">
                
                {{-- Barre de recherche --}}
                <form action="{{ route('funding.list') }}" method="GET" class="d-flex" style="max-width: 450px;">
                    <div class="input-group">
                        <input type="text" name="searchKey" class="form-control"
                               placeholder="Request, Banque..."
                               value="{{ request('searchKey') }}">
                        <div class="input-group-append">
                            <button class="btn" type="submit"
                                    style="background-color: rgb(249, 138, 27); color: white; border: none;">
                                <i class="fa-solid fa-magnifying-glass"></i>
                            </button>
                            
                            @if(request()->filled('searchKey'))
                                <a href="{{ route('funding.list') }}" class="btn btn-secondary" title="Effacer le filtre" style="border: none;">
                                    <i class="fa-solid fa-xmark"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                </form>

                <div class="d-flex align-items-center">
                    {{-- Bouton Exportation Excel Direct --}}
                    <a href="{{ route('funding.exportExcel', ['searchKey' => request('searchKey')]) }}" 
                       class="btn btn-light mr-3" 
                       style="font-weight: 500;">
                        <i class="fa-solid fa-file-excel text-success me-1"></i> Exporter en Excel
                    </a>

                    {{-- Bouton Ajouter --}}
                    <a href="{{ route('funding.create') }}"
                       class="btn text-white fw-bold"
                       style="background-color: rgb(249,138,27); border:none;">
                        <i class="fa-solid fa-plus me-1"></i> Ajouter un prêt bancaire
                    </a>
                </div>
            </div>
        </div>

        <div class="card-body" style="background-color:#6c757d;">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="table-responsive">
                <style>
                    thead th, tbody td {
                        white-space: nowrap;
                        text-align: center !important;
                        vertical-align: middle !important;
                        color: white !important;
                    }

                    .table-bordered, 
                    .table-bordered td, 
                    .table-bordered th {
                        border: 1px solid white !important;
                    }

                    tbody tr:hover {
                        background-color: #5a6268 !important;
                        cursor: pointer;
                    }

                    thead th:nth-child(2),
                    tbody td:nth-child(2) {
                        min-width: 120px;
                        max-width: 160px;
                        white-space: normal; 
                    }

                    .table-actions {
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        gap: 8px;
                    }
                </style>

                <table class="table table-bordered align-middle text-white"
                       style="background-color:#6c757d;">
                    <thead>
                        <tr class="bg-secondary text-white">
                            <th>Request</th>
                            <th>Banque</th>
                            <th>Montant emprunté</th>
                            <th>Nombre de jours</th>
                            <th>Taux</th>
                            <th>Montant à payer</th>
                            <th>Date</th>
                            <th style="min-width: 150px;">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($fundings as $funding)
                            <tr>
                                <td>{{ $funding->motif }}</td>
                                <td>{{ $funding->nom_de_banque }}</td>
                                <td><strong>{{ formatValue($funding->montant_emprunte) }} FCFA</strong></td>
                                <td>{{ $funding->nombre_de_jours }}</td>
                                <td>{{ intval($funding->taux) }}%</td>
                                <td><strong>{{ formatValue($funding->montant_a_payer) }} FCFA</strong></td>
                                <td>{{ \Carbon\Carbon::parse($funding->date)->format('d/m/Y') }}</td>
                                <td>
                                    <div class="table-actions">
                                        <a href="{{ route('funding.details', $funding->id) }}"
                                           title="Détails"
                                           class="btn d-flex align-items-center justify-content-center p-0"
                                           style="background-color: rgb(140, 142, 151); border:none; width:36px; height:36px;">
                                            <i class="fa-solid fa-eye text-white" style="font-size:1.1rem;"></i>
                                        </a>

                                        <a href="{{ route('funding.edit', $funding->id) }}"
                                           title="Modifier"
                                           class="btn d-flex align-items-center justify-content-center p-0"
                                           style="background-color: rgb(109, 103, 97); border:none; width:36px; height:36px; color:white;">
                                            <i class="fa-solid fa-pen-to-square" style="font-size:1.1rem;"></i>
                                        </a>

                                        <form action="{{ route('funding.destroy', $funding->id) }}"
                                              method="POST" class="delete-form m-0"
                                              style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="btn d-flex align-items-center justify-content-center p-0"
                                                    title="Supprimer"
                                                    style="background-color: rgb(101, 77, 79); border:none; width:36px; height:36px; color:white;">
                                                <i class="fa-solid fa-trash-can" style="font-size:1.1rem;"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-white">
                                    Aucun résultat trouvé pour "{{ request('searchKey') }}".
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="d-flex justify-content-end mt-3">
                    {{ $fundings->appends(request()->all())->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Scripts --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.delete-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Confirmer la suppression ?',
                text: "Cette action est irréversible.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#90908c',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Oui, supprimer',
                cancelButtonText: 'Annuler'
            }).then((result) => {
                if (result.isConfirmed) {
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
@endsection