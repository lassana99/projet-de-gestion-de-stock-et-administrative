@extends('user.layouts.master')
@section('content')
<!-- Modal Search Start -->
<div class="modal fade" id="searchModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content rounded-0">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Recherche par mot-clé</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body d-flex align-items-center">
                <div class="input-group w-75 mx-auto d-flex">
                    <input type="search" class="form-control p-3" placeholder="keywords" aria-describedby="search-icon-1">
                    <span id="search-icon-1" class="input-group-text p-3"><i class="fa fa-search"></i></span>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Modal Search End -->


<!-- Single Page Header start -->
<div class="container-fluid page-header py-5">
    <h1 class="text-center text-white display-6">Votre commande</h1>
    <ol class="breadcrumb justify-content-center mb-0">
        <li class="breadcrumb-item"><a href="{{route ('userDashboard')}}">Accueil</a></li>

    </ol>
</div>
<!-- Single Page Header End -->
        <!-- Begin Page Content -->
        <div class="container-fluid py-1">
                <div class="card-body">
                    <a href="{{ route('orderList')}}" class="text-white fw-bold"><i class="fa-solid fa-arrow-left-long"></i> Back</a>
                    <div class="table-responsive">
                        <table class="text-white text-center table table-bordered mt-3" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Image</th>
                                    <th>Nom</th>
                                    <th>Quantité</th>
                                    <th>Prix ​​du produit</th>
                                    <th>Prix ​​total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($order as $item)
                                <tr>
                                    <td class="col-2"><img class="img-thumbnail"
                                        src="{{ asset('productImages/' . $item->productimage) }}" alt=""></td>
                                    <td>{{ $item->productname }}</td>
                                    <td>{{ $item->ordercount }}</td>
                                    <td>{{ $item->productprice}}</td>
                                    <td>{{ $item->ordercount * $item->productprice}}</td>
                                </tr>
                                @endforeach

                            </tbody>

                        </table>

                        <span class="d-flex justify-content-end">{{ $order->links()}}</span>
                    </div>
                </div>
            </div>


        <!-- /.container-fluid -->
@endsection
