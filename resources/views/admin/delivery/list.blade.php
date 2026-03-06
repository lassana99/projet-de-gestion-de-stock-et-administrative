@extends('admin.layouts.master')

@section('content')
    <div class="container-fluid">
        <div class="card shadow mb-4">
            <div class="card-header py-3" style="background-color:#6c757d;">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="m-0 font-weight-bold text-white">Liste des Livraisons</h5>
                    <a href="{{ route('deliveryCreate') }}" class="btn btn-warning text-white fw-bold" style="background-color: rgb(249, 138, 27); border: none;">
                        <i class="fa-solid fa-plus"></i> Nouvelle livraison
                    </a>
                </div>
            </div>
            <div class="card-body" style="background-color:#6c757d;">
                <div class="table-responsive">
                    <table class="table table-bordered align-middle" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr class="text-center text-white" style="background-color:#6c757d;">
                                <th>Numéro de la facture</th>
                                <th>Client</th>
                                <th>Adresse</th>
                                <th>Produit</th>
                                <th>Image</th>
                                <th>Quantité</th>
                                <th>Date de Livraison</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($deliveries as $delivery)
                                <tr class="text-center text-white" style="background-color:#6c757d;">
                                    <td>{{ $delivery->invoice_number }}</td>
                                    <td>{{ $delivery->customer_name }}</td>
                                    <td>{{ $delivery->customer_address }}</td>
                                    <td>{{ $delivery->product ? $delivery->product->name : 'Non spécifié' }}</td>
                                    <td>
                                        @if($delivery->image)
                                            <img src="{{ asset('storage/' . $delivery->image) }}" 
                                                 style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px;" 
                                                 alt="Image livraison">
                                        @else
                                            <span class="text-warning">Pas d'image</span>
                                        @endif
                                    </td>
                                    <td>{{ $delivery->quantity }}</td>
                                    <td>{{ $delivery->delivery_date ? $delivery->delivery_date->format('d/m/Y') : '-' }}</td>
                                    <td>
                                        @php
                                            $statusLabel = [
                                                'pending' => 'En attente',
                                                'delivered' => 'Livré'
                                            ];
                                            $color = $delivery->delivery_status == 'delivered' ? 'bg-success' : 'bg-secondary';
                                        @endphp
                                        <span class="badge {{ $color }}">
                                            {{ $statusLabel[$delivery->delivery_status] ?? ucfirst($delivery->delivery_status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('deliveryDetails', $delivery->id) }}" class="btn btn-sm btn-info" style="background-color:rgb(140, 142, 151);" title="Détails">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('deliveryEdit', $delivery->id) }}" class="btn btn-sm btn-warning" style="background-color:rgb(109, 103, 97);" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('deliveryDelete', $delivery->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" style="background-color:rgb(101, 77, 79);" title="Supprimer" onclick="return confirm('Supprimer cette livraison ?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <!-- Pagination -->
                    <span class="d-flex justify-content-end">{{ $deliveries->links() }}</span>
                </div>
            </div>
        </div>
    </div>
@endsection
