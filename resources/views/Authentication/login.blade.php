@extends('Authentication.layouts.master')

@section('content')
<div style="background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('{{ asset('loginImage/ERP5.jpg') }}'); 
            background-size: cover; 
            background-position: center; 
            background-repeat: no-repeat;
            width: 100%;
            height: 729px; /* Hauteur fixe conservée */
            position: relative; 
            left: 50%; 
            transform: translateX(-50%);
            display: flex;
            align-items: center;
            justify-content: center;">
            
    <div class="container">
        <div class="row justify-content-center">

            <div class="col-xl-6 col-lg-12 col-md-9">

                <!-- Ajout de margin-top négatif pour remonter la carte -->
                <div class="card o-hidden border-0 my-5 loginform shadow-lg" style="margin-top: -300px !important;">
                    <div class="card-body p-0">
                        <div class="row">
                            <div class="col-lg-10 offset-1">
                                <div class="p-5">
                                    <div class="text-center">
                                        <h1 class="mb-4">Se connecter</h1>
                                    </div>
                                    <form class="user" method="POST" action="{{ route('login') }}">
                                        @csrf
                                        <div class="form-group">
                                            <input type="email" 
                                                class="form-control form-control-user @error('email') is-invalid @enderror"
                                                placeholder="Entrer votre Email..." name="email" value="{{ old('email') }}">
                                            @error('email')
                                                <small class="invalid-feedback">{{ $message }}</small>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <input type="password" 
                                                class="form-control form-control-user @error('password') is-invalid @enderror"
                                                placeholder="Votre mot de passe" name="password">
                                            @error('password')
                                                <small class="invalid-feedback">{{ $message }}</small>
                                            @enderror
                                        </div>

                                        <input type="submit" value="Connexion"
                                            class="btn w-100 text-white btn-user btn-block"
                                            style="
                                                background-color: rgb(249, 138, 27); 
                                                border: none;">

                                        {{-- Ligne HR supprimée --}}
                                        
                                    </form>
                                    {{-- Ligne HR supprimée --}}
                                  <!--
                                    <div class="text-center mt-4">
                                        {{-- Modification des classes et ajout de style pour agrandir et mettre en gras --}}
                                        <a class="text-dark font-weight-bold" 
                                            href="{{ route('userRegister') }}" 
                                            style="font-size: 0.9rem;">
                                            Vous n'avez pas de compte ? Inscrivez-vous ici !
                                        </a>
                                    </div>
                                    -->
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