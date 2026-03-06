@extends('user.layouts.master')

@section('content')
     <!-- Begin Page Content -->
     <div class="container-fluid">
        <!-- DataTales Example -->
        <div class="card shadow mb-4 col-5 offset-3">
            <div class="card-header py-3">
                <div class="">
                    <div class="">
                        <h6 class="m-0 font-weight-bold text-dark">Changer le mot de passe...</h6>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <form action="{{route ('changePassword')}}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="exampleFormControlInput1" class="form-label ">Ancien mot de passe</label>
                        <input type="password" name="oldPassword" value="{{old('oldPassword')}}" class="form-control @error('oldPassword') is-invalid @enderror" id="exampleFormControlInput1" >
                        @error('category')
                            <small class="invalid-feedback">{{$message}}</small>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="exampleFormControlInput1" class="form-label ">Nouveau mot de passe</label>
                        <input type="password" name="newPassword" value="{{old('newPassword')}}" class="form-control @error('newPassword') is-invalid @enderror" id="exampleFormControlInput1" >
                        @error('category')
                            <small class="invalid-feedback">{{$message}}</small>
                        @enderror
                    </div><div class="mb-3">
                        <label for="exampleFormControlInput1" class="form-label ">Confirmez le mot de passe</label>
                        <input type="password" name="confirmPassword" value="{{old('confirmPassword')}}" class="form-control @error('confirmPassword') is-invalid @enderror" id="exampleFormControlInput1">
                        @error('category')
                            <small class="invalid-feedback">{{$message}}</small>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <input type="submit" value="Mettre à jour" class="btn btn-primary">
                        </div>
                        <div class="col-6">
                            <a href="{{ route('adminList') }}" class="btn btn-secondary w-100 text-center"
                                            >Retour</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    </div>
    <!-- /.container-fluid -->
@endsection
