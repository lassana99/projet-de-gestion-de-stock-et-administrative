@extends('admin.layouts.master')

@section('content')
@php
function formatValue($value) {
    if ($value === null || $value === 0) return '0';
    return ($value >= 1)
        ? number_format(round($value), 0, ',', ' ')
        : number_format($value, 2, ',', ' ');
}
@endphp

<div class="container-fluid">
    <div class="card shadow mb-4">
        {{-- En-tête avec Recherche et Boutons --}}
        <div class="card-header py-3" style="background-color:#6c757d; border-bottom: 1px solid white;">
            <div class="d-flex justify-content-between align-items-center">
                
                {{-- Barre de recherche --}}
                <form action="{{ route('machineList') }}" method="GET" class="d-flex" style="max-width: 450px;">
                    <div class="input-group">
                        <input type="text" name="searchKey" class="form-control"
                               placeholder="Référence..."
                               value="{{ request('searchKey') }}">
                        <div class="input-group-append">
                            <button class="btn" type="submit"
                                    style="background-color: rgb(249,138,27); color: white; border:none;">
                                <i class="fa-solid fa-magnifying-glass"></i>
                            </button>
                            
                            @if(request()->filled('searchKey'))
                                <a href="{{ route('machineList') }}" class="btn btn-secondary" title="Effacer le filtre" style="border: none;">
                                    <i class="fa-solid fa-xmark"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                </form>

                <div class="d-flex align-items-center">
                    {{-- Bouton Exportation Excel --}}
                    <a href="{{ route('machineExportExcel', ['searchKey' => request('searchKey')]) }}" 
                       class="btn btn-light mr-3" 
                       style="font-weight: 500;">
                        <i class="fa-solid fa-file-excel text-success me-1"></i> Exporter en Excel
                    </a>

                    <a href="{{ route('machineCreate') }}"
                       class="btn btn-warning text-white fw-bold shadow-sm"
                       style="background-color: rgb(249,138,27); border:none;">
                        <i class="fa-solid fa-plus"></i> Nouvelle rentabilité
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
                        text-align: center !important;
                        vertical-align: middle !important;
                        white-space: nowrap;
                        color: white !important;
                    }

                    #dataTable {
                        border: 1px solid white !important;
                        border-collapse: collapse;
                    }

                    #dataTable thead th, 
                    #dataTable tbody td {
                        border: 1px solid white !important;
                    }

                    #dataTable tbody tr:hover {
                        background-color: #5a6268 !important; 
                        cursor: pointer;
                    }

                    .table-actions {
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        gap: 8px;
                    }

                    .action-btn {
                        width: 36px;
                        height: 36px;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        border-radius: 6px;
                        border: none;
                        transition: transform 0.2s;
                        color: white;
                    }

                    .action-btn:hover {
                        transform: scale(1.1);
                        color: white;
                    }
                </style>

                <table id="dataTable" class="table table-bordered align-middle text-white"
                       style="background-color:#6c757d;">
                    <thead>
                        <tr class="bg-secondary text-white">
                            <th>Référence</th>
                            <th>Poids (KG)</th>
                            <th>Prix achat unité</th>
                            <th>Marge (%)</th>
                            <th>Bénéfice</th>
                            <th>Prix de vente HT</th>
                            <th>Prix de vente TTC</th>
                            <th>Date</th>
                            <th style="min-width: 150px;">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($machines as $machine)
                            <tr>
                                <td>{{ $machine->purchase_reference }}</td>
                                <td>{{ formatValue($machine->weight ?? 0) }}</td>
                                <td>
                                    <strong>
                                        {{ formatValue($machine->unit_purchase_price ?? 0) }}
                                        {{ $machine->purchase->currency ?? '' }}
                                    </strong>
                                </td>
                                <td>{{ formatValue($machine->margin ?? 0) }}%</td>
                                <td><strong>{{ formatValue($machine->profit ?? 0) }} FCFA</strong></td>
                                <td><strong>{{ formatValue($machine->selling_price ?? 0) }} FCFA</strong></td>
                                <td><strong>{{ formatValue($machine->selling_price_ttc ?? 0) }} FCFA</strong></td>
                                <td>{{ $machine->date_profitability ? $machine->date_profitability->format('d/m/Y') : '-' }}</td>
                                <td>
                                    <div class="table-actions">
                                        <a href="{{ route('machineDetails', $machine->id) }}"
                                           title="Détails" class="action-btn"
                                           style="background-color: rgb(140, 142, 151);">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>

                                        <a href="{{ route('machineEdit', $machine->id) }}"
                                           title="Modifier" class="action-btn"
                                           style="background-color: rgb(109, 103, 97);">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>

                                        <form action="{{ route('machineDelete', $machine->id) }}"
                                              method="POST" class="delete-form m-0"
                                              style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="action-btn"
                                                    title="Supprimer"
                                                    style="background-color: rgb(101, 77, 79);">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-white">
                                    Aucune rentabilité trouvée pour "{{ request('searchKey') }}".
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="d-flex justify-content-end mt-3">
                    {{ $machines->appends(request()->all())->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.delete-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Confirmer la suppression ?',
                text: 'Cette action est irréversible.',
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