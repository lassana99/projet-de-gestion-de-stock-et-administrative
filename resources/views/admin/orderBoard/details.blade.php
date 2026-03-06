@extends('admin.layouts.master')
@section('content')
        <!-- Begin Page Content -->
        <div class="container-fluid">
            <!-- Order Header -->
            <div class="row align-items-center">
                <div class="col-md-2">
                    <a href="{{ route('orderListPage') }}" class="btn btn-secondary w-100 mt-2">
                        <i class="fa-solid fa-arrow-left-long"></i> Retour
                    </a>
                </div>

            @foreach ($orderState as $item)
                <div class="col-md-2 mt-2">
                    <select class="form-control text-center font-weight-bold statusChange"
                            data-id="{{ $item->order_code }}"
                            style="background-color:rgb(223, 203, 28);">
                        <option value="0" @if ($item->status == 0) selected @endif>Pending</option>
                        <option value="1" @if ($item->status == 1) selected @endif>Confirmed</option>
                        <option value="2" @if ($item->status == 2) selected @endif>Reject</option>
                    </select>
                </div>
            @endforeach

            <!-- Reject Reason Modal (Only One) -->
            <div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="rejectModalLabel">Motif du rejet de la commande</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <textarea class="form-control" id="rejectNote" placeholder="Enter reason for rejection"></textarea>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                            <button type="button" class="btn btn-danger" id="confirmReject">Confirmer le rejet</button>
                        </div>
                    </div>
                </div>
            </div>

            </div>
             <!-- Order Board -->
             <div class="row mt-4">
                <div class="col-lg-10">
                    <div class="card shadow-sm">
                        <div class="card-header bg-white text-dark">
                            <h6 class="m-0">Order Items</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered text-center">
                                    <thead class="bg-secondary text-white">
                                        <tr class="text-white text-center">
                                            <th>Image</th>
                                            <th>Nom</th>
                                            <th>Nombre</th>
                                            <th>Prix du produit</th>
                                            <th>Prix total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($order as $item)
                                            <tr class="text-white text-center">
                                                <td>
                                                    <img class="img-thumbnail"
                                                        src="{{ asset('productImages/' . $item->productimage) }}"
                                                        style="width: 80px;">
                                                </td>
                                                <td>{{ $item->productname }}</td>
                                                <td>{{ $item->ordercount }}</td>
                                                <td>{{ $item->productprice }}</td>
                                                <td>{{ $item->ordercount * $item->productprice }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order & Payment Details -->
            <div class="row mt-4">
                <!-- Order Info Card -->
                <div class="col-lg-4 offset-1">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h6 class="font-weight-bold text-primary">Order Details</h6>
                            <hr>
                            <p><strong>Nom:</strong> {{ $order[0]->customername }}</p>
                            <p><strong>Téléphone:</strong> {{ $order[0]['phone'] }}</p>
                            <p><strong>Code de commande:</strong> {{ $order[0]->order_code }}</p>
                            <p><strong>Date de commande:</strong> {{ $order[0]->created_at->format('j-F-y') }}</p>
                            <p><strong>Prix total:</strong> {{ $total + 500 }} MMK</p>
                            <small class="text-danger">(Includes Delivery Charges)</small>
                        </div>
                    </div>
                </div>

                <!-- Payslip Card -->
                <div class="col-lg-5">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h6 class="font-weight-bold text-primary">Payment Details</h6>
                            <hr>
                            <p><strong>Contact:</strong> {{ $paySlipData->phone }}</p>
                            <p><strong>Method:</strong> {{ $paySlipData->payment_type }}</p>
                            <p><strong>Image:</strong> <div class="text-center mt-3">
                                <!-- Clickable Payslip Image -->
                                <a href="{{ asset('payslipRecords/' . $paySlipData->payslip_image) }}" target="_blank">
                                    <img src="{{ asset('payslipRecords/' . $paySlipData->payslip_image) }}"
                                        class="img-thumbnail"
                                        style="max-width: 100px; cursor: pointer;">
                                </a>
                            </div></p>

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- /.container-fluid -->
@endsection

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


@section('js-section')
<script>

$(document).ready(function () {
    $('.statusChange').on('change', function () {
        let status = $(this).val();
        let orderId = $(this).data('id');

        if (status == 2) { // If "Reject" is selected
            $('#rejectModal').modal('show');
        } else {
            // Send request to update status and remove reject reason
            $.ajax({
                url: '/admin/order/removereject',
                type: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    order_code: orderId,
                    status: status // Send the new status
                },
                success: function (response) {
                    console.log("Order status updated, reject reason removed.");
                },
                error: function () {
                    alert("Something went wrong!");
                }
            });
        }
    });

    $('#confirmReject').on('click', function () {
        let reason = $('#rejectNote').val();
        let orderId = $('.statusChange').data('id'); // Get order ID

        if (reason.trim() == "") {
            alert("Please provide a reason for rejection.");
            return;
        }

        $.ajax({
            url: '/admin/order/reject',
            type: 'POST',
            data: {
                _token: "{{ csrf_token() }}",
                order_code: orderId,
                reason: reason
            },
            success: function (response) {
                alert("Order Rejected Successfully!");
                $('#rejectModal').modal('hide');
            },
            error: function () {
                alert("Something went wrong!");
            }
        });
    });
});

</script>
@endsection


