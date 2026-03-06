@extends('admin.layouts.master')

@section('content')
    <!-- Begin Page Content -->
    <div class="container-fluid">
        <div class="card shadow mb-4">
            <div class="card-header py-3" style="background-color:#6c757d;">
                <div class="d-flex justify-content-between">
                    <div class="" style="background-color:#6c757d;">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <form action="{{ route('userList') }}" method="get">
                                <div class="input-group mb-3">
                                    <input type="text" name="searchKey" class="form-control " placeholder="Chercher..."
                                        value="{{ request('searchKey') }}">
                                    <button class="btn btn-outline-secondary" type="submit" id="button-addon2" style="background-color: rgb(249, 138, 27); color: white; border: none;">
                                        <i class="fa-solid fa-magnifying-glass"></i>
                                    </button>
                                </div>
                            </form>
                        </h6>
                    </div>

                </div>
            </div>
            <div class="card-body" style="background-color:#6c757d;">
            <div class="d-flex mb-3">
                <a href="{{ route('adminList') }}" class="btn mr-2" style="background-color:rgb(144, 142, 140);color: white;">
                    Liste des administrateurs 
                    <span class="badge badge-light">{{ $adminCount }}</span>
                </a>
                <a href="{{ route('userList') }}" class="btn" style="background-color:rgb(144, 142, 140); color: white;">
                    Liste des clients 
                    <span class="badge badge-light">{{ $data->total() }}</span>
                </a>
            </div>

                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr class="text-center text-white">
                                <th>Nom</th>
                                <th>Email</th>
                                <th>Téléphone</th>
                                <th>Adresse</th>
                                @if (auth()->user()->role == 'superadmin')
                                    <th>Actions</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data as $item)
                                <tr class="text-center text-white">
                                <td class="font-weight-bold" style="color: rgb(255, 193, 7);">
                                    @if ($item->name != null)
                                        <a href="{{ route('accountProfile', $item->id) }}" style="color: rgb(255, 193, 7);">
                                            {{ $item->name }}
                                        </a>
                                    @else
                                        <a href="{{ route('accountProfile', $item->id) }}" style="color: rgb(255, 193, 7);">
                                            {{ $item->nickname }}
                                        </a>
                                    @endif
                                </td>

                                    <td>{{ $item->email }}</td>
                                    <td>{{ $item->phone }}</td>
                                    <td>{{ $item->address }}</td>

                                    @if (auth()->user()->role == 'superadmin')
                                    <td>
                                        @if (auth()->user()->id != $item->id)
                                            <!-- Bouton Supprimer (Rouge) -->
                                            <a href="{{ route('deleteAdminAccount', $item->id) }}">
                                                <button class="btn btn-sm text-white" 
                                                    style="background-color:rgb(101, 77, 79);border: none;">
                                                    <i class="fa-solid fa-trash-can"></i>
                                                </button>
                                            </a>

                                            <!-- Bouton Passer au rôle d'administrateur (Orange) -->
                                            <a href="{{ route('changeAdminRole', $item->id) }}">
                                                <button class="btn btn-sm text-white" 
                                                    style="background-color:rgb(144, 142, 140); border: none;">
                                                    Passer au rôle d'administrateur
                                                    <i class="p-1 fa-solid fa-arrow-up"></i>
                                                </button>
                                            </a>
                                        @endif
                                    </td>
                                    @endif
                                </tr>
                            @endforeach

                        </tbody>
                    </table>
                    <span class="d-flex justify-content-end">{{ $data->links() }}</span>
                </div>
            </div>
        </div>

    </div>
    <!-- /.container-fluid -->
@endsection
