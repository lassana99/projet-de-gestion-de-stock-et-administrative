@extends('Authentication.layouts.master')

@section('content')
<!-- Gestion du background de la page de connexion -->
<div style="background-color: rgb(146, 144, 144); 
                                    width: 100%;
                                    height: 729px; /* Hauteur fixe */
                                    position: relative; 
                                    left: 50%; 
                                    transform: translateX(-50%);">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-6 col-lg-12 col-md-9">
                <div class="card o-hidden border-0 my-5  registerform">
                    <div class="card-body p-0">
                        <!-- Nested Row within Card Body -->
                        <div class="row">

                            <div class="col-lg-10 offset-1">
                                <div class="p-5">
                                    <div class="text-center">
                                        <h1 class="h4 text-gray-900 mb-4">Créez un compte!</h1>
                                    </div>
                                    <form class="user" method="POST" action="{{ route('register') }}">
                                        @csrf
                                        <div class="form-group">
                                            <input type="text"
                                                class="form-control form-control-user @error('name') is-invalid @enderror"
                                                placeholder="Nom d'utilisateur..." name="name" value="{{ old('name') }}">
                                            @error('name')
                                                <small class="invalid-feedback">{{ $message }}</small>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <input type="email"
                                                class="form-control form-control-user @error('email') is-invalid @enderror"
                                                placeholder="Entrez l'e-mail..." name="email" value="{{ old('email') }}">
                                            @error('email')
                                                <small class="invalid-feedback">{{ $message }}</small>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <input type="text"
                                                class="form-control form-control-user @error('phone') is-invalid @enderror"
                                                placeholder="Entrez le téléphone..." name="phone" value="{{ old('phone') }}">
                                            @error('phone')
                                                <small class="invalid-feedback">{{ $message }}</small>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <input type="text"
                                                class="form-control form-control-user @error('address') is-invalid @enderror"
                                                placeholder="Entrez l'adresse..." name="address" value="{{ old('address') }}">
                                            @error('address')
                                                <small class="invalid-feedback">{{ $message }}</small>
                                            @enderror
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-sm-6 mb-3 mb-sm-0">
                                                <input type="password"
                                                    class="form-control form-control-user @error('password') is-invalid @enderror"
                                                    placeholder="Mot de passe" name="password" value="{{ old('password') }}">
                                                @error('password')
                                                    <small class="invalid-feedback">{{ $message }}</small>
                                                @enderror
                                            </div>
                                            <div class="col-sm-6">
                                                <input type="password"
                                                    class="form-control form-control-user @error('password_confirmation') is-invalid @enderror"
                                                    placeholder="Répéter le mot de passe" name="password_confirmation"
                                                    value="{{ old('password_confirmation') }}">
                                                @error('password_confirmation')
                                                    <small class="invalid-feedback">{{ $message }}</small>
                                                @enderror
                                            </div>
                                        </div>
                                        <input type="submit" value="Créer un compte" class="btn btn-primary btn-user btn-block">
                                        <hr>

                                    </form>
                                    <div class="text-center">
                                        <a class="small" href="{{ route('userLogin') }}">Vous avez déjà un compte ? Connectez-vous !</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

     </div>
</div>
@endsection
