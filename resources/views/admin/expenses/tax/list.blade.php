@extends('admin.layouts.master')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3" style="background-color:#6c757d; border-bottom: 1px solid white;">
            <div class="d-flex justify-content-between align-items-center">
                {{-- Barre de recherche --}}
                <form action="{{ route('taxList') }}" method="GET" class="d-flex" style="max-width: 450px;">
                    <div class="input-group">
                        <input type="text" name="searchKey" class="form-control" placeholder="Numéro, Mois, Description..." value="{{ request('searchKey') }}">
                        <div class="input-group-append">
                            <button class="btn" type="submit" style="background-color: rgb(249, 138, 27); color: white;"><i class="fa-solid fa-magnifying-glass"></i></button>
                            @if(request()->filled('searchKey'))
                                <a href="{{ route('taxList') }}" class="btn btn-secondary"><i class="fa-solid fa-xmark"></i></a>
                            @endif
                        </div>
                    </div>
                </form>

                {{-- Groupe d'actions --}}
                <div class="d-flex align-items-center">
                    {{-- CORRECTION ICI : Utilisation de taxExportExcel au lieu de rentExportExcel --}}
                    <a href="{{ route('taxExportExcel', ['searchKey' => request('searchKey')]) }}" 
                       class="btn btn-light mr-3" 
                       style="font-weight: 500;">
                        <i class="fa-solid fa-file-excel text-success me-1"></i> Exporter en Excel
                    </a>

                    <a href="{{ route('taxCreate') }}" class="btn text-white" style="background-color: rgb(249, 138, 27);">
                        <i class="fa-solid fa-plus me-1"></i> Nouveau Impôt
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
                    #dataTableTax { border: 1px solid white !important; border-collapse: collapse; }
                    #dataTableTax thead th, #dataTableTax tbody td { 
                        text-align: center !important; 
                        vertical-align: middle !important; 
                        white-space: nowrap; 
                        color: white !important; 
                        border: 1px solid white !important; 
                    }
                    #dataTableTax tbody tr:hover { background-color: #5a6268 !important; }
                    .action-btn { width: 34px; height: 34px; display: flex; align-items: center; justify-content: center; border-radius: 6px; border: none; color: white !important; transition: 0.2s; }
                    .action-btn:hover { transform: scale(1.1); }
                </style>

                <table id="dataTableTax" class="table table-bordered" width="100%">
                    <thead>
                        <tr class="bg-secondary text-white">
                            <th>Numéro</th>
                            <th>Mois</th>
                            <th>Description</th>
                            <th>Référence</th>
                            <th>Date émission</th>
                            <th>Montant (FCFA)</th>
                            <th style="width: 140px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($taxes as $tax)
                            <tr>
                                <td class="fw-bold">{{ $tax->number }}</td>
                                <td>{{ $tax->month }}</td>
                                <td>{{ $tax->description === 'Autres' ? $tax->description_other : $tax->description }}</td>
                                <td>{{ $tax->reference ?? '-' }}</td>
                                <td>{{ $tax->issue_date->format('d/m/Y') }}</td>
                                <td class="fw-bold text-warning">{{ number_format($tax->amount_fcfa, 0, ',', ' ') }}</td>
                                <td>
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="{{ route('taxDetails', $tax->id) }}" class="action-btn" title="Voir" style="background-color: rgb(140, 142, 151);">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>
                                        <a href="{{ route('taxEdit', $tax->id) }}" class="action-btn" title="Modifier" style="background-color: rgb(109, 103, 97);">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>
                                        <form action="{{ route('taxDelete', $tax->id) }}" method="POST" class="delete-form m-0">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="action-btn" title="Supprimer" style="background-color: rgb(101, 77, 79);">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center py-4">Aucun impôt trouvé pour "{{ request('searchKey') }}".</td></tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="d-flex justify-content-end mt-3">
                    {{ $taxes->appends(request()->all())->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Script SweetAlert identique --}}
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.querySelectorAll('.delete-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Confirmer la suppression', 
                text: "Cette action est irréversible.", 
                icon: 'warning',
                showCancelButton: true, 
                confirmButtonColor: '#90908c', 
                cancelButtonColor: '#d33', 
                confirmButtonText: 'Oui, supprimer',
                cancelButtonText: 'Annuler'
            }).then((result) => { if (result.isConfirmed) form.submit(); });
        });
    });

    // Auto-fermeture alertes
    setTimeout(() => {
        document.querySelectorAll('.alert').forEach(alert => {
            alert.style.transition = 'opacity 0.5s ease';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        });
    }, 5000);
</script>
@endsection