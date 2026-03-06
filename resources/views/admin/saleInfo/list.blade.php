@extends('admin.layouts.master')
@section('content')
        <!-- Begin Page Content -->
        <div class="container-fluid">
            <!-- DataTales Example -->
            <div class="card shadow mb-4">
                <div class="card-header py-3" style="background-color:#6c757d;">
                    <div class="d-flex justify-content-between">
                        <div class="">
                            <h5 class="m-0 font-weight-bold text-white">Informations sur les ventes</h5>
                        </div>

                    </div>
                </div>
                <div class="card-body" style="background-color:#6c757d;">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr class="text-center text-white" style="background-color:#6c757d;">
                                    <th>Image du produit</th>
                                    <th>Nom</th>
                                    <th>Nom du client</th>
                                    <th>Pays d'origine</th> <!-- cette partie doit être gérer du côté du client -->
                                    <th>Date</th>
                                    <th>Quantité</th>
                                    <th>Montant</th>
                                    <th>Code de commande</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($order as $item)
                                    <tr class="text-center text-white" style="background-color:#6c757d;">
                                        <td><img class="rounded-circle" style="width: 64px; height: 64px;" src="{{ asset('productImages/'. $item->productimage)}}" alt=""></td>
                                        <td>{{ $item->productname }}</td>
                                        <td>{{ $item->username }}</td>
                                        <td>{{ $item->usercontry }}</td>
                                        <td>{{ $item->created_at->format('j-F-Y') }}</td>
                                        <td>{{ $item->ordercount }}</td>
                                        <td>{{ $item->price }}</td>
                                        <td><a href="{{route ('userOrderDetails', $item->order_code)}}">{{ $item->order_code }} </a></td>
                                    </tr>
                                @endforeach

                            </tbody>

                        </table>
                        {{-- <span class="d-flex justify-content-end">{{ $order->links()}}</span> --}}
                    </div>
                </div>
            </div>

        </div>
        <!-- /.container-fluid -->
@endsection
@section('js-section')
    <script>
        $(document).ready(function(){
            $('.statusChange').change(function(){
                $currentStatus = $(this).val();
                $orderCode = $(this).parents("tr").find('.orderCode').val();

                $data ={
                    'status' : $currentStatus,
                    'orderCode' : $orderCode
                }
                // console.log($data);
                $.ajax({
                    type : 'get',
                    url : 'change/status',
                    data  : $data,
                    dataType   : 'json'
                })

            })
        })
    </script>
@endsection
