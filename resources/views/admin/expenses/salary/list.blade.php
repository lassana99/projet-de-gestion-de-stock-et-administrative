@extends('admin.layouts.master')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        {{-- En-tête --}}
        <div class="card-header py-3" style="background-color:#6c757d; border-bottom: 1px solid white;">
            <div class="d-flex justify-content-between align-items-center">
                <form action="{{ route('salaryList') }}" method="GET" class="d-flex" style="max-width: 450px;">
                    <div class="input-group">
                        <input type="text" name="searchKey" class="form-control" placeholder="Rechercher Nom, N°, Fonction..." value="{{ request('searchKey') }}">
                        <div class="input-group-append">
                            <button class="btn" type="submit" style="background-color: rgb(249, 138, 27); color: white; border: none;"><i class="fa-solid fa-magnifying-glass"></i></button>
                            @if(request()->filled('searchKey'))
                                <a href="{{ route('salaryList') }}" class="btn btn-secondary" title="Effacer"><i class="fa-solid fa-xmark"></i></a>
                            @endif
                        </div>
                    </div>
                </form>
                <div class="d-flex align-items-center">
                    <a href="{{ route('salaryExportExcel', ['searchKey' => request('searchKey')]) }}" class="btn btn-light mr-3" style="font-weight: 500;">
                        <i class="fa-solid fa-file-excel text-success me-1"></i> Exporter
                    </a>
                    <a href="{{ route('salaryCreate') }}" class="btn text-white fw-bold shadow-sm" style="background-color: rgb(249, 138, 27); border: none;">
                        <i class="fa-solid fa-plus me-1"></i> Nouveau
                    </a>
                </div>
            </div>
        </div>

        {{-- Corps --}}
        <div class="card-body" style="background-color:#6c757d;">
            @if(session('success')) <div class="alert alert-success border-0 shadow-sm">{{ session('success') }}</div> @endif
            
            <div class="table-responsive">
                <style>
                    #dataTableSal { border: 1px solid white !important; border-collapse: collapse; }
                    #dataTableSal thead th, #dataTableSal tbody td { text-align: center !important; vertical-align: middle !important; white-space: nowrap; color: white !important; border: 1px solid white !important; }
                    #dataTableSal tbody tr:hover { background-color: #5a6268 !important; cursor: pointer; }
                    .action-btn { width: 34px; height: 34px; display: flex; align-items: center; justify-content: center; border-radius: 6px; border: none; color: white !important; transition: 0.2s; }
                    .action-btn:hover { transform: scale(1.1); }
                </style>

                <table id="dataTableSal" class="table table-bordered" width="100%">
                    <thead class="bg-secondary text-white">
                        <tr>
                            <th>Numéro</th>
                            <th>Date</th>
                            <th>Bénéficiaire</th>
                            <th>Fonction</th>
                            <th>Montant (FCFA)</th>
                            <th style="width: 140px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($salaries as $sal)
                            <tr>
                                <td class="fw-bold">{{ $sal->number }}</td>
                                <td>{{ $sal->payment_date->format('d/m/Y') }}</td>
                                <td class="text-left px-3">{{ $sal->full_name }}</td>
                                <td>{{ $sal->position }}</td>
                                <td class="fw-bold" style="color: #ffc107;">{{ $sal->formattedAmount() }}</td>
                                <td>
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="{{ route('salaryDetails', $sal->id) }}" class="action-btn" title="Voir" style="background-color: rgb(140, 142, 151);"><i class="fa-solid fa-eye"></i></a>
                                        <a href="{{ route('salaryEdit', $sal->id) }}" class="action-btn" title="Modifier" style="background-color: rgb(109, 103, 97);"><i class="fa-solid fa-pen-to-square"></i></a>
                                        <form action="{{ route('salaryDelete', $sal->id) }}" method="POST" class="delete-form m-0">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="action-btn" title="Supprimer" style="background-color: rgb(101, 77, 79);"><i class="fa-solid fa-trash-can"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center py-5 text-white-50">Aucun salaire enregistré.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="d-flex justify-content-end mt-3">{{ $salaries->appends(request()->all())->links() }}</div>
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
                title: 'Supprimer ?', text: "Cette action est irréversible.", icon: 'warning',
                showCancelButton: true, confirmButtonColor: '#90908c', cancelButtonColor: '#d33', confirmButtonText: 'Oui, supprimer', cancelButtonText: 'Annuler'
            }).then((result) => { if (result.isConfirmed) form.submit(); });
        });
    });
</script>
@endsection