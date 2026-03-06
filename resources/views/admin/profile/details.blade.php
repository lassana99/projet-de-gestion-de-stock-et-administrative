@extends('admin.layouts.master')

@section('content')
    <!-- Begin Page Content -->
    <div class="container-fluid">
        <!-- DataTales Example -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <div class="">
                    <div class="">
                        <h6 class="m-0 font-weight-bold text-dark">
                        Profil d'administrateur
                        </h6>
                    </div>
                </div>
            </div>
            <form action="{{ route('adminProfileUpdate') }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="card-body">
                    <div class="row">
                        <div class="">
                            <input type="hidden" name="oldImage" value="{{ auth()->user()->profile }}">

                            <div class="mb-3 text-center">

                                @if (auth()->user()->profile == null)
                                    <img class="img-profile img-thumbnail" id="output"
                                        src="{{ asset('admin/img/undraw_profile.svg') }}" style="max-width: 300px; height:300px;">
                                @else
                                    <img class="img-profile img-thumbnail" id="output"
                                        src="{{ asset('adminProfile/' . auth()->user()->profile) }}" style="max-width: 300px; height:300px;">
                                @endif
                                <input type="file" name="image" style="max-width: 300px;"
                                    class="form-control mt-1 @error('image') is-invalid @enderror"
                                    onchange="loadFile(event)">
                            </div>
                            @error('image')
                                <small class="invalid-feedback">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="col">
                            <div class="row">
                                <div class="col">
                                    <div class="mb-3">
                                        <label for="exampleFormControlInput1" class="form-label">Nom</label>
                                        <input type="text" name="name"
                                            @if (auth()->user()->provider != 'simple') disabled @endif
                                            class="form-control @error('name') is-invalid @enderror"
                                            id="exampleFormControlInput1" placeholder="Name..."
                                            value="{{ old('name', auth()->user()->name == null ? auth()->user()->nickname : auth()->user()->name) }}">
                                        @error('name')
                                            <small class="invalid-feedback">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="mb-3">
                                        <label for="exampleFormControlInput1" class="form-label">Email</label>
                                        <input type="text" name="email"
                                            @if (auth()->user()->provider != 'simple') disabled @endif
                                            class="form-control @error('email') is-invalid @enderror"
                                            id="exampleFormControlInput1" placeholder="Email..."
                                            value="{{ auth()->user()->email }}">
                                        @error('email')
                                            <small class="invalid-feedback">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <div class="mb-3">
                                        <label for="exampleFormControlInput1" class="form-label">Téléphone</label>
                                        <input type="text" name="phone"
                                            class="form-control @error('phone') is-invalid @enderror"
                                            id="exampleFormControlInput1" placeholder="09xxxxxxxx"
                                            value="{{ old('phone', auth()->user()->phone) }}">
                                        @error('phone')
                                            <small class="invalid-feedback">{{ $message }}</small>
                                        @enderror

                                    </div>
                                </div>
                                <div class="col">
                                    <div class="mb-3">
                                        <label for="exampleFormControlInput1" class="form-label">Adresse</label>
                                        <input type="text" name="address"
                                            class="form-control @error('address') is-invalid @enderror"
                                            id="exampleFormControlInput1" placeholder="Address..."
                                            value="{{ old('address', auth()->user()->address) }}">
                                        @error('address')
                                            <small class="invalid-feedback">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                                    @if (auth()->user()->provider == 'simple')
                                        <a href="{{ route('passwordChange') }}">Changer le mot de passe</a><br><br>
                                    @endif
                            <div class="row">
                                <div class="col-6">
                                    <input type="submit" value="Mettre à jour" class="btn btn-primary mt-2 w-100">
                                </div>
                                <div class="col-6">
                                    <a href="{{ route('adminList') }}" class="btn btn-secondary mt-2 w-100 text-center"
                                    >Retour</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

    </div>
    <!-- /.container-fluid -->
@endsection
