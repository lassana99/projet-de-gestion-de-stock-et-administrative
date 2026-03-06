@extends('user.layouts.master')
@section('content')
    <!-- Single Page Header start -->
    <div class="container-fluid page-header py-5">
        <h1 class="text-center text-white display-6">Liste des commandes</h1>
        <ol class="breadcrumb justify-content-center mb-0">
            <li class="breadcrumb-item"><a href="{{route('userDashboard')}}">Accueil</a></li>
            <li class="breadcrumb-item"><a href="#">Pages</a></li>
            <li class="breadcrumb-item active text-white">Liste des commandes</li>
        </ol>
    </div>
    <!-- Single Page Header End -->


    <!-- Cart Page Start -->
    <div class="container-fluid py-5">
        <div class="container py-5 ">
            <div class="table-responsive">
                <table class="table text-white text-center " id="dataTable">
                    <thead>
                      <tr>
                        <th scope="col">Code de commande</th>
                        <th scope="col">Date</th>
                        <th scope="col">Statut de la commande</th>
                      </tr>
                    </thead>
                    <tbody>
                       @foreach ($order as $item)
                       <tr>
                            <td ><a class="text-primary" href="{{route ('customerOrders', $item->order_code)}}">Check Details: {{ $item->order_code }}</a></td>

                            <td><p class="text-center">{{ $item->created_at->format('j-F-y') }}</p></td>

                            <td><p class="" >
                                    @if ($item->status == 0)
                                        <span class="text-warning">En attente</span>
                                    @elseif ($item->status == 1)
                                        <span class="text-secondary">Effectuée</span>
                                    @elseif ($item->status == 2)
                                        <span class="text-danger">Rejeter</span>
                                    @endif
                                </p>
                            </td>
                        </tr>
                       @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- Cart Page End -->

@endsection



