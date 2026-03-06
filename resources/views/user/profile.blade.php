@extends('user.layouts.master')

@section('content')

   <!-- Single Page Header start -->
   <div class="container-fluid page-header py-5">
        <h1 class="text-center text-white display-6">Votre profil</h1>
        <ol class="breadcrumb justify-content-center mb-0">
            <li class="breadcrumb-item"><a href="{{route('userDashboard')}}">Accueil</a></li>
            <li class="breadcrumb-item "><a href="#">Pages</a></li>
            <li class="breadcrumb-item active text-white">Profil</li>
        </ol>
    </div>
<!-- Single Page Header End -->

    <!-- Begin Page Content -->
        <div class="container-fluid profile py-1" >
            <div class="card-header col-8 offset-2 py-3" style="background-color: #f1f1f1;">
                <div class="mt-1">
                    <div class="">
                        <h4 class="font-weight-bold text-secondary">
                        Profil utilisateure
                        </h4>
                    </div>
                </div>
                    <form action="{{ route('profileUpdate') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="card-body">
                            <div class="row">
                                <div class="col-3">
                                    <input type="hidden" name="oldImage" value="{{auth()->user()->profile}}">

                                    @if (auth()->user()->profile == null)
                                        <img class="img-profile img-thumbnail" id="output" src="{{asset('admin/img/undraw_profile.svg')}}" style="width: 200px;">
                                    @else
                                        <img class="img-profile img-thumbnail" id="output" src="{{asset('userProfile/'. auth()->user()->profile)}}" style="width: 200px;">
                                    @endif


                                        <input type="file" name="image"
                                        class="form-control mt-1 @error('image') is-invalid @enderror"
                                        onchange="loadFile(event)">
                                    @error('image')
                                        <small class="invalid-feedback">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="col">
                                    <div class="row">
                                        <div class="col">
                                            <div class="mb-3">
                                                <label for="exampleFormControlInput1" class="form-label">Nom</label>
                                                <input type="text" name="name" @if (auth()->user()->provider != 'simple') disabled @endif
                                                    class="form-control @error('name') is-invalid @enderror"
                                                    id="exampleFormControlInput1" placeholder="Name..." value="{{old('name',auth()->user()->name == null ? auth()->user()->nickname : auth()->user()->name)}}">
                                                @error('name')
                                                    <small class="invalid-feedback">{{ $message }}</small>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="mb-3">
                                                <label for="exampleFormControlInput1" class="form-label">Email</label>
                                                <input type="text" name="email" @if (auth()->user()->provider != 'simple') disabled @endif
                                                    class="form-control @error('email') is-invalid @enderror"
                                                    id="exampleFormControlInput1" placeholder="Email..." value="{{auth()->user()->email}}">
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
                                                    id="exampleFormControlInput1" placeholder="09xxxxxxxx" value="{{old('phone',auth()->user()->phone)}}">
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
                                                    id="exampleFormControlInput1" placeholder="Adresse..." value="{{old('address',auth()->user()->address)}}">
                                                @error('address')
                                                    <small class="invalid-feedback">{{ $message }}</small>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    @if (auth()->user()->provider == 'simple')
                                    <a class="text-info fw-bold" href="{{route('changePassword')}}">Changer le mot de passe</a><br><br>
                                    @endif

                                    <div class="row">
                                        <div class="col">
                                            <input type="submit" value="Update" class="btn btn-warning w-100 mt-2">
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
