@extends('admin.layouts.master')

@section('content')
<style>
    /* ✅ Alignement et bordures blanches */
    table tbody tr td, table thead tr th {
        vertical-align: middle !important;
        text-align: center !important;
        border: 1px solid white !important;
        white-space: nowrap;
        color: white !important;
    }

    /* ✅ Démarcation au survol */
    table tbody tr:hover {
        background-color: #5a6268 !important; 
        cursor: pointer;
    }

    table tbody tr td img {
        display: block;
        margin: 0 auto;
        width: 120px;
        height: 80px;
        object-fit: cover;
        border-radius: 8px;
        border: 1px solid rgba(255,255,255,0.5);
    }

    /* ✅ Actions bien rangées */
    .action-buttons {
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
        padding: 0;
        border-radius: 6px;
        color: white;
        border: none;
        transition: transform 0.2s;
    }

    .action-btn:hover {
        transform: scale(1.1);
        color: white;
    }
</style>

<div class="container-fluid">
    <div class="card shadow mb-4">
        {{-- En-tête avec Recherche, Exportation et Bouton Ajouter --}}
        <div class="card-header py-3" style="background-color:#6c757d; border-bottom: 1px solid white;">
            <div class="d-flex justify-content-between align-items-center">
                
                {{-- Barre de recherche avec bouton de suppression de filtre --}}
                <div class="flex-grow-1" style="max-width: 250px;">
                    <form action="{{ route('productList') }}" method="GET">
                        <div class="input-group">
                            <input type="text" name="searchKey" class="form-control"
                                   placeholder="Nom du produit ou référence..."
                                   value="{{ request('searchKey') }}">
                            <div class="input-group-append">
                                <button class="btn" type="submit"
                                        style="background-color: rgb(249, 138, 27); color: white; border: none;">
                                    <i class="fa-solid fa-magnifying-glass"></i>
                                </button>
                                {{-- Bouton de suppression de filtre --}}
                                @if(request()->filled('searchKey'))
                                    <a href="{{ route('productList') }}" class="btn btn-secondary" title="Effacer le filtre">
                                        <i class="fa-solid fa-xmark"></i>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>

                <div class="d-flex align-items-center">
                    {{-- Bouton Exportation Excel Direct --}}
                    <a href="{{ route('productExportExcel', ['searchKey' => request('searchKey')]) }}" 
                       class="btn btn-light mr-3" 
                       style="font-weight: 500;">
                        <i class="fa-solid fa-file-excel text-success me-1"></i> Exporter en Excel
                    </a>

                    <a href="{{ route('productCreate') }}"
                       class="btn btn-warning text-white fw-bold shadow-sm"
                       style="background-color: rgb(249, 138, 27); border: none;">
                        <i class="fa-solid fa-plus"></i> Ajouter un produit
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
                <table class="table table-bordered text-white" width="100%" style="background-color:#6c757d; border: 1px solid white !important;">
                    <thead>
                        <tr class="bg-secondary text-white">
                            <th>Référence</th>
                            <th>Nom du produit</th>
                            <th>Marque</th>
                            <th>État</th>
                            <th>Image</th>
                            <th>Prix de vente</th>
                            <th>Quantité</th>
                            <th>Date</th>
                            <th style="width: 140px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($products as $item)
                        <tr>
                            <td>{{ $item->reference }}</td>
                            <td class="fw-bold"><strong>{{ $item->name }}</strong></td>
                            <td>{{ $item->brand }}</td>
                            <td>{{ $item->status }}</td>
                            <td>
                                <img src="{{ asset('purchaseImages/' . $item->image) }}" alt="Image produit">
                            </td>
                            {{-- Prix en gras --}}
                            <td class="fw-bold"><strong>{{ number_format($item->price, 0, ',', ' ') }} FCFA</strong></td>
                            <td>{{ $item->count }}</td>
                            {{-- Affichage de la date --}}
                            <td>{{ $item->date_product ? $item->date_product->format('d/m/Y') : '-' }}</td>
                            <td>
                                <div class="action-buttons">
                                    {{-- Voir --}}
                                    <a href="{{ route('productDetails', $item->id) }}"
                                       class="action-btn" title="Détails"
                                       style="background-color: rgb(140, 142, 151);">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>

                                    {{-- Modifier --}}
                                    <a href="{{ route('productEdit', $item->id) }}"
                                       class="action-btn" title="Modifier"
                                       style="background-color: rgb(109, 103, 97);">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>

                                    {{-- Supprimer --}}
                                    <button class="action-btn delete-button" title="Supprimer"
                                            style="background-color: rgb(101, 77, 79);"
                                            data-url="{{ route('productDelete', $item->id) }}">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-4">
                                Aucun produit trouvé pour "{{ request('searchKey') }}".
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

                {{-- Pagination avec conservation des filtres --}}
                <div class="d-flex justify-content-end mt-3">
                    {{ $products->appends(request()->all())->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    // Gestion de la suppression avec SweetAlert2
    document.querySelectorAll('.delete-button').forEach(button => {
        button.addEventListener('click', function () {
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
                    window.location.href = url;
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
@endsection