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

    .table-actions .btn {
        width: 36px;
        height: 36px;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
        border: none;
    }

    .badge {
        font-weight: 600;
        border-radius: 6px;
        padding: 0.35rem 0.75rem;
    }
</style>

<div class="container-fluid">
    <div class="card shadow mb-4">

        {{-- Header avec barre de recherche, Export et bouton Créer --}}
        <div class="card-header py-3" style="background-color:#6c757d; border-bottom: 1px solid white;">
            <div class="d-flex justify-content-between align-items-center">

                {{-- Barre de recherche avec bouton de suppression de filtre --}}
                <form action="{{ route('invoices.list') }}" method="GET" class="d-flex" style="max-width: 450px;">
                    <div class="input-group">
                        <input type="text" name="searchKey"
                               class="form-control"
                               placeholder="Rechercher N° Facture ou Client..."
                               value="{{ request('searchKey') }}">
                        <div class="input-group-append">
                            <button class="btn" type="submit" style="background-color: rgb(249,138,27); color:white; border: none;">
                                <i class="fa-solid fa-magnifying-glass"></i>
                            </button>
                            @if(request()->filled('searchKey'))
                                <a href="{{ route('invoices.list') }}" class="btn btn-secondary" title="Effacer le filtre" style="border: none;">
                                    <i class="fa-solid fa-xmark"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                </form>

                <div class="d-flex align-items-center">
                    {{-- Bouton Exportation Excel Direct --}}
                    <a href="{{ route('invoices.exportExcel', ['searchKey' => request('searchKey')]) }}" 
                       class="btn btn-light mr-3" 
                       style="font-weight: 500;">
                        <i class="fa-solid fa-file-excel text-success me-1"></i> Exporter en Excel
                    </a>

                    <a href="{{ route('invoices.create') }}"
                       class="btn btn-warning text-white fw-bold shadow-sm"
                       style="background-color: rgb(249,138,27); border: none;">
                        <i class="fa-solid fa-plus"></i> Créer une facture
                    </a>
                </div>
            </div>
        </div>

        {{-- Body --}}
        <div class="card-body" style="background-color:#6c757d;">

            @foreach (['success','error','info'] as $msg)
                @if(session($msg))
                    <div class="alert alert-{{ $msg }}">{{ session($msg) }}</div>
                @endif
            @endforeach

            <div class="table-responsive">
                <table class="table table-bordered text-white" id="dataTable" width="100%" cellspacing="0" style="background-color:#6c757d; border: 1px solid white !important;">
                    <thead>
                        <tr class="bg-secondary text-white">
                            <th>N° Facture</th>
                            <th>Client</th>
                            <th>Total HTVA</th>
                            <th>Total TTC</th>
                            <th>Date</th>
                            <th>État</th>
                            <th style="min-width:140px;">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($invoices as $i)
                        <tr>
                            <td>{{ $i->invoice_number ?? 'N/A' }}</td>
                            <td class="fw-bold"><strong>{{ $i->client }}</strong></td>
                            <td><strong>{{ number_format($i->total_htva ?? 0, 0, ',', ' ') }} FCFA</strong></td>
                            <td class="fw-bold"><strong>{{ number_format($i->total_ttc ?? 0, 0, ',', ' ') }} FCFA</strong></td>
                            <td>{{ \Carbon\Carbon::parse($i->date_invoice)->format('d/m/Y') }}</td>
                            <td>
                                @php
                                    $rawStatus = strtolower(trim($i->status ?? ''));
                                    $normalized = str_replace('_', ' ', $rawStatus);

                                    $map = [
                                        'pending'            => ['En attente', 'bg-warning text-dark'],
                                        'sent'               => ['Envoyée', 'bg-info text-white'],
                                        'paid'               => ['Payée', 'bg-success text-white'],
                                        'partial'            => ['Payée partiellement', 'bg-primary text-white'],
                                        'partially paid'     => ['Payée partiellement', 'bg-primary text-white'],
                                        'partially_paid'     => ['Payée partiellement', 'bg-primary text-white'],
                                        'cancelled'          => ['Annulée', 'bg-danger text-white'],
                                        'canceled'           => ['Annulée', 'bg-danger text-white'],
                                        'draft'              => ['Brouillon', 'bg-secondary text-white'],
                                        'overdue'            => ['En retard', 'bg-danger text-white'],
                                    ];

                                    if (array_key_exists($normalized, $map)) {
                                        [$label, $class] = $map[$normalized];
                                    } elseif (array_key_exists($rawStatus, $map)) {
                                        [$label, $class] = $map[$rawStatus];
                                    } else {
                                        $label = ucfirst(str_replace('_', ' ', $rawStatus ?: 'Inconnu'));
                                        $class = 'bg-dark text-white';
                                    }
                                @endphp

                                <span class="badge {{ $class }} p-2">
                                    {{ $label }}
                                </span>
                            </td>

                            <td>
                                <div class="table-actions">
                                    <a href="{{ route('invoices.details', $i->id) }}"
                                       class="btn" title="Voir"
                                       style="background-color: rgb(140, 142, 151); color:white;">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>

                                    <a href="{{ route('invoices.edit', $i->id) }}"
                                       class="btn" title="Modifier"
                                       style="background-color: rgb(109, 103, 97); color:white;">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>

                                    <button class="btn delete-button" title="Supprimer"
                                            data-url="{{ route('invoices.delete', $i->id) }}"
                                            style="background-color: rgb(101, 77, 79); color:white;">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-white py-4">
                                Aucun résultat trouvé pour "{{ request('searchKey') }}".
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="d-flex justify-content-end mt-3">
                    {{ $invoices->appends(request()->all())->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

{{-- SweetAlert & JS --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.delete-button').forEach(btn => {
        btn.addEventListener('click', () => {
            Swal.fire({
                title: 'Confirmer la suppression ?',
                text: "Cette action est irréversible.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Oui, supprimer',
                cancelButtonText: 'Annuler',
                confirmButtonColor: '#90908c',
                cancelButtonColor: '#d33'
            }).then(r => {
                if (r.isConfirmed) {
                    let f = document.createElement('form');
                    f.method = 'POST';
                    f.action = btn.dataset.url;
                    f.innerHTML = `
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="_method" value="DELETE">
                    `;
                    document.body.appendChild(f);
                    f.submit();
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