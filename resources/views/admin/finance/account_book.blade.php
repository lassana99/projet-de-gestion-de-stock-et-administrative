@extends('admin.layouts.master')

@section('content')
<div class="container-fluid">
    {{-- Titre de la page --}}
    <h1 class="h3 mb-4 text-dark font-weight-bold">Livre de comptes</h1>

    <!-- Barre de Filtrage Complète -->
    <div class="card mb-4 shadow-sm" style="border: 1px solid white;">
        <div class="card-body" style="background-color: #6c757d;">
            <form action="{{ url()->current() }}" method="GET" class="row align-items-end g-3">
                
                {{-- Sélecteur de mode de filtrage --}}
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
                                <option value="{{ sprintf('%02d', $m) }}" {{ $month == $m ? 'selected' : '' }}>
                                    {{ Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="text-white font-weight-bold" style="font-size: 15px;">Année</label>
                        <select name="year" class="form-control">
                            @for ($y=date('Y'); $y>=date('Y')-5; $y--)
                                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
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
                    <button type="submit" class="btn fw-bold w-100" style="background-color: rgb(249, 138, 27); border:none; color:white;">
                        <i class="fa-solid fa-filter"></i> Filtrer
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tableau des mouvements -->
    <div class="card shadow mb-4" style="border: 1px solid white;">
        <div class="card-header py-3" style="background-color:#6c757d; border-bottom: 1px solid white;">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-white">
                    <i class="fa-solid fa-book-open me-2"></i> Journal des mouvements 
                    <small class="ml-2" style="color:#ffffff !important;">
                        (@if($filterMode == 'range') Du {{ Carbon\Carbon::parse($dateStart)->format('d/m/Y') }} au {{ Carbon\Carbon::parse($dateEnd)->format('d/m/Y') }} 
                         @elseif($filterMode == 'all') Année {{ $year }}
                         @else {{ Carbon\Carbon::create()->month((int)$month)->translatedFormat('F') }} {{ $year }} @endif)
                    </small>
                </h6>

                {{-- BOUTON EXPORT EXCEL --}}
                <a href="{{ route('accountBookExportExcel', request()->all()) }}" 
                   class="btn btn-light" style="font-weight: 500;">
                    <i class="fa-solid fa-file-excel text-success me-1"></i> Exporter en Excel
                </a>
            </div>
        </div>
        
        <div class="card-body" style="background-color:#6c757d;">
            <div class="table-responsive">
                <style>
                    #accountTable {
                        border: 1px solid white !important;
                        border-collapse: collapse;
                    }

                    #accountTable thead th, 
                    #accountTable tfoot td,
                    #accountTable tbody td {
                        white-space: nowrap;
                        text-align: center !important;
                        vertical-align: middle !important;
                        color: white !important;
                        border: 1px solid white !important;
                    }

                    #accountTable tbody tr:hover {
                        background-color: #5a6268 !important; 
                        cursor: pointer;
                    }

                    /* VOS COULEURS PRÉFÉRÉES REPRISES ICI */
                    .text-credit { color: #2ecc71 !important; font-weight: bold; }
                    .text-debit { color: #e74c3c !important; font-weight: bold; }
                    .text-solde-pos { color: #3498db !important; font-weight: bold; }
                    .text-solde-neg { color: #e67e22 !important; font-weight: bold; }
                </style>

                <table class="table table-bordered text-white" id="accountTable" width="100%" cellspacing="0">
                    <thead>
                        <tr class="bg-secondary">
                            <th style="width: 120px;">Date</th>
                            <th>Libellé</th>
                            <th style="width: 150px;">Crédit (+)</th>
                            <th style="width: 150px;">Débit (-)</th>
                            <th style="width: 180px;">Solde</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $runningBalance = 0; @endphp
                        @forelse($allEntries as $entry)
                            @php 
                                $runningBalance += ($entry['credit'] - $entry['debit']); 
                            @endphp
                            <tr>
                                <td>{{ Carbon\Carbon::parse($entry['date'])->format('d/m/Y') }}</td>
                                <td class="text-left px-3">{{ $entry['label'] }}</td>
                                <td class="text-credit">
                                    {{ $entry['credit'] > 0 ? number_format($entry['credit'], 0, ',', ' ') : '-' }}
                                </td>
                                <td class="text-debit">
                                    {{ $entry['debit'] > 0 ? number_format($entry['debit'], 0, ',', ' ') : '-' }}
                                </td>
                                <td class="{{ $runningBalance >= 0 ? 'text-solde-pos' : 'text-solde-neg' }}">
                                    {{ number_format($runningBalance, 0, ',', ' ') }} <small>FCFA</small>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4">
                                    Aucune donnée trouvée pour cette sélection.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr class="bg-secondary font-weight-bold">
                            <td colspan="2" class="text-right px-3 text-white">BILAN FINAL :</td>
                            <td class="text-credit">{{ number_format($allEntries->sum('credit'), 0, ',', ' ') }}</td>
                            <td class="text-debit">{{ number_format($allEntries->sum('debit'), 0, ',', ' ') }}</td>
                            <td class="{{ $runningBalance >= 0 ? 'text-solde-pos' : 'text-solde-neg' }}">
                                {{ number_format($runningBalance, 0, ',', ' ') }} FCFA
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
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

    document.addEventListener('DOMContentLoaded', () => {
        const rows = document.querySelectorAll('#accountTable tbody tr');
        rows.forEach((row, index) => {
            row.style.opacity = '0';
            row.style.transform = 'translateY(10px)';
            row.style.transition = 'all 0.3s ease';
            setTimeout(() => {
                row.style.opacity = '1';
                row.style.transform = 'translateY(0)';
            }, index * 30);
        });
    });
</script>
@endsection