@extends('admin.layouts.master')
@section('content')
    <section class="container-fluid">
        <div class="card">
            <div class="row justify-content-center align-items-center">
                <div class="card-body" style="background-color:#6c757d;">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-4">
                            <h3 class="intro-y text-lg text-white fw-bold font-medium mt-1 mb-1">
                                Rapport de profits et pertes
                            </h3>
                        </div>
                        <div class="col-8 mt-1">
                            <form action="{{ route('profitlossReport') }}" method="GET" class="mb-4 d-flex justify-content-end align-items-center">
                                <input type="date" name="start_date" class="form-control mx-2" value="{{ request('start_date') }}" style="max-width: 200px;">
                                <input type="date" name="end_date" class="form-control mx-2" value="{{ request('end_date') }}" style="max-width: 200px;">
                                <button type="submit" class="btn btn-dark text-dark fw-bold mx-2" style="background-color: #ffffff;">Filtrer</button>
                            <button type="button" class="btn" style="background-color: rgb(249, 138, 27); color: white;" onclick="exportTableToExcel('salesTable')">Exporter vers Excel</button>
                            </form>
                        </div>
                    </div>

                    <!-- Table of Profit & Loss Report -->
                    <table class="table table-bordered" id="salesTable">
                        <thead>
                            <tr class="text-white text-center">
                                <th>Nom du produit</th>
                                <th>Prix ​​de vente</th>
                                <th>Prix ​​d'achat</th>
                                <th>Quantité vendue</th>
                                <th>Profit</th>
                                <th>Chiffre d'affaire</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(!empty($productsales) && count($productsales) > 0)
                                @foreach ($productsales as $item)
                                    <tr class="text-white text-center">
                                        <td>{{ $item->name }}</td>
                                        <td>{{ number_format($item->sell_price, 2) }}</td>
                                        <td>{{ number_format($item->purchase_price, 2) }}</td>
                                        <td>{{ $item->units_sold }}</td>
                                        <td>{{ number_format($item->total_profit, 2) }}</td>
                                        <td>{{ number_format($item->total_ca, 2) }}</td> <!-- Chiffre d'affaire à regler -->
                                    </tr>
                                @endforeach
                            @else
                                <!-- Ligne vide pour espacer le message de l'entête -->
                                <tr><td colspan="6">&nbsp;</td></tr>

                                <!-- Message affiché si la liste est vide -->
                                <tr class="text-center">
                                    <td colspan="6" style="
                                        color: #ffffff; 
                                        font-size: 16px; 
                                        font-weight: bold; 
                                        background-color:rgb(144, 142, 140);
                                        padding: 15px;">
                                        Aucun rapport n'est disponible pour cette période.
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
    <!-- Export Excel -->
    <script>
        function exportTableToExcel(tableId, filename = 'Profit_Loss_Report_Details.xlsx') {
            const table = document.getElementById(tableId);
            const workbook = XLSX.utils.table_to_book(table, {
                sheet: "Sheet1"
            });
            XLSX.writeFile(workbook, filename);
        }
    </script>

@endsection
