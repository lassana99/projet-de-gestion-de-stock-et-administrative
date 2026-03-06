@extends('admin.layouts.master')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        {{-- En-tête : Recherche, Export et Ajout --}}
        <div class="card-header py-3" style="background-color:#6c757d; border-bottom: 1px solid white;">
            <div class="d-flex justify-content-between align-items-center">
                <form action="{{ route('utilityList') }}" method="GET" class="d-flex" style="max-width: 450px;">
                    <div class="input-group">
                        <input type="text" name="searchKey" class="form-control" placeholder="Numéro, Mois, Description..." value="{{ request('searchKey') }}">
                        <div class="input-group-append">
                            <button class="btn" type="submit" style="background-color: rgb(249, 138, 27); color: white; border: none;"><i class="fa-solid fa-magnifying-glass"></i></button>
                            @if(request()->filled('searchKey'))
                                <a href="{{ route('utilityList') }}" class="btn btn-secondary" title="Effacer"><i class="fa-solid fa-xmark"></i></a>
                            @endif
                        </div>
                    </div>
                </form>
                <div class="d-flex align-items-center">
                    <a href="{{ route('utilityExportExcel', ['searchKey' => request('searchKey')]) }}" class="btn btn-light mr-3" style="font-weight: 500;">
                        <i class="fa-solid fa-file-excel text-success me-1"></i> Exporter en Excel
                    </a>
                    <a href="{{ route('utilityCreate') }}" class="btn text-white fw-bold shadow-sm" style="background-color: rgb(249, 138, 27); border: none;">
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
                    #dataTableUtil { border: 1px solid white !important; border-collapse: collapse; }
                    #dataTableUtil thead th, #dataTableUtil tbody td { 
                        text-align: center !important; 
                        vertical-align: middle !important; 
                        white-space: nowrap; 
                        color: white !important; 
                        border: 1px solid white !important; 
                    }
                    #dataTableUtil tbody tr:hover { background-color: #5a6268 !important; }
                    .action-btn { width: 34px; height: 34px; display: flex; align-items: center; justify-content: center; border-radius: 6px; border: none; color: white !important; transition: 0.2s; }
                    .action-btn:hover { transform: scale(1.1); }
                </style>

                <table id="dataTableUtil" class="table table-bordered" width="100%">
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
                        @forelse($utilities as $util)
                            <tr>
                                <td class="fw-bold">{{ $util->number }}</td>
                                <td>{{ $util->month }}</td>
                                <td>{{ $util->displayDescription() }}</td>
                                <td>{{ $util->reference ?? '-' }}</td>
                                <td>{{ $util->issue_date->format('d/m/Y') }}</td>
                                <td class="fw-bold text-warning">{{ $util->formattedAmount() }}</td>
                                <td>
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="{{ route('utilityDetails', $util->id) }}" class="action-btn" title="Voir" style="background-color: rgb(140, 142, 151);">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>
                                        <a href="{{ route('utilityEdit', $util->id) }}" class="action-btn" title="Modifier" style="background-color: rgb(109, 103, 97);">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>
                                        <form action="{{ route('utilityDelete', $util->id) }}" method="POST" class="delete-form m-0">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="action-btn" title="Supprimer" style="background-color: rgb(101, 77, 79);">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center py-4 text-white-50">Aucun enregistrement trouvé.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="d-flex justify-content-end mt-3">{{ $utilities->appends(request()->all())->links() }}</div>
            </div>
        </div>
    </div>
</div>

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
                confirmButtonText: 'Oui, supprimer'
            }).then((result) => { if (result.isConfirmed) form.submit(); });
        });
    });
</script>
@endsection