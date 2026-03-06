@extends('admin.layouts.master')

@section('content')

        <div class="card shadow mb-4 col-5 offset-3" style="background-color:#6c757d; border: none;">
            <div class="card-body" 
                                style="
                                    background-color:#6c757d; 
                                    width: 110%; 
                                    position: relative; 
                                    left: 50%; 
                                    transform: translateX(-50%);
                                    border: 5px solid rgb(249, 138, 27); /* Couleur orange */
                                    border-radius: 15px; /* Coins arrondis */
                                    box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2); /* Ombre pour un effet esthétique */
                                ">
                <div class="card-header py-3 justify-content-center" style="background-color:#6c757d;">
                    <div class="">
                        <h6 class="m-0 font-weight-bold text-white text-center">Ajouter un nouveau compte</h6>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('createAdmin') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="name" class="form-label">Nom</label>
                            <input type="text" name="name" value="{{ old('name') }}"
                                class="form-control @error('name') is-invalid @enderror" id="name" placeholder="Nom complet">
                            @error('name')
                                <small class="invalid-feedback">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="text" name="email" value="{{ old('email') }}"
                                class="form-control @error('email') is-invalid @enderror" id="email" placeholder="Ex: admin@gmail.com">
                            @error('email')
                                <small class="invalid-feedback">{{ $message }}</small>
                            @enderror
                        </div>

                        <!-- AJOUT DU CHAMP RÔLE -->
                        <div class="mb-3">
                            <label for="role" class="form-label">Rôle du compte</label>
                            <select name="role" class="form-control @error('role') is-invalid @enderror" id="role">
                                <option value="" disabled selected>Choisir un rôle...</option>
                                <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Administrateur</option>
                                <option value="superadmin" {{ old('role') == 'superadmin' ? 'selected' : '' }}>Super-Administrateur</option>
                            </select>
                            @error('role')
                                <small class="invalid-feedback">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Mot de passe</label>
                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                                id="password" placeholder="Minimum 8 caractères">
                            @error('password')
                                <small class="invalid-feedback">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="confirmpassword" class="form-label">Confirmez le mot de passe</label>
                            <input type="password" name="confirmpassword"
                                class="form-control @error('confirmpassword') is-invalid @enderror" id="confirmpassword" placeholder="Répéter le mot de passe">
                            @error('confirmpassword')
                                <small class="invalid-feedback">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="row mt-4">
                            <div class="col">
                                <input type="submit" value="Créer le compte" 
                                    class="btn w-100 font-weight-bold" 
                                    style="background-color: rgb(249, 138, 27); color: white; border: none;">
                            </div>
                            <div class="col">
                                <a href="{{ route('adminList') }}" 
                                    class="btn w-100 font-weight-bold" 
                                    style="background-color: rgb(144, 142, 140); color: white; border: none;">
                                    Annuler
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <!-- /.container-fluid -->
@endsection