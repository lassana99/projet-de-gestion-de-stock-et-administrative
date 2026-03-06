@extends('admin.layouts.master')

@section('content')
    <!-- Begin Page Content -->
    <div class="container-fluid">
        <!-- DataTales Example -->
        <div class="card shadow mb-4 col-lg-6 col-md-8 col-12 mx-auto">
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
            <div class="card-header py-3" style="background-color:#6c757d;">
                    <h6 class="m-0 font-weight-bold text-white text-center">Réinitialisation manuelle du mot de passe</h6>
            </div>
            <form action="{{ route('resetPassword') }}" method="POST">
                @csrf
                <div class="card-body" style="background-color:#6c757d;">
                    <!-- Champ Email -->
                    <div class="mb-3">
                        <label class="text-white small">Adresse Email de l'utilisateur</label>
                        <input type="text" name="email" value="{{ old('email') }}"
                            class="form-control @error('email') is-invalid @enderror" id="email"
                            placeholder="Entrer l'adresse email...">
                        @error('email')
                            <small class="invalid-feedback">{{ $message }}</small>
                        @enderror
                    </div>

                    <!-- Champ Nouveau Mot de Passe -->
                    <div class="mb-3">
                        <label class="text-white small">Nouveau mot de passe</label>
                        <input type="password" name="newPassword" 
                            class="form-control @error('newPassword') is-invalid @enderror" 
                            placeholder="Saisir le nouveau mot de passe...">
                        @error('newPassword')
                            <small class="invalid-feedback">{{ $message }}</small>
                        @enderror
                    </div>

                    <!-- Champ Confirmation -->
                    <div class="mb-3">
                        <label class="text-white small">Confirmer le mot de passe</label>
                        <input type="password" name="confirmPassword" 
                            class="form-control @error('confirmPassword') is-invalid @enderror" 
                            placeholder="Confirmer le mot de passe...">
                        @error('confirmPassword')
                            <small class="invalid-feedback">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="row mt-4">
                        <div class="col-6">
                            <input type="submit" value="Mettre à jour" 
                                class="btn w-100 font-weight-bold" 
                                style="background-color:rgb(249, 138, 27); color: white; border: none;">
                        </div>
                        <div class="col-6">
                            <a href="{{ route('adminList') }}" 
                                class="btn w-100 text-center font-weight-bold" 
                                style="background-color:rgb(144, 142, 140); color: white; border: none;">
                                Annuler
                            </a>
                        </div>
                    </div>
                </div>
            </form>
         </div>
        </div>
    </div>
    <!-- /.container-fluid -->
@endsection