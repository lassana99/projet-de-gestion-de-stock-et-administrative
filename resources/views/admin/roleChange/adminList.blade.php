@extends('admin.layouts.master')

@section('content')
<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Gestion des Administrateurs</h1>
        <a href="{{ route('createAdminAccount') }}" class="btn shadow-sm text-white fw-bold" 
           style="background-color: rgb(249, 138, 27); border:none; padding: 10px 20px; font-size: 1rem;">
            <i class="fas fa-plus fa-sm text-white-50 mr-2"></i> Ajouter un compte
        </a>
    </div>

    <!-- Card avec bordure blanche -->
    <div class="card shadow mb-4" style="border: 1px solid white;">
        {{-- Header avec fond gris et trait de séparation blanc --}}
        <div class="card-header py-3" style="background-color:#6c757d; border-bottom: 1px solid white;">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-white">Liste des Administrateurs</h6>
                
                {{-- Barre de recherche avec bouton de suppression de filtre --}}
                <div class="flex-grow-1" style="max-width: 250px; margin-left: 20px;">
                    <form action="{{ route('adminList') }}" method="GET">
                        <div class="input-group">
                            <input type="text" name="searchKey" value="{{ request('searchKey') }}" 
                                   class="form-control" placeholder="Rechercher...">
                            <div class="input-group-append">
                                <button class="btn" type="submit" style="background-color: rgb(249, 138, 27); border: none;">
                                    <i class="fas fa-search fa-sm" style="color: white;"></i>
                                </button>
                                {{-- Bouton de suppression de filtre (X) --}}
                                @if(request()->filled('searchKey'))
                                    <a href="{{ route('adminList') }}" class="btn btn-secondary" title="Effacer le filtre" style="border: none;">
                                        <i class="fas fa-times fa-sm"></i>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        {{-- Body --}}
        <div class="card-body" style="background-color:#6c757d;">
            <div class="table-responsive">
                <style>
                    /* Lignes du tableau en blanc */
                    .table-bordered, 
                    .table-bordered td, 
                    .table-bordered th {
                        border: 1px solid white !important;
                        color: white !important;
                        vertical-align: middle !important;
                        text-align: center !important;
                        white-space: nowrap;
                    }

                    .table-actions {
                        display: flex;
                        justify-content: center;
                        align-items: center;
                        gap: 8px;
                    }

                    .role-badge {
                        min-width: 160px;
                        display: inline-block;
                        padding: 8px;
                        border-radius: 5px;
                        font-weight: 600;
                        color: white;
                    }

                    /* Effet de survol comme sur la page prospect */
                    #dataTable tbody tr:hover {
                        background-color: #5a6268 !important; 
                        cursor: pointer;
                    }
                </style>

                <table class="table table-bordered align-middle" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Nom</th>
                            <th>Email</th>
                            <th class="text-center">Rôle</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data as $item)
                            @php 
                                $userRole = trim($item->role); 
                                $isAdminOrSuper = ($userRole == 'admin' || $userRole == 'superadmin');
                            @endphp
                            <tr>
                                <td class="text-center">
                                    @if($item->profile)
                                        <img src="{{ asset('adminProfile/' . $item->profile) }}" class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">
                                    @else
                                        <div class="rounded-circle bg-secondary d-inline-flex align-items-center justify-content-center text-white" style="width: 40px; height: 40px;">
                                            {{ strtoupper(substr($item->name, 0, 1)) }}
                                        </div>
                                    @endif
                                </td>
                                <td class="font-weight-bold">{{ $item->name }}</td>
                                <td>{{ $item->email }}</td>
                                
                                <td class="text-center">
                                    @if($userRole == 'superadmin')
                                        <span class="role-badge" style="background-color: #dc3545;">
                                            <i class="fas fa-user-shield"></i> Super-Administrateur
                                        </span>
                                    @elseif($userRole == 'admin')
                                        <span class="role-badge" style="background-color: #007bff;">
                                            <i class="fas fa-user-tie"></i> Administrateur
                                        </span>
                                    @else
                                        <span class="role-badge" style="background-color: #4b5563;">
                                            Utilisateur Simple
                                        </span>
                                    @endif
                                </td>

                                <td class="text-center">
                                    <div class="table-actions">
                                        <a href="{{ route('accountProfile', $item->id) }}" title="Voir profil"
                                           class="btn d-flex align-items-center justify-content-center p-0 shadow-sm"
                                           style="background-color: rgb(140, 142, 151); border: none; width: 36px; height: 36px; color: white;">
                                            <i class="fas fa-eye" style="font-size: 1.1rem;"></i>
                                        </a>

                                        @if(Auth::user()->id !== $item->id)
                                            @if($isAdminOrSuper)
                                                <a href="{{ route('changeUserRole', $item->id) }}" title="Changer Rôle"
                                                   class="btn d-flex align-items-center justify-content-center p-0 shadow-sm"
                                                   style="background-color: rgb(109, 103, 97); border: none; width: 36px; height: 36px; color: white;">
                                                    <i class="fas fa-sync" style="font-size: 1.1rem;"></i>
                                                </a>
                                            @endif

                                            <a href="{{ route('deleteAdminAccount', $item->id) }}" 
                                               class="btn d-flex align-items-center justify-content-center p-0 shadow-sm"
                                               title="Supprimer"
                                               style="background-color: rgb(101, 77, 79); border: none; width: 36px; height: 36px; color: white;"
                                               onclick="return confirm('Supprimer ce compte administrateur ?')">
                                                <i class="fas fa-trash-can" style="font-size: 1.1rem;"></i>
                                            </a>
                                        @else
                                            <span class="badge border text-white align-self-center px-2 py-1" style="background-color: rgba(0,0,0,0.2); border: 1px solid white !important;">
                                                Mon compte
                                            </span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-white">
                                    @if(request('searchKey'))
                                        Aucun résultat pour "{{ request('searchKey') }}".
                                    @else
                                        Aucun administrateur trouvé.
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="mt-3 d-flex justify-content-end">
                {{ $data->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>

@include('sweetalert::alert')

@endsection