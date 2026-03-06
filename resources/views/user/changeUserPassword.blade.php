@extends('user.layouts.master')

@section('content')

<!-- Single Page Header start -->
<div class="container-fluid page-header py-5">
        <h1 class="text-center text-white display-6">Changer le mot de passe</h1>
        <ol class="breadcrumb justify-content-center mb-0">
            <li class="breadcrumb-item"><a href="{{route('userDashboard')}}">Accueil</a></li>
            <li class="breadcrumb-item "><a href="#">Pages</a></li>
            <li class="breadcrumb-item active text-white">CChanger le mot de passe</li>
        </ol>
    </div>
<!-- Single Page Header End -->

     <!-- Begin Page Content -->
     <div class="container-fluid py-5">
        <div class="card shadow col-6 offset-3 py-3">
                {{-- <div class="container"> --}}

                <div class="card-header">
                    <div class="">
                        <div class="">
                            <h5 class="font-weight-bold text-secondary">Changer le mot de passe...</h5>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{route ('changeUserPassword')}}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="exampleFormControlInput1" class="form-label ">Ancien mot de passe</label>
                            <input type="password" name="oldPassword" value="{{old('oldPassword')}}" class="form-control @error('oldPassword') is-invalid @enderror" id="exampleFormControlInput1" >
                            @error('oldPassword')
                                <small class="invalid-feedback">{{$message}}</small>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="exampleFormControlInput1" class="form-label ">Nouveau mot de passe</label>
                            <input type="password" name="newPassword" value="{{old('newPassword')}}" class="form-control @error('newPassword') is-invalid @enderror" id="exampleFormControlInput1" >
                            @error('newPassword')
                                <small class="invalid-feedback">{{$message}}</small>
                            @enderror
                        </div><div class="mb-3">
                            <label for="exampleFormControlInput1" class="form-label ">Confirmez le mot de passe</label>
                            <input type="password" name="confirmPassword" value="{{old('confirmPassword')}}" class="form-control @error('confirmPassword') is-invalid @enderror" id="exampleFormControlInput1">
                            @error('confirmPassword')
                                <small class="invalid-feedback">{{$message}}</small>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col">
                                <input type="submit" value="Change" class="btn btn-warning w-100">
                            </div>
                        </div>
                    </form>
                </div>
        </div>
    </div>
    <!-- /.container-fluid -->
@endsection
