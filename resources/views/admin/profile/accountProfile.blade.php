@extends('admin.layouts.master')

@section('content')
<div class="container-fluid">
    <!-- Card Principale -->
    <div class="card shadow mb-4 col-lg-10 mx-auto" style="background-color: #6c757d; border: 1px solid white;">
        
        <!-- Header de la Card -->
        <div class="card-header py-3" style="background-color: #6c757d; border-bottom: 1px solid white; margin-left: -12px; margin-right: -12px;">
            <h6 class="m-0 font-weight-bold text-white">
                <i class="fas fa-user-circle mr-2"></i> Informations sur le compte
            </h6>
        </div>

        <div class="card-body" style="background-color: #6c757d; margin-left: -12px; margin-right: -12px;">
            <div class="row">
                <!-- Colonne Gauche : Image -->
                <div class="col-md-4 text-center" style="border-right: 1px solid white !important;">
                    <div class="mb-3">
                        @if($account->profile)
                            <img class="shadow-sm" src="{{ asset('adminProfile/' . $account->profile) }}" 
                                 alt="Profile" id="output" 
                                 style="width: 250px; height: 250px; object-fit: cover; border: 2px solid white;">
                        @else
                            <div class="rounded bg-secondary d-flex align-items-center justify-content-center text-white mx-auto shadow-sm" 
                                 style="width: 250px; height: 250px; border: 2px solid white; font-size: 5rem;">
                                {{ strtoupper(substr($account->name, 0, 1)) }}
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Colonne Droite : Détails -->
                <div class="col-md-8 text-white">
                    <style>
                        /* Style pour que les titres soient plus grands que les contenus */
                        .info-label {
                            font-size: 1.2rem; /* Grande taille pour le titre */
                            color: #f8f9fa;
                            font-weight: bold;
                            margin-bottom: 2px;
                            display: block;
                        }
                        .info-content {
                            font-size: 1rem; /* Taille plus petite pour le contenu */
                            color: #ffc107; /* Couleur jaune/orange pour le contenu */
                            margin-bottom: 20px;
                            font-weight: normal;
                        }
                    </style>

                    <div class="row mt-3">
                        <!-- Nom -->
                        <div class="col-md-6">
                            <label class="info-label">Nom Complet</label>
                            <div class="info-content">{{ $account->name ?: $account->nickname }}</div>
                        </div>
                        
                        <!-- Email -->
                        <div class="col-md-6">
                            <label class="info-label">Adresse Email</label>
                            <div class="info-content">{{ $account->email }}</div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Téléphone -->
                        <div class="col-md-6">
                            <label class="info-label">Téléphone</label>
                            <div class="info-content">{{ $account->phone ?: '..............' }}</div>
                        </div>
                        
                        <!-- Adresse -->
                        <div class="col-md-6">
                            <label class="info-label">Adresse</label>
                            <div class="info-content">{{ $account->address ?: '..............' }}</div>
                        </div>
                    </div>

                    <!-- Rôle -->
                    <div class="row">
                        <div class="col-md-12">
                            <label class="info-label">Rôle / Niveau d'accès</label>
                            <div class="mt-2">
                                @if($account->role == 'superadmin')
                                    <span class="badge p-2" style="background-color: #dc3545; color: white; min-width: 180px; font-size: 1rem; border: 1px solid white;">
                                        <i class="fas fa-user-shield"></i> Super-Administrateur
                                    </span>
                                @elseif($account->role == 'admin')
                                    <span class="badge p-2" style="background-color: #007bff; color: white; min-width: 180px; font-size: 1rem; border: 1px solid white;">
                                        <i class="fas fa-user-tie"></i> Administrateur
                                    </span>
                                @else
                                    <span class="badge p-2" style="background-color: #4b5563; color: white; min-width: 180px; font-size: 1rem; border: 1px solid white;">
                                        Utilisateur Simple
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Bouton Retour -->
                    <div class="row mt-5">
                        <div class="col-md-4">
                            <a href="{{ route('adminList') }}" class="btn btn-outline-light w-100 font-weight-bold shadow-sm" 
                               style="border: 1px solid white; background-color: rgba(255,255,255,0.1);">
                                <i class="fas fa-arrow-left mr-1"></i> Retour à la liste
                            </a>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection