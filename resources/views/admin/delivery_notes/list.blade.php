@extends('admin.layouts.master')

@section('content')
<style>
    /* Styles pour centrer le contenu des cellules et des en-têtes */
    table tbody tr td, table thead tr th {
        vertical-align: middle !important;
        text-align: center;
        color: white !important;
        border: 1px solid white !important;
    }
    /* Styles pour les actions avec Flexbox */
    .action-buttons {
        display: flex; 
        justify-content: center; 
        align-items: center; 
        gap: 5px; 
    }
    .action-buttons a, .action-buttons button {
        padding: 5px 8px;
        border: none;
        border-radius: 4px;
    }
    .badge {
        font-weight: 600;
        padding: 0.5em 0.7em;
    }
    /* ✅ Démarcation de la ligne au survol */
    table tbody tr:hover {
        background-color: #5a6268 !important; 
        cursor: pointer;
    }
</style>

<div class="container-fluid">
    <div class="card shadow mb-4">
        {{-- EN-TÊTE AVEC RECHERCHE ET BOUTONS --}}
        <div class="card-header py-3" style="background-color:#6c757d; border-bottom: 1px solid white;">
            <div class="d-flex justify-content-between align-items-center">
                
                {{-- Formulaire de recherche --}}
                <div class="flex-grow-1">
                    <form action="{{ route('delivery_notes.list') }}" method="GET" class="d-inline-block w-100"> 
                        <div class="input-group">
                            <input type="text" name="searchKey" class="form-control" 
                                    placeholder="Rechercher N° BL ou Client..." 
                                    value="{{ request('searchKey') }}" 
                                    style="max-width: 250px;">
                            <div class="input-group-append">
                                <button class="btn" type="submit" 
                                        style="background-color: rgb(249, 138, 27); color: white; border: none;">
                                    <i class="fa-solid fa-magnifying-glass"></i>
                                </button>
                                @if(request()->filled('searchKey'))
                                    <a href="{{ route('delivery_notes.list') }}" class="btn btn-secondary" title="Effacer la recherche">
                                        <i class="fa-solid fa-xmark"></i>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
                
                <div class="d-flex align-items-center">
                    {{-- BOUTON EXPORT EXCEL --}}
                    <a href="{{ route('delivery_notes.exportExcel', ['searchKey' => request('searchKey')]) }}" 
                       class="btn btn-light mr-3" style="font-weight: 500;">
                        <i class="fa-solid fa-file-excel text-success me-1"></i> Exporter en Excel
                    </a>

                    {{-- BOUTON CRÉER --}}
                    <a href="{{ route('delivery_notes.create') }}" class="btn btn-warning text-white fw-bold" 
                       style="background-color: rgb(249, 138, 27); border: none;">
                        <i class="fa-solid fa-plus"></i> Créer un Bordereau
                    </a>
                </div>

            </div>
        </div>

        {{-- Corps de la carte --}}
        <div class="card-body" style="background-color:#6c757d;">
            
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            
            <div class="table-responsive">
                <table class="table table-bordered text-white" width="100%" cellspacing="0" style="background-color:#6c757d; border: 1px solid white !important;">
                    <thead>
                        <tr class="bg-secondary text-white">
                            <th style="width: 17%;">BL N°</th>
                            <th style="width: 25%;">Client</th>
                            <th style="width: 15%;">BC N°</th>
                            <th style="width: 20%;">Facture Associée</th>
                            <th style="width: 12%;">Date de Livraison</th>
                            <th style="width: 11%;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($deliveryNotes as $bl) 
                        <tr>
                            <td>{{ $bl->delivery_note_number }}</td>
                            <td class="fw-bold"><strong>{{ $bl->client_name }}</strong></td>
                            <td>{{ $bl->purchase_order_number ?? 'N/A' }}</td>
                            <td>
                                @if ($bl->invoice)
                                    <a href="{{ route('invoices.details', $bl->invoice_id) }}" class="text-white" style="text-decoration: underline;">
                                        <span>{{ $bl->invoice->invoice_number }}</span>
                                    </a>
                                @else
                                    <span class="badge badge-secondary">Aucune</span>
                                @endif
                            </td>
                            <td>{{ $bl->date_delivery->format('d/m/Y') }}</td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{ route('delivery_notes.show', $bl->id) }}" class="btn" title="Voir/Imprimer BL"
                                        style="background-color: rgb(140, 142, 151); color: white;">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    <a href="{{ route('delivery_notes.edit', $bl->id) }}" class="btn" title="Modifier BL"
                                        style="background-color: rgb(109, 103, 97); color: white;">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <button class="btn delete-button" title="Supprimer BL"
                                            style="background-color: rgb(101, 77, 79); color: white;"
                                            data-url="{{ route('delivery_notes.delete', $bl->id) }}">
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
                
                @if(method_exists($deliveryNotes, 'links'))
                    <div class="d-flex justify-content-end mt-3">
                        {{ $deliveryNotes->appends(request()->input())->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.delete-button').forEach(button => {
            button.addEventListener('click', function() {
                let url = this.getAttribute('data-url');
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
                        let form = document.createElement('form');
                        form.action = url;
                        form.method = 'POST';
                        form.innerHTML = `
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <input type="hidden" name="_method" value="DELETE">
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
@endsection