@extends('admin.layouts.master')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4 col-5 offset-3">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-dark">Changer le mot de passe</h6>
        </div>

        <div class="card-body">
            <form action="{{ route('changePassword') }}" method="POST">
                @csrf

                {{-- Ancien mot de passe --}}
                <div class="mb-3">
                    <label class="form-label">Ancien mot de passe</label>
                    <input
                        type="password"
                        name="oldPassword"
                        class="form-control @error('oldPassword') is-invalid @enderror"
                        autocomplete="current-password"
                    >
                    @error('oldPassword')
                        <small class="invalid-feedback">{{ $message }}</small>
                    @enderror
                </div>

                {{-- Nouveau mot de passe --}}
                <div class="mb-3">
                    <label class="form-label">Nouveau mot de passe</label>
                    <input
                        type="password"
                        name="newPassword"
                        class="form-control @error('newPassword') is-invalid @enderror"
                        autocomplete="new-password"
                    >
                    @error('newPassword')
                        <small class="invalid-feedback">{{ $message }}</small>
                    @enderror
                </div>

                {{-- Confirmation --}}
                <div class="mb-3">
                    <label class="form-label">Confirmer le mot de passe</label>
                    <input
                        type="password"
                        name="password_confirmation"
                        class="form-control @error('password_confirmation') is-invalid @enderror"
                        autocomplete="new-password"
                    >
                    @error('password_confirmation')
                        <small class="invalid-feedback">{{ $message }}</small>
                    @enderror
                </div>

                {{-- Boutons --}}
                <div class="row">
                    <div class="col-6">
                        <button type="submit" class="btn btn-primary w-100">
                            Mettre à jour
                        </button>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('adminDashboard') }}" class="btn btn-secondary w-100">
                            Retour
                        </a>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection
