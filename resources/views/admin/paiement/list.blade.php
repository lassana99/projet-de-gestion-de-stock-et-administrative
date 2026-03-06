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

    .action-btn {
        width: 36px;
        height: 36px;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
        border: none;
        color: white;
        transition: transform 0.2s;
    }

    .action-btn:hover {
        transform: scale(1.1);
        color: white;
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
                
                {{-- Barre de recherche --}}
                <form action="{{ route('paymentList') }}" method="GET" class="d-flex" style="max-width: 400px;">
                    <div class="input-group">
                        <input type="text" name="searchKey" class="form-control" placeholder="Facture, Client..." value="{{ request('searchKey') }}">
                        <div class="input-group-append">
                            <button class="btn" type="submit" style="background-color: rgb(249, 138, 27); color: white; border: none;">
                                <i class="fa-solid fa-magnifying-glass"></i>
                            </button>
                            @if(request()->filled('searchKey'))
                                <a href="{{ route('paymentList') }}" class="btn btn-secondary" title="Effacer le filtre" style="border: none;">
                                    <i class="fa-solid fa-xmark"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                </form>

                <div class="d-flex align-items-center">
                    {{-- Bouton Exportation Excel Direct --}}
                    <a href="{{ route('paymentExportExcel', ['searchKey' => request('searchKey')]) }}" 
                       class="btn btn-light mr-3" 
                       style="font-weight: 500;">
                        <i class="fa-solid fa-file-excel text-success me-1"></i> Exporter en Excel
                    </a>

                    <a href="{{ route('paymentCreate') }}" class="btn text-white fw-bold shadow-sm" style="background-color: rgb(249, 138, 27); border: none;">
                        <i class="fa-solid fa-plus"></i> Nouveau Paiement
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
                <table class="table table-bordered text-white" id="dataTable" width="100%" cellspacing="0" style="background-color:#6c757d; border: 1px solid white !important;">
                    <thead>
                        <tr class="bg-secondary text-white">
                            <th>N° Facture</th>
                            <th>Client</th>
                            <th>Montant HTVA</th>
                            <th>Mode</th>
                            <th>Date</th>
                            <th style="width: 140px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payments as $p)
                        <tr>
                            <td><strong>{{ $p->invoice_number }}</strong></td>
                            <td class="fw-bold"><strong>{{ $p->client_name }}</strong></td>
                            <td class="fw-bold"><strong>{{ number_format($p->amount_htva, 0, '', ' ') }} FCFA</strong></td>
                            <td><span class="badge bg-info p-2">{{ $p->payment_method }}</span></td>
                            <td>{{ $p->payment_date->format('d/m/Y') }}</td>
                            <td>
                                <div class="table-actions">
                                    {{-- Voir --}}
                                    <a href="{{ route('paymentDetails', $p->id) }}" class="action-btn" 
                                       style="background-color: rgb(140, 142, 151);" title="Détails">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>

                                    {{-- Modifier --}}
                                    <a href="{{ route('paymentEdit', $p->id) }}" class="action-btn" 
                                       style="background-color: rgb(109, 103, 97);" title="Modifier">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>

                                    {{-- Supprimer --}}
                                    <button type="button" class="action-btn delete-payment-btn" 
                                            style="background-color: rgb(101, 77, 79);" 
                                            title="Supprimer"
                                            data-url="{{ route('paymentDelete', $p->id) }}" 
                                            data-invoice="{{ $p->invoice_number }}">
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

                <div class="d-flex justify-content-end mt-3">
                    {{ $payments->appends(request()->all())->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Scripts JS --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Gestion de la suppression avec SweetAlert2
        document.querySelectorAll('.delete-payment-btn').forEach(button => {
            button.addEventListener('click', function() {
                const url = this.getAttribute('data-url');
                const invoice = this.getAttribute('data-invoice');

                Swal.fire({
                    title: 'Confirmer la suppression ?',
                    text: "Voulez-vous vraiment supprimer le paiement de la facture " + invoice + " ? Cette action est irréversible.",
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