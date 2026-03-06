@extends('admin.layouts.master')
<style>
    .card-custom-purple {
    background-color: #7d6c70 !important;
    border-radius: 15px !important;
}
</style>
@section('content')
<div class="container-fluid">

    @if(auth()->user()->role === 'admin')
        <!-- ============================================================== -->
        <!-- VUE POUR LE RÔLE ADMINISTRATEUR                                -->
        <!-- ============================================================== -->
        <div class="row justify-content-center align-items-center" style="min-height: 70vh;">
    <div class="col-md-10 text-center">
        <!-- Fond gris (#6c757d) avec !important pour garantir l'affichage -->
        <div class="card shadow border-0 py-5 px-4" style="background: #6c757d !important; border-radius: 15px;">
            <div class="card-body">
                <div class="mb-4">
                    <!-- On reprend l'icône qui fonctionne et on ajoute 'fa-flip-horizontal' pour la tourner -->
                    <i class="fa-solid fa-person-walking-arrow-right fa-5x text-white fa-flip-horizontal"></i>
                </div>
                <h2 class="font-weight-bold text-white mb-3">
                    Bienvenue sur la plateforme de gestion des congés de façon transparente et rapide
                </h2>
                <p class="text-white lead" style="opacity: 0.9;">
                    Votre espace personnel est prêt! Utilisez le menu RH pour accéder à vos demandes et consultations.
                </p>
            </div>
        </div>
    </div>
</div>
    @else
        <!-- ============================================================== -->
        <!-- VUE COMPLÈTE (SUPERADMIN ET AUTRES RÔLES)                      -->
        <!-- ============================================================== -->

        <!-- Barre de Filtrage -->
        <div class="card mb-4 shadow-sm" style="border: 1px solid white;">
            <div class="card-body" style="background-color: #6c757d;">
                <form action="{{ url()->current() }}" method="GET" class="row align-items-end g-3">
                    <div class="col-md-3">
                        <label class="text-white font-weight-bold" style="font-size: 15px;">Type de vue</label>
                        <select name="filter_mode" id="filter_mode_select" class="form-control" onchange="toggleFilterFields(this.value)">
                            <option value="month" {{ $filterMode == 'month' ? 'selected' : '' }}>Mois spécifique</option>
                            <option value="range" {{ $filterMode == 'range' ? 'selected' : '' }}>Période personnalisée</option>
                            <option value="all" {{ $filterMode == 'all' ? 'selected' : '' }}>Tous les mois confondus</option>
                        </select>
                    </div>

                    <!-- Bloc : Sélection Mois/Année (Standard) -->
                    <div id="dateSelectors" class="row col-md-7 g-3" style="{{ ($filterMode == 'all' || $filterMode == 'range') ? 'display:none' : 'display:flex' }}">
                        <div class="col-md-6">
                            <label class="text-white font-weight-bold" style="font-size: 15px;">Mois</label>
                            <select name="month" class="form-control">
                                @for ($m=1; $m<=12; $m++)
                                    <option value="{{ $m }}" {{ $currentMonth == $m ? 'selected' : '' }}>
                                        {{ Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="text-white font-weight-bold" style="font-size: 15px;">Année</label>
                            <select name="year" class="form-control">
                                @for ($y=date('Y'); $y>=date('Y')-5; $y--)
                                    <option value="{{ $y }}" {{ $currentYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>

                    <!-- Bloc : Sélection Période (Dates début et fin) -->
                    <div id="rangeSelectors" class="row col-md-7 g-3" style="{{ $filterMode != 'range' ? 'display:none' : 'display:flex' }}">
                        <div class="col-md-6">
                            <label class="text-white font-weight-bold" style="font-size: 15px;">Date de début</label>
                            <input type="date" name="date_start" class="form-control" value="{{ $dateStart ?? '' }}">
                        </div>
                        <div class="col-md-6">
                            <label class="text-white font-weight-bold" style="font-size: 15px;">Date de fin</label>
                            <input type="date" name="date_end" class="form-control" value="{{ $dateEnd ?? '' }}">
                        </div>
                    </div>

                    <div class="col-md-2">
                        <button type="submit" class="btn btn-warning w-100 fw-bold" style="background-color: rgb(249, 138, 27); border:none; color:white;">
                            <i class="fa-solid fa-filter"></i> Filtrer
                        </button>
                    </div>
                </form>
            </div>
        </div>

        @php
            $calculDebits = ($totalRent ?? 0) + ($totalTaxes ?? 0) + ($totalUtilities ?? 0) + ($totalOthers ?? 0) + ($totalSalaries ?? 0) + ($totalInps ?? 0);
            // Modification ici : On utilise le CA des factures payées au lieu de la marge
            $calculCredits = $totalPaidCA ?? 0; 
            $soldeFinalValue = $calculCredits - $calculDebits;
        @endphp

        <!-- Ligne 1 : Produits, Achats, Devis, Factures -->
        <div class="row g-3 mb-3">
            <div class="col-xl-3 col-md-6">
                <a href="{{ route('productList') }}" class="text-decoration-none">
                    <div class="card border-left-warning shadow h-100">
                        <div class="card-body" style="background-color:#6c757d;">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="font-weight-bold text-warning text-uppercase mb-1" style="font-size: 0.85rem;">Produits</div>
                                    <div class="h5 mb-0 font-weight-bold text-white" data-count="{{ $totalProducts }}">0</div>
                                </div>
                                <div class="col-auto text-white"><i class="fa-solid fa-2x fa-layer-group"></i></div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-xl-3 col-md-6">
                <a href="{{ route('purchaseList') }}" class="text-decoration-none">
                    <div class="card border-left-warning shadow h-100">
                        <div class="card-body" style="background-color:#6c757d;">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="font-weight-bold text-warning text-uppercase mb-1" style="font-size: 0.85rem;">Achats</div>
                                    <div class="h5 mb-0 font-weight-bold text-white" data-count="{{ $totalPurchases }}">0</div>
                                </div>
                                <div class="col-auto text-white"><i class="fa-solid fa-2x fa-cart-shopping"></i></div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-xl-3 col-md-6">
                <a href="{{ route('devis.list') }}" class="text-decoration-none">
                    <div class="card border-left-warning shadow h-100">
                        <div class="card-body" style="background-color:#6c757d;">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="font-weight-bold text-warning text-uppercase mb-1" style="font-size: 0.85rem;">Devis</div>
                                    <div class="h5 mb-0 font-weight-bold text-white" data-count="{{ $devisCount }}">0</div>
                                </div>
                                <div class="col-auto text-white"><i class="fa-solid fa-2x fa-file-lines"></i></div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-xl-3 col-md-6">
                <a href="{{ route('invoices.list') }}" class="text-decoration-none">
                    <div class="card border-left-warning shadow h-100">
                        <div class="card-body" style="background-color:#6c757d;">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="font-weight-bold text-warning text-uppercase mb-1" style="font-size: 0.85rem;">Factures</div>
                                    <div class="h5 mb-0 font-weight-bold text-white" data-count="{{ $invoiceCount }}">0</div>
                                </div>
                                <div class="col-auto text-white"><i class="fa-solid fa-2x fa-file-invoice-dollar"></i></div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <!-- Ligne 2 : Partenaires et Loyer -->
        <div class="row g-3 mb-3">
            <div class="col-xl-3 col-md-6">
                <a href="{{ route('supplierList') }}" class="text-decoration-none">
                    <div class="card border-left-warning shadow h-100">
                        <div class="card-body" style="background-color:#6c757d;">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="font-weight-bold text-warning text-uppercase mb-1" style="font-size: 0.85rem;">Fournisseurs</div>
                                    <div class="h5 mb-0 font-weight-bold text-white" data-count="{{ $supplierCount }}">0</div>
                                </div>
                                <div class="col-auto text-white"><i class="fa-solid fa-2x fa-truck-field"></i></div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-xl-3 col-md-6">
                <a href="{{ route('customerList') }}" class="text-decoration-none">
                    <div class="card border-left-warning shadow h-100">
                        <div class="card-body" style="background-color:#6c757d;">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="font-weight-bold text-warning text-uppercase mb-1" style="font-size: 0.85rem;">Clients</div>
                                    <div class="h5 mb-0 font-weight-bold text-white" data-count="{{ $customerCount }}">0</div>
                                </div>
                                <div class="col-auto text-white"><i class="fa-solid fa-2x fa-users"></i></div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-xl-3 col-md-6">
                <a href="{{ route('prospectList') }}" class="text-decoration-none">
                    <div class="card border-left-warning shadow h-100">
                        <div class="card-body" style="background-color:#6c757d;">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="font-weight-bold text-warning text-uppercase mb-1" style="font-size: 0.85rem;">Prospects</div>
                                    <div class="h5 mb-0 font-weight-bold text-white" data-count="{{ $prospectCount }}">0</div>
                                </div>
                                <div class="col-auto text-white"><i class="fa-solid fa-2x fa-user-plus"></i></div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-xl-3 col-md-6">
                <a href="{{ route('rentList') }}" class="text-decoration-none">
                    <div class="card border-left-warning shadow h-100">
                        <div class="card-body" style="background-color:#6c757d;">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="font-weight-bold text-warning text-uppercase mb-1" style="font-size: 0.85rem;">Dépenses : Loyer</div>
                                    <div class="h5 mb-0 font-weight-bold text-white">{{ number_format($totalRent ?? 0, 0, ',', ' ') }} <small>FCFA</small></div>
                                </div>
                                <div class="col-auto text-white"><i class="fa-solid fa-2x fa-house-chimney"></i></div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <!-- Ligne 3 : Exploitation -->
        <div class="row g-3 mb-3">
            <div class="col-xl-3 col-md-6">
                <a href="{{ route('taxList') }}" class="text-decoration-none">
                    <div class="card border-left-warning shadow h-100">
                        <div class="card-body" style="background-color:#6c757d;">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="font-weight-bold text-warning text-uppercase mb-1" style="font-size: 0.85rem;">Dépenses : Impôts</div>
                                    <div class="h5 mb-0 font-weight-bold text-white">{{ number_format($totalTaxes ?? 0, 0, ',', ' ') }} <small>FCFA</small></div>
                                </div>
                                <div class="col-auto text-white"><i class="fa-solid fa-2x fa-scale-balanced"></i></div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-xl-3 col-md-6">
                <a href="{{ route('utilityList') }}" class="text-decoration-none">
                    <div class="card border-left-warning shadow h-100">
                        <div class="card-body" style="background-color:#6c757d;">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="font-weight-bold text-warning text-uppercase mb-1" style="font-size: 0.85rem;">Eau/Élec/Internet</div>
                                    <div class="h5 mb-0 font-weight-bold text-white">{{ number_format($totalUtilities ?? 0, 0, ',', ' ') }} <small>FCFA</small></div>
                                </div>
                                <div class="col-auto text-white"><i class="fa-solid fa-2x fa-bolt-lightning"></i></div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-xl-3 col-md-6">
                <a href="{{ route('otherExpenseList') }}" class="text-decoration-none">
                    <div class="card border-left-warning shadow h-100">
                        <div class="card-body" style="background-color:#6c757d;">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="font-weight-bold text-warning text-uppercase mb-1" style="font-size: 0.85rem;">Dépenses : Autres</div>
                                    <div class="h5 mb-0 font-weight-bold text-white">{{ number_format($totalOthers ?? 0, 0, ',', ' ') }} <small>FCFA</small></div>
                                </div>
                                <div class="col-auto text-white"><i class="fa-solid fa-2x fa-ellipsis-vertical"></i></div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-xl-3 col-md-6">
                <a href="{{ route('salaryList') }}" class="text-decoration-none">
                    <div class="card border-left-warning shadow h-100">
                        <div class="card-body" style="background-color:#6c757d;">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="font-weight-bold text-warning text-uppercase mb-1" style="font-size: 0.85rem;">Dépenses : Salaires</div>
                                    <div class="h5 mb-0 font-weight-bold text-white">{{ number_format($totalSalaries ?? 0, 0, ',', ' ') }} <small>FCFA</small></div>
                                </div>
                                <div class="col-auto text-white"><i class="fa-solid fa-2x fa-hand-holding-dollar"></i></div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <!-- Ligne 4 : Bilan Financier -->
        <div class="row g-3 mb-3">
            <div class="col-xl-3 col-md-6">
                <a href="{{ route('inpsList') }}" class="text-decoration-none">
                    <div class="card border-left-warning shadow h-100">
                        <div class="card-body" style="background-color:#6c757d;">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="font-weight-bold text-warning text-uppercase mb-1" style="font-size: 0.85rem;">Salaires/INPS</div>
                                    <div class="h5 mb-0 font-weight-bold text-white">{{ number_format($totalInps ?? 0, 0, ',', ' ') }} <small>FCFA</small></div>
                                </div>
                                <div class="col-auto text-white"><i class="fa-solid fa-2x fa-file-shield"></i></div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-xl-3 col-md-6">
                <a href="{{ route('accountBook') }}" class="text-decoration-none">
                    <div class="card shadow h-100" style="border-left: 0.25rem solid #9e2101;">
                        <div class="card-body" style="background-color:#6c757d;">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="font-weight-bold text-uppercase mb-1" style="font-size: 0.85rem; color: #9e2101;">DÉBIT (Total depenses)</div>
                                    <div class="h5 mb-0 font-weight-bold text-white">{{ number_format($calculDebits, 0, ',', ' ') }} <small>FCFA</small></div>
                                </div>
                                <div class="col-auto" style="color: #9e2101;"><i class="fa-solid fa-2x fa-money-bill-transfer"></i></div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-xl-3 col-md-6">
                <a href="{{ route('accountBook') }}" class="text-decoration-none">
                    <div class="card border-left-success shadow h-100">
                        <div class="card-body" style="background-color:#6c757d;">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="font-weight-bold text-success text-uppercase mb-1" style="font-size: 0.85rem;">CRÉDIT</div>
                                    <div class="h5 mb-0 font-weight-bold text-white">{{ number_format($calculCredits, 0, ',', ' ') }} <small>FCFA</small></div>
                                </div>
                                <div class="col-auto text-success"><i class="fa-solid fa-2x fa-vault"></i></div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-xl-3 col-md-6">
                <a href="{{ route('accountBook') }}" class="text-decoration-none">
                    <div class="card shadow h-100" style="border-left: 0.25rem solid {{ $soldeFinalValue >= 0 ? '#4e73df' : '#e17055' }};">
                        <div class="card-body" style="background-color:#6c757d;">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="font-weight-bold text-uppercase mb-1" style="font-size: 0.85rem; color: {{ $soldeFinalValue >= 0 ? '#36b9cc' : '#e17055' }};">SOLDE FINAL</div>
                                    <div class="h5 mb-0 font-weight-bold text-white">{{ number_format($soldeFinalValue, 0, ',', ' ') }} <small>FCFA</small></div>
                                </div>
                                <div class="col-auto" style="color: {{ $soldeFinalValue >= 0 ? '#36b9cc' : '#e17055' }};"><i class="fa-solid fa-2x fa-scale-unbalanced"></i></div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <!-- GRAPHES -->
        <div class="row g-3 mt-3">
            <div class="col-xl-8 col-lg-7 col-sm-12">
                <div class="card shadow mb-4" style="background-color:#FFFFFF;">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between" style="background-color:#6c757d; color:white;">
                        <h5 class="m-0 font-weight-bold">Performance : CA vs Marge</h5>
                    </div>
                    <div class="card-body" style="background-color:#FFFFFF; height:380px;">
                        <div class="chart-area" style="position: relative; height: 100%;">
                            <canvas id="invoicesChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4 col-lg-5 col-sm-12">
                <div class="card shadow mb-4" style="background-color:#FFFFFF;">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between" style="background-color:#6c757d; color:white;">
                        <h5 class="m-0 font-weight-bold">État des devis</h5>
                    </div>
                    <div class="card-body" style="background-color:#FFFFFF; height:380px;">
                        <div class="chart-pie pt-4 pb-2" style="position: relative; height: 100%;">
                            <canvas id="devisStatusChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mt-3">
            <div class="col-12">
                <div class="card shadow mb-4" style="background-color:#FFFFFF;">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between" style="background-color:#6c757d; color:white;">
                        <h5 class="m-0 font-weight-bold">Bilan Financier : Factures Payées vs Factures Impayées</h5>
                    </div>
                    <div class="card-body" style="background-color:#FFFFFF; height:450px;">
                        <div class="chart-area" style="position: relative; height: 100%;">
                            <canvas id="financialBalanceChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

</div>
@endsection

@section('js-section')
@if(auth()->user()->role !== 'admin')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0/dist/chartjs-plugin-datalabels.min.js"></script>

<script>
Chart.register(ChartDataLabels);

function toggleFilterFields(mode) {
    const dateSelectors = document.getElementById('dateSelectors');
    const rangeSelectors = document.getElementById('rangeSelectors');
    
    if (mode === 'range') {
        dateSelectors.style.display = 'none';
        rangeSelectors.style.display = 'flex';
    } else if (mode === 'all') {
        dateSelectors.style.display = 'none';
        rangeSelectors.style.display = 'none';
    } else {
        dateSelectors.style.display = 'flex';
        rangeSelectors.style.display = 'none';
    }
}

document.addEventListener("DOMContentLoaded", function() {

    // Compteurs animés
    document.querySelectorAll("[data-count]").forEach(element => {
        let count = 0;
        let target = parseInt(element.dataset.count);
        if(target === 0) { element.innerText = "0"; return; }
        let increment = Math.ceil(target / 50);
        let counter = setInterval(() => {
            count += increment;
            if (count >= target) {
                element.innerText = target;
                clearInterval(counter);
            } else {
                element.innerText = count;
            }
        }, 30);
    });

    const monthNames = ["Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre"];
    
    let currentPeriodLabel = "";
    const currentMode = "{{ $filterMode }}";
    if (currentMode === 'range') {
        currentPeriodLabel = "Du {{ \Carbon\Carbon::parse($dateStart)->format('d/m/Y') }} au {{ \Carbon\Carbon::parse($dateEnd)->format('d/m/Y') }}";
    } else if (currentMode === 'month') {
        currentPeriodLabel = monthNames[parseInt("{{ $currentMonth }}") - 1] + " " + "{{ $currentYear }}";
    } else {
        currentPeriodLabel = "Année " + "{{ $currentYear }}";
    }

    const handleHover = (event, chartElement) => {
        event.native.target.style.cursor = chartElement[0] ? 'pointer' : 'default';
    };

    // Graphe Performance
    const invoiceData = @json($invoiceOverview);
    const invoiceLabels = invoiceData.map(item => {
        if (item.month_label.includes('-')) {
            const parts = item.month_label.split('-');
            const monthIdx = parseInt(parts[1]) - 1;
            return monthNames[monthIdx].substring(0, 4) + " " + parts[0];
        }
        return item.month_label;
    });

    new Chart(document.getElementById("invoicesChart").getContext('2d'), {
        type: 'bar',
        data: {
            labels: invoiceLabels,
            datasets: [
                {
                    label: 'Chiffre d\'affaire',
                    data: invoiceData.map(item => Math.round(parseFloat(item.total_ht_after_discount || 0))),
                    backgroundColor: 'rgba(78, 115, 223, 0.8)',
                    borderRadius: 5,
                },
                {
                    label: 'Marge',
                    data: invoiceData.map(item => Math.round(parseFloat(item.total_profit || 0))),
                    backgroundColor: 'rgba(28, 200, 138, 0.8)',
                    borderRadius: 5,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            layout: { padding: { top: 30 } },
            onHover: handleHover,
            onClick: (evt, elements) => {
                if (elements.length > 0) {
                    const datasetIndex = elements[0].datasetIndex;
                    window.location.href = (datasetIndex === 1) ? "{{ route('machineList') }}" : "{{ route('invoices.list') }}";
                }
            },
            plugins: {
                legend: { position: 'top' },
                datalabels: {
                    anchor: 'end', align: 'top', color: '#444', font: { weight: 'bold', size: 10 },
                    formatter: (value) => value > 0 ? value.toLocaleString('fr-FR') + ' FCFA' : ''
                },
                tooltip: {
                    callbacks: {
                        title: () => currentPeriodLabel,
                        label: (context) => context.dataset.label + ' : ' + context.parsed.y.toLocaleString('fr-FR') + ' FCFA'
                    }
                }
            },
            scales: { y: { beginAtZero: true, grace: '15%', ticks: { callback: (v) => v.toLocaleString('fr-FR') } } }
        }
    });

    // Graphe Devis
    const devisStats = @json($devisStats);
    const mapFR = { 'pending':'En attente', 'sent':'Envoyé', 'accepted':'Accepté', 'rejected':'Annulé', 'draft':'Brouillon', 'abandoned':'Abandonné', 'invoiced':'Facturé' };
    const totalDevisCount = devisStats.reduce((acc, curr) => acc + curr.total, 0);

    new Chart(document.getElementById("devisStatusChart").getContext('2d'), {
        type: 'doughnut',
        data: {
            labels: devisStats.map(item => {
                let label = mapFR[item.status] || item.status;
                let percentage = totalDevisCount > 0 ? Math.round((item.total / totalDevisCount) * 100) : 0;
                return `${label} (${percentage}%)`; 
            }),
            datasets: [{
                data: devisStats.map(item => item.total),
                backgroundColor: ['#ffc107','#0dcaf0','#198754','#dc3545','#6c757d','#0ca25c','#fd7e14'],
                hoverOffset: 15
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            onHover: handleHover,
            onClick: () => { window.location.href = "{{ route('devis.list') }}"; },
            plugins: { legend: { position: 'bottom' }, datalabels: { display: false } }
        }
    });

    // Graphe Bilan Financier
    new Chart(document.getElementById("financialBalanceChart").getContext('2d'), {
        type: 'bar',
        data: {
            labels: [currentPeriodLabel + ' (Impayées)', currentPeriodLabel + ' (Payées)'],
            datasets: [
                {
                    label: 'Chiffre d\'Affaire (HTVA)',
                    data: [{{ $totalUnpaidCA ?? 0 }}, {{ $totalPaidCA ?? 0 }}],
                    backgroundColor: 'rgba(54, 162, 235, 0.7)',
                    borderRadius: 5,
                },
                {
                    label: 'Marge',
                    data: [{{ $totalUnpaidProfit ?? 0 }}, {{ $totalPaidProfit ?? 0 }}],
                    backgroundColor: 'rgba(255, 193, 7, 0.8)',
                    borderRadius: 5,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            layout: { padding: { top: 40 } },
            onHover: handleHover,
            onClick: (evt, elements) => {
                if (elements.length > 0) {
                    const datasetIndex = elements[0].datasetIndex;
                    window.location.href = (datasetIndex === 1) ? "{{ route('machineList') }}" : "{{ route('invoices.list') }}";
                }
            },
            plugins: {
                legend: { position: 'top' },
                datalabels: {
                    anchor: 'end', align: 'top', color: '#444', font: { weight: 'bold', size: 11 },
                    formatter: (value) => value > 0 ? value.toLocaleString('fr-FR') + ' FCFA' : ''
                },
                tooltip: {
                    callbacks: {
                        title: () => currentPeriodLabel,
                        label: (context) => context.dataset.label + ' : ' + context.parsed.y.toLocaleString('fr-FR') + ' FCFA'
                    }
                }
            },
            scales: { y: { beginAtZero: true, grace: '15%', ticks: { callback: (v) => v.toLocaleString('fr-FR') } } }
        }
    });
});
</script>
@endif
@endsection