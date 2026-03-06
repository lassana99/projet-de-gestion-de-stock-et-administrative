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
        {{-- En-tête avec Recherche, Exportation et Bouton Ajouter --}}
        <div class="card-header py-3" style="background-color:#6c757d;">
            <div class="d-flex justify-content-between align-items-center">
                
                {{-- Barre de recherche avec bouton de suppression de filtre --}}
                <form action="{{ route('purchaseList') }}" method="GET" class="d-flex" style="max-width: 450px;">
                    <div class="input-group">
                        <input type="text" name="searchKey" class="form-control"
                               placeholder="Référence, produit..."
                               value="{{ request('searchKey') }}">
                        <div class="input-group-append">
                            {{-- Bouton Rechercher --}}
                            <button class="btn" type="submit"
                                    style="background-color: rgb(249, 138, 27); color: white; border: none;">
                                <i class="fa-solid fa-magnifying-glass"></i>
                            </button>
                            
                            {{-- Bouton de suppression de filtre (X) --}}
                            @if(request()->filled('searchKey'))
                                <a href="{{ route('purchaseList') }}" class="btn btn-secondary" title="Effacer le filtre" style="border: none;">
                                    <i class="fa-solid fa-xmark"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                </form>

                <div class="d-flex align-items-center">
                    {{-- Bouton Exportation Excel Direct --}}
                    <a href="{{ route('purchaseExportExcel', ['searchKey' => request('searchKey')]) }}" 
                       class="btn btn-light mr-3" 
                       style="font-weight: 500;">
                        <i class="fa-solid fa-file-excel text-success me-1"></i> Exporter en Excel
                    </a>

                    {{-- Bouton Ajouter --}}
                    <a href="{{ route('purchaseCreate') }}" class="btn text-white"
                       style="background-color: rgb(249, 138, 27);">
                        <i class="fa-solid fa-plus me-1"></i> Ajouter un achat / stock
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
                    /* Force les bordures du tableau en blanc */
                    #dataTable {
                        border: 1px solid white !important;
                        border-collapse: collapse;
                    }

                    #dataTable thead th, 
                    #dataTable tbody td {
                        text-align: center !important;
                        vertical-align: middle !important;
                        white-space: nowrap;
                        color: white !important;
                        border: 1px solid white !important;
                    }

                    #dataTable tbody tr:hover,
                    #dataTable tbody tr:hover > td {
                        background-color: #5a6268 !important;
                        color: white !important;
                        cursor: pointer;
                    }

                    /* ===== Actions ===== */
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
                        transition: 0.2s ease-in-out;
                    }

                    .action-btn i {
                        font-size: 14px;
                        color: #ffffff;
                    }

                    .action-btn:hover {
                        opacity: 0.85;
                        transform: scale(1.05);
                    }
                </style>

                <table id="dataTable" class="table table-bordered table-hover"
                       width="100%" cellspacing="0" style="background-color:#343a40;">
                    <thead>
                        <tr class="bg-secondary text-white">
                            <th>Référence</th>
                            <th>Produit</th>
                            <th>Pays</th>
                            <th>Incoterm</th>
                            <th>Prix d'achat unité</th>                          
                            <th>Prix d'achat (FCFA)</th>
                            <th>Quantité</th>
                            <th>Date d'achat</th>
                            <th style="width: 150px;">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($purchases as $purchase)
                            <tr style="background-color:#6c757d;">
                                <td>{{ $purchase->reference }}</td>
                                <td>{{ $purchase->purchasename }}</td>
                                <td>{{ $purchase->country ?? '-' }}</td>
                                <td>{{ $purchase->incoterm ?? '-' }}</td>
                                
                                {{-- Prix Unité Original en Gras --}}
                                <td><strong>{{ formatValue($purchase->purchaseprice) }} {{ $purchase->currency ?? 'FCFA' }}</strong></td>
                                
                                {{-- Prix en FCFA en Gras --}}
                                <td class="fw-bold">
                                    <strong>{{ formatValue($purchase->purchase_price_fcfa) }} FCFA</strong>
                                </td>
                                
                                <td>{{ formatValue($purchase->quantity) }}</td>
                                {{-- Affichage de la date --}}
                                <td>{{ $purchase->date_purchase ? $purchase->date_purchase->format('d/m/Y') : '-' }}</td>
                                <td>
                                    <div class="table-actions">
                                        {{-- Voir --}}
                                        <a href="{{ route('purchaseDetails', $purchase->id) }}"
                                           class="action-btn"
                                           title="Voir"
                                           style="background-color: rgb(140, 142, 151);">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>

                                        {{-- Modifier --}}
                                        <a href="{{ route('purchaseEdit', $purchase->id) }}"
                                           class="action-btn"
                                           title="Modifier"
                                           style="background-color: rgb(109, 103, 97);">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>

                                        {{-- Supprimer --}}
                                        <form action="{{ route('purchaseDelete', $purchase->id) }}"
                                              method="POST" class="delete-form m-0">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="action-btn"
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
                                <td colspan="9" class="text-center text-white"
                                    style="background-color:#6c757d;">
                                    Aucun résultat trouvé pour "{{ request('searchKey') }}".
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                {{-- Pagination avec conservation des filtres --}}
                <div class="d-flex justify-content-end mt-3">
                    {{ $purchases->appends(request()->all())->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Scripts JS --}}
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Confirmation de suppression avec SweetAlert2
    document.querySelectorAll('.delete-form').forEach(form => {
        form.addEventListener('submit', function(event) {
            event.preventDefault();
            Swal.fire({
                title: 'Confirmer la suppression',
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

    // Disparition automatique des alertes après 5 secondes
    setTimeout(() => {
        document.querySelectorAll('.alert').forEach(alert => {
            alert.style.transition = 'opacity 0.5s ease';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        });
    }, 5000);
</script>
@endsection