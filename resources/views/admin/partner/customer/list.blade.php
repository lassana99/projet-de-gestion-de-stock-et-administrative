@extends('admin.layouts.master')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        {{-- En-tête avec Recherche, Export et Bouton Ajouter --}}
        <div class="card-header py-3" style="background-color:#6c757d; border-bottom: 1px solid white;">
            <div class="d-flex justify-content-between align-items-center">
                
                {{-- Barre de recherche adaptée avec bouton de suppression de filtre --}}
                <div class="flex-grow-1" style="max-width: 250px;">
                    <form action="{{ route('customerList') }}" method="GET">
                        <div class="input-group">
                            <input type="text" name="searchKey" class="form-control"
                                placeholder="Nom du client..." value="{{ request('searchKey') }}">
                            <div class="input-group-append">
                                <button class="btn" type="submit"
                                    style="background-color: rgb(249, 138, 27); color: white; border: none;">
                                    <i class="fa-solid fa-magnifying-glass"></i>
                                </button>
                                {{-- Bouton de suppression de filtre (X) --}}
                                @if(request()->filled('searchKey'))
                                    <a href="{{ route('customerList') }}" class="btn btn-secondary" title="Effacer le filtre" style="border: none;">
                                        <i class="fa-solid fa-xmark"></i>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>

                <div class="d-flex align-items-center">
                    {{-- BOUTON EXPORT EXCEL --}}
                    <a href="{{ route('customerExportExcel', ['searchKey' => request('searchKey')]) }}" 
                       class="btn btn-light mr-3" style="font-weight: 500;">
                        <i class="fa-solid fa-file-excel text-success me-1"></i> Exporter en Excel
                    </a>

                    {{-- BOUTON AJOUTER --}}
                    <a href="{{ route('customerCreate') }}" class="btn btn-warning text-white fw-bold" 
                       style="background-color: rgb(249,138,27); border:none;">
                        <i class="fa-solid fa-plus"></i> Ajouter un client
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
                <style>
                    /* ✅ Alignement et bordures blanches */
                    #dataTable {
                        border: 1px solid white !important;
                        border-collapse: collapse;
                    }

                    #dataTable thead th, 
                    #dataTable tbody td {
                        white-space: nowrap;
                        text-align: center !important;
                        vertical-align: middle !important;
                        color: white !important;
                        border: 1px solid white !important;
                    }

                    /* ✅ Démarcation de la ligne au survol */
                    #dataTable tbody tr:hover {
                        background-color: #5a6268 !important; 
                        cursor: pointer;
                    }

                    .table-actions {
                        display: flex;
                        justify-content: center;
                        align-items: center;
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

                    .contact-badge {
                        font-size: 0.75rem;
                        padding: 2px 6px;
                        border-radius: 10px;
                        background-color: #17a2b8;
                        color: white;
                        margin-left: 5px;
                        font-weight: bold;
                    }
                </style>

                <table class="table table-bordered text-white" id="dataTable" width="100%" cellspacing="0" style="background-color:#6c757d;">
                    <thead>
                        <tr class="bg-secondary text-white">
                            <th style="width: 120px;">Code client</th>
                            <th style="width: 200px;">Nom du client</th>
                            <th style="width: 180px;">Personne à contacter</th>
                            <th style="width: 130px;">Téléphone</th>
                            <th style="width: 110px;">Date</th>
                            <th style="width: 140px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($customers as $customer)
                        <tr>
                            <td>{{ $customer->code_client }}</td>
                            <td class="fw-bold"><strong>{{ $customer->name }}</strong></td>
                            
                            {{-- Affichage du premier contact + badge si plusieurs --}}
                            <td>
                                @if($customer->contacts->isNotEmpty())
                                    {{ $customer->contacts->first()->name }}
                                    @if($customer->contacts->count() > 1)
                                        <span class="contact-badge" title="Et {{ $customer->contacts->count() - 1 }} autre(s) contact(s)">
                                            +{{ $customer->contacts->count() - 1 }}
                                        </span>
                                    @endif
                                @else
                                    <span class="text-white-50 small">Aucun contact</span>
                                @endif
                            </td>

                            {{-- Téléphone du premier contact --}}
                            <td>
                                @if($customer->contacts->isNotEmpty())
                                    {{ $customer->contacts->first()->phone ?? '-' }}
                                @else
                                    -
                                @endif
                            </td>

                            <td>{{ $customer->date ? $customer->date->format('d/m/Y') : '-' }}</td>
                            
                            <td>
                                <div class="table-actions">
                                    {{-- Voir --}}
                                    <a href="{{ route('customerDetails', $customer->id) }}" title="Voir"
                                        class="action-btn" style="background-color: rgb(140, 142, 151);">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    {{-- Éditer --}}
                                    <a href="{{ route('customerEdit', $customer->id) }}" title="Éditer"
                                        class="action-btn" style="background-color: rgb(109, 103, 97);">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    {{-- Supprimer --}}
                                    <form action="{{ route('customerDelete', $customer->id) }}" method="POST"
                                        style="margin: 0;" id="delete-form-{{ $customer->id }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button"
                                            class="action-btn delete-button"
                                            title="Supprimer"
                                            style="background-color: rgb(101, 77, 79);"
                                            data-id="{{ $customer->id }}"
                                            data-name="{{ $customer->name }}">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
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

                {{-- Pagination avec conservation des filtres --}}
                <div class="d-flex justify-content-end mt-3">
                    {{ $customers->appends(request()->all())->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Scripts JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Gestion de la suppression avec SweetAlert2
        document.querySelectorAll('.delete-button').forEach(button => {
            button.addEventListener('click', (e) => {
                const customerName = button.dataset.name;
                const formId = `delete-form-${button.dataset.id}`;
                Swal.fire({
                    title: 'Confirmer la suppression',
                    text: `Voulez-vous vraiment supprimer le client "${customerName}" ?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#90908c',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Oui, supprimer',
                    cancelButtonText: 'Annuler'
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById(formId).submit();
                    }
                });
            });
        });

        // Auto-suppression des alertes après 5 secondes
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