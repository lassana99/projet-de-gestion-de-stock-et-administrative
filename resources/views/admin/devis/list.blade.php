@extends('admin.layouts.master')

@section('content')
<style>
    /* ✅ Alignement et bordures blanches */
    table tbody tr td,
    table thead tr th {
        vertical-align: middle !important;
        text-align: center;
        border: 1px solid white !important; /* Traits blancs */
        color: white !important;
    }

    /* ✅ Démarcation de la ligne au survol */
    table tbody tr:hover {
        background-color: #5a6268 !important; 
        cursor: pointer;
    }

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

    .bg-success-darker {
        background-color: #0ca25c; /* vert foncé */
        color: white;
    }

    .bg-abandoned {
        background-color: #fd7e14; /* orange foncé pour Abandonné */
        color: white;
    }

    /* Autres statuts */
    .bg-warning { background-color: #ffc107; color: #212529; } /* En attente */
    .bg-info { background-color: #17a2b8; color: white; } /* Envoyé */
    .bg-success { background-color: #28a745; color: white; } /* Accepté */
    .bg-danger { background-color: #dc3545; color: white; } /* Annulé */
    .bg-secondary { background-color: #6c757d; color: white; } /* Brouillon */
</style>

<div class="container-fluid">
    <div class="card shadow mb-4">

        {{-- Header avec barre de recherche, Export et bouton Créer --}}
        <div class="card-header py-3" style="background-color:#6c757d; border-bottom: 1px solid white;">
            <div class="d-flex justify-content-between align-items-center">
                
                {{-- Barre de recherche avec bouton de suppression de filtre --}}
                <form action="{{ route('devis.list') }}" method="GET" class="d-flex" style="max-width: 450px;">
                    <div class="input-group">
                        <input type="text" name="searchKey" class="form-control"
                               placeholder="Rechercher N° Devis ou Client..."
                               value="{{ request('searchKey') }}">
                        <div class="input-group-append">
                            <button class="btn" type="submit"
                                    style="background-color: rgb(249,138,27); color:white; border:none;">
                                <i class="fa-solid fa-magnifying-glass"></i>
                            </button>
                            @if(request()->filled('searchKey'))
                                <a href="{{ route('devis.list') }}" class="btn btn-secondary" title="Effacer le filtre" style="border: none;">
                                    <i class="fa-solid fa-xmark"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                </form>

                <div class="d-flex align-items-center">
                    {{-- Bouton Exportation Excel Direct --}}
                    <a href="{{ route('devis.exportExcel', ['searchKey' => request('searchKey')]) }}" 
                       class="btn btn-light mr-3" 
                       style="font-weight: 500;">
                        <i class="fa-solid fa-file-excel text-success me-1"></i> Exporter en Excel
                    </a>

                    <a href="{{ route('devis.create') }}"
                       class="btn btn-warning text-white fw-bold"
                       style="background-color: rgb(249,138,27); border:none;">
                        <i class="fa-solid fa-plus"></i> Créer un devis
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
                <table class="table table-bordered text-white" style="background-color:#6c757d; border: 1px solid white !important;">
                    <thead>
                        <tr class="bg-secondary text-white">
                            <th>N° Devis</th>
                            <th>Client</th>
                            <th>Total HTVA</th>
                            <th>Total TTC</th>
                            <th>Date</th>
                            <th>État</th>
                            <th style="min-width:140px;">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($devis as $d)
                        <tr>
                            <td>{{ $d->devis_number ?? 'N/A' }}</td>
                            <td class="fw-bold"><strong>{{ $d->client }}</strong></td>

                            <td>
                                <strong>{{ number_format($d->total_htva ?? 0, 0, ',', ' ') }} FCFA</strong>
                            </td>

                            <td class="fw-bold"><strong>{{ number_format($d->total_ttc ?? 0, 0, ',', ' ') }} FCFA</strong></td>
                            <td>{{ \Carbon\Carbon::parse($d->date_devis)->format('d/m/Y') }}</td>

                            {{-- Statut --}}
                            <td>
                                @php
                                    $status = strtolower($d->status ?? 'sent');

                                    $map = [
                                        'pending'   => ['En attente', 'bg-warning'],
                                        'sent'      => ['Envoyé', 'bg-info'],
                                        'accepted'  => ['Accepté', 'bg-success'],
                                        'rejected'  => ['Annulé', 'bg-danger'],
                                        'draft'     => ['Brouillon', 'bg-secondary'],
                                        'abandoned' => ['Abandonné', 'bg-abandoned'],
                                        'invoiced'  => ['Facturé', 'bg-success-darker'],
                                    ];

                                    [$label, $class] = $map[$status] ?? ['Inconnu', 'bg-dark'];
                                    $isInvoiced = $status === 'invoiced';
                                @endphp

                                <span class="badge {{ $class }}">{{ $label }}</span>
                            </td>

                            {{-- Actions --}}
                            <td>
                                <div class="table-actions">

                                    <a href="{{ route('devis.details', $d->id) }}"
                                       class="btn" title="Voir"
                                       style="background-color: rgb(140, 142, 151); color:white;">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>

                                    @if($isInvoiced)
                                        <a href="#" onclick="showInvoicedAlert(event)"
                                           class="btn" title="Modifier"
                                           style="background-color: rgb(109, 103, 97); color:white;">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>
                                    @else
                                        <a href="{{ route('devis.edit', $d->id) }}"
                                           class="btn" title="Modifier"
                                           style="background-color: rgb(109, 103, 97); color:white;">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>
                                    @endif

                                    <button class="btn delete-button" title="Supprimer"
                                            style="background-color: rgb(101, 77, 79); color:white;"
                                            data-url="{{ route('devis.delete', $d->id) }}"
                                            data-invoiced="{{ $isInvoiced ? 'true' : 'false' }}">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>

                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-white text-center py-4">
                                Aucun devis trouvé pour "{{ request('searchKey') }}".
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="d-flex justify-content-end mt-3">
                    {{ $devis->appends(request()->all())->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// Alerte pour la modification impossible
function showInvoicedAlert(e) {
    e.preventDefault();
    Swal.fire({
        title: 'Modification impossible',
        text: 'Ce devis est déjà facturé.',
        icon: 'warning',
        confirmButtonColor: '#90908c'
    });
}

document.addEventListener('DOMContentLoaded', () => {
    // Gestion de la suppression
    document.querySelectorAll('.delete-button').forEach(btn => {
        btn.addEventListener('click', () => {
            if (btn.dataset.invoiced === 'true') {
                Swal.fire({
                    title: 'Suppression impossible',
                    text: 'Il n\'est pas possible de supprimer un devis déjà facturé.',
                    icon: 'error',
                    confirmButtonColor: '#90908c'
                });
                return;
            }

            Swal.fire({
                title: 'Supprimer ce devis ?',
                text: "Cette action est irréversible.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#90908c',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Oui, supprimer',
                cancelButtonText: 'Annuler'
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