@extends('admin.layouts.master')
@section('content')
    <section class="container-fluid">
        <div class="card">
            <div class="row justify-content-center align-items-center">
                <!-- Usage de la couleur de background -->
            <div class="card-body" style="background-color:#6c757d;"> 
            <div class="col-md-12">
                    <div class="row">
                        <div class="col-4">
                            <h5 class="intro-y text-lg text-white fw-bold font-medium mt-1 mb-1">
                                Détails du rapport des ventes
                            </h5>
                        </div>
                        <div class="col-8 mt-1">
                            <form action="{{ route('salesReport') }}" method="GET" class="mb-4 d-flex justify-content-end align-items-center">
                                <input type="date" name="start_date" class="form-control mx-2" value="{{ request('start_date') }}" style="max-width: 200px;">
                                <input type="date" name="end_date" class="form-control mx-2" value="{{ request('end_date') }}" style="max-width: 200px;">
                                <button type="submit" class="btn btn-dark text-dark fw-bold mx-2" style="background-color: #ffffff;">Filtrer</button>
                                <button type="button" class="btn" style="background-color: rgb(249, 138, 27); color: white;" onclick="exportTableToExcel('salesTable')">Exporter vers Excel</button>

                            </form>
                        </div>
                    </div>

                    <!-- Table of Daily Sales -->
                    <table class="table table-bordered" id="salesTable">
                        <thead class="table-white">
                            <tr class="text-white text-center" style="background-color:#6c757d;">
                                <th>ID de commande</th>
                                <th>Code de commande</th>
                                <th>Nom du produit</th>
                                <th>Prix</th>
                                <th>Quantité en stock</th>
                                <th>Quantité vendue</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(!empty($sales) && count($sales) > 0)
                                @foreach ($sales as $item)
                                    <tr class="text-white text-center">
                                        <td>{{ $item->order_id }}</td>
                                        <td>{{ $item->order_code }}</td>
                                        <td>{{ $item->productname }}</td>
                                        <td>{{ $item->price }}</td>
                                        <td>{{ $item->instock }}</td>
                                        <td>{{ $item->sold }}</td>
                                        <td>{{ $item->created_at->format('j-F-y') }}</td>
                                    </tr>
                                @endforeach
                            @else
                                <!-- Empty row to keep headers visible -->
                                <!-- Add a blank row for spacing -->
                                <tr>
                                    <td colspan="7">&nbsp;</td> <!-- Ligne vide pour espacer -->
                                </tr>
                                <!-- Message displayed below the header -->
                                <tr class="text-center" style="background-color:#6c757d;">
                                    <td colspan="7" style="
                                        color: #ffffff; 
                                        font-size: 16px; 
                                        font-weight: bold; 
                                        background-color:rgb(144, 142, 140);
                                        padding: 15px;">
                                        Aucun produit n'a été vendu durant cette période.
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>

                </div>
            </div>
 
            </div>
        </div>

    </section>

    <!-- Export to Excel Script -->
    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
    <script>
        function exportTableToExcel(tableId, filename = 'Sales_Report_Details.xlsx') {
            const table = document.getElementById(tableId);
            const workbook = XLSX.utils.table_to_book(table, {
                sheet: "Sheet1"
            });
            XLSX.writeFile(workbook, filename);
        }
    </script>

@endsection
