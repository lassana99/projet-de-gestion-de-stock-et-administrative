@extends('user.layouts.master')
@section('content')

   <!-- Single Page Header start -->
   <div class="container-fluid page-header py-5">
        <h1 class="text-center text-white display-6">Contact</h1>
        <ol class="breadcrumb justify-content-center mb-0">
            <li class="breadcrumb-item"><a href="{{route('userDashboard')}}">Accueil</a></li>
            <li class="breadcrumb-item "><a href="#">Achat</a></li>
            <li class="breadcrumb-item active text-white">Contact</li>
        </ol>
    </div>
    <!-- Single Page Header End -->

    <div class="container-fluid py-3">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header bg-primary text-black fw-bold">Contactez-nous</div>
                            <div class="card-body">
                                <form method="post" action="{{route('sendMessage')}}">
                                    @csrf
                                    <div class="form-group">
                                        <label for="subject">Sujet</label>
                                        <input type="text" name="subject" id="subject" value="{{old('subject')}}" class="form-control" placeholder="Enter subject" required>
                                    </div>

                                    <div class="form-group mt-3">
                                        <label for="message">Message</label>
                                        <textarea name="message" id="message" rows="5" value="{{old('message')}}" class="form-control" placeholder="Write your message here" required></textarea>
                                    </div>

                                    <div class="form-group mt-4">
                                        <button type="submit" class="btn btn-secondary btn-block w-100">Envoyer un message</button>

                                        @if (session('success'))
                                            <div class="alert alert-success mt-3">
                                                {{ session('success') }}
                                            </div>
                                        @endif
                                    </div>

                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </div>
    @endsection
