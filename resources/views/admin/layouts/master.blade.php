<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Tableau de bord</title>

    <!-- Custom fonts -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
        integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Custom styles -->
    <link href="{{ asset('admin/css/sb-admin-2.min.css') }}" rel="stylesheet">
    <link href="{{ asset('admin/css/sb-admin-2.css') }}" rel="stylesheet">

    <style>
        /* 1. FIXER LA SIDEBAR ENTIÈRE AVEC DÉFILEMENT INTERNE */
        #accordionSidebar {
            position: -webkit-sticky;
            position: sticky;
            top: 0;
            height: 100vh;
            overflow-y: auto;
            z-index: 1070;
            scrollbar-width: none;
        }

        #accordionSidebar::-webkit-scrollbar {
            display: none;
        }

        /* 2. FIXER LE LOGO */
        .sidebar .sidebar-brand {
            position: -webkit-sticky;
            position: sticky;
            top: 0;
            z-index: 1080;
            background-color: #343a40;
            margin-bottom: 0;
            padding: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* 3. FIXER LA TOPBAR */
        .navbar {
            position: -webkit-sticky !important;
            position: sticky !important;
            top: 0 !important;
            z-index: 1060 !important;
            background-color: #ffffff !important;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15) !important;
        }

        /* 4. STYLE DES ICÔNES (TAILLE FIXE SANS DÉCALAGE) */
        .nav-item .btn {
            font-size: 1.08rem !important;
            font-weight: normal !important;
            color: white !important;
            transition: background-color 0.2s;
            border-left: 5px solid transparent !important; 
            border-radius: 0 5px 5px 0;
            text-align: left;
            width: 100%;
            white-space: nowrap; 
            margin-bottom: 2px;
        }

        .nav-item .btn.active {
            background-color: #1e3a8a !important; /* Bleu roi */
            border-left: 5px solid #ffc107 !important; /* Bordure dorée */
        }

        /* 5. STYLE DU SOUS-MENU UTILISATEURS (HAUTE LISIBILITÉ) */
        .submenu {
            background-color: #f8f9fc !important; 
            border-radius: 5px;
            margin: 0 10px 10px 15px;
            padding: 5px 0;
            list-style: none;
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.1);
        }

        .submenu .submenu-btn {
            display: block;
            padding: 8px 15px;
            color: #1e3a8a !important; 
            text-decoration: none !important;
            font-size: 0.95rem;
            font-weight: 600;
            border-left: 3px solid transparent;
            transition: all 0.2s;
        }
        
        .submenu .submenu-btn:hover, .submenu .submenu-btn.active-sub {
            background-color: #e2e6ea; 
            border-left: 3px solid #ffc107;
            color: #000000 !important;
        }

        /* 6. STYLE PERSONNALISÉ POUR LE MENU DÉROULANT PROFIL/DÉCONNEXION */
        .custom-dropdown-item {
            background-color: #6c757d !important; 
            color: white !important; 
            font-weight: bold;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            transition: background-color 0.2s;
        }

        .custom-dropdown-item:hover {
            background-color: #343a40 !important; 
            color: #ffc107 !important; 
        }

        .custom-dropdown-item i {
            color: white !important; 
        }

        /* Style spécifique pour les alertes de règlements */
        .settlement-alert {
            border-left: 5px solid !important;
            border-radius: 8px;
            margin-bottom: 15px;
            border: 1px solid rgba(0,0,0,0.1);
        }

        /* CLASSE POUR LE ORANGE PERSONNALISÉ rgb(249, 138, 27) */
        .alert-custom-orange {
            background-color: rgb(249, 138, 27) !important;
            color: white !important;
            border: none;
        }
        .alert-custom-orange strong, .alert-custom-orange i {
            color: white !important;
        }

        /* STRUCTURE */
        #content-wrapper { overflow: visible !important; }
        #wrapper { display: flex; overflow: visible !important; }

        @media (max-width: 992px) {
            .navbar-text { font-size: 1rem !important; }
        }
    </style>
</head>

<body id="page-top">
    <div id="wrapper">
        <button id="sidebarToggle" class="btn bg-dark d-lg-none">
            <i class="fa fa-bars"></i>
        </button>

        <!-- Sidebar -->
        <ul class="navbar-nav sidebar sidebar-dark accordion" id="accordionSidebar" style="background-color:#343a40;">

            <!-- LOGO FIGÉ -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="">
                <div class="sidebar-brand-icon w-100">
                    <img alt="Logo" class="logo__image w-100 h-auto" src="{{ asset('adminProfile/company-logo.png') }}">
                </div>
            </a>

            <div style="height: 10px;"></div>
            <hr class="sidebar-divider">
            
            {{-- SECTIONS RÉSERVÉES AU SUPERADMIN --}}
            @if(auth()->user()->role == 'superadmin')
                <li class="nav-item">
                    <a href="{{ route('adminDashboard') }}" class="btn text-start mb-2 text-white {{ Route::is('adminDashboard') ? 'active' : '' }}" style="background-color:#6c757d;">
                        <i class="fas fa-home"></i> Tableau de bord
                    </a>
                </li>

                <!-- LIEN VERS LES REGLEMENTS (DETTES / CREANCES) -->
                <li class="nav-item">
                    <a class="btn text-start mb-2 text-white {{ Request::is('admin/reglements*') ? 'active' : '' }}" href="{{ route('settlementList') }}" style="background-color:#6c757d;">
                        <i class="fa-solid fa-bullhorn"></i> Alerte
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="btn text-start mb-2 text-white {{ Route::is('articlePriceList') ? 'active' : '' }}" href="{{ route('articlePriceList') }}" style="background-color:#6c757d;">
                        <i class="fas fa-coins"></i> Prix Fournisseurs
                    </a>
                </li>
                <li class="nav-item">
                    <a class="btn text-start mb-2 text-white {{ Route::is('purchaseList') ? 'active' : '' }}" href="{{ route('purchaseList') }}" style="background-color:#6c757d;">
                        <i class="fas fa-shopping-bag"></i> Achats / Stocks
                    </a>
                </li>
                <li class="nav-item">
                    <a class="btn text-start mb-2 text-white {{ Route::is('funding.list') ? 'active' : '' }}" href="{{ route('funding.list') }}" style="background-color:#6c757d;">
                    <i class="fas fa-money-check-dollar"></i> Prêts bancaires
                    </a>
                </li>
                <li class="nav-item">
                    <a class="btn text-start mb-2 text-white {{ Route::is('machineList') ? 'active' : '' }}" href="{{ route('machineList') }}" style="background-color:#6c757d;">
                        <i class="fa-solid fa-magnifying-glass-chart"></i> Rentabilités
                    </a>
                </li>
                <li class="nav-item">
                    <a class="btn text-start mb-2 text-white {{ Route::is('productList') ? 'active' : '' }}" href="{{ route('productList') }}" style="background-color:#6c757d;">
                        <i class="fas fa-box"></i> Produits
                    </a>
                </li>
                <li class="nav-item">
                    <a class="btn text-start mb-2 text-white {{ Route::is('devis.list') ? 'active' : '' }}" href="{{ route('devis.list') }}" style="background-color:#6c757d;">
                        <i class="fas fa-file-invoice"></i> Devis
                    </a>
                </li>
                <li class="nav-item">
                    <a class="btn text-start mb-2 text-white {{ Route::is('invoices.list') ? 'active' : '' }}" href="{{ route('invoices.list') }}" style="background-color:#6c757d;">
                        <i class="fas fa-receipt"></i> Facture
                    </a>
                </li>
                <li class="nav-item">
                    <a class="btn text-start mb-2 text-white {{ Route::is('delivery_notes.list') ? 'active' : '' }}" href="{{ route('delivery_notes.list') }}" style="background-color:#6c757d;">
                        <i class="fas fa-shipping-fast"></i> BL
                    </a>
                </li>
                <li class="nav-item">
                    <a class="btn text-start mb-2 text-white {{ Route::is('paymentList') ? 'active' : '' }}" href="{{ route('paymentList') }}" style="background-color:#6c757d;">
                        <i class="fa-solid fa-dollar-sign"></i> Paiements
                    </a>
                </li>

                <!-- NOUVEAU MENU : LIVRE DE COMPTES -->
                <li class="nav-item">
                    <a class="btn text-start mb-2 text-white {{ Route::is('accountBook') ? 'active' : '' }}" href="{{ route('accountBook') }}" style="background-color:#6c757d;">
                        <i class="fa-solid fa-book"></i> Livre de comptes
                    </a>
                </li>

                <li class="nav-item">
                    <a class="btn text-start mb-2 text-white {{ Route::is('supplierList') ? 'active' : '' }}" href="{{ route('supplierList') }}" style="background-color:#6c757d;">
                        <i class="fa-solid fa-truck-field"></i> Fournisseurs
                    </a>
                </li>
                <li class="nav-item">
                    <a class="btn text-start mb-2 text-white {{ Route::is('customerList') ? 'active' : '' }}" href="{{ route('customerList') }}" style="background-color:#6c757d;">
                        <i class="fa-solid fa-users"></i> Clients
                    </a>
                </li>
                <li class="nav-item">
                    <a class="btn text-start mb-2 text-white {{ Route::is('prospectList') ? 'active' : '' }}" href="{{ route('prospectList') }}" style="background-color:#6c757d;">
                        <i class="fa-solid fa-user-plus"></i> Prospects
                    </a>
                </li>
            @endif

            <!-- SECTION RH (VISIBLE PAR SUPERADMIN ET ADMIN) -->
            <li class="nav-item flex-column">
                @php 
                    // Vérification des routes pour le Personnel
                    $isPersonnelRoute = Route::is('employeeList') || Route::is('employeeCreate') || Route::is('employeeEdit') || Route::is('employeeDetails');
                    
                    // Vérification des routes pour les Congés
                    $isLeaveRoute = Route::is('leaveList') || Route::is('leaveCreate') || Route::is('leaveEdit') || Route::is('leaveDetails') || Route::is('leaveUpdateStatus');
                    
                    // Le menu "RH" est actif si l'un des sous-menus (Personnel ou Congés) est actif
                    $isRhActive = $isPersonnelRoute || $isLeaveRoute; 
                @endphp

                <a class="btn text-start mb-2 text-white toggle-submenu {{ $isRhActive ? 'active' : '' }}" href="#" style="background-color:#6c757d;">
                    <i class="fa-solid fa-users-gear me-1"></i> RH
                </a>

                <ul class="submenu list-unstyled {{ $isRhActive ? '' : 'd-none' }}">
                    {{-- PERSONNEL --}}
                    <li>
                        <a class="submenu-btn {{ $isPersonnelRoute ? 'active-sub' : '' }}" href="{{ route('employeeList') }}">
                            <i class="fa-solid fa-user-group me-1"></i> Personnel
                        </a>
                    </li>

                    {{-- GESTION DES CONGÉS --}}
                    <li>
                        <a class="submenu-btn {{ $isLeaveRoute ? 'active-sub' : '' }}" href="{{ route('leaveList') }}">
                            <i class="fa-solid fa-calendar-check me-1"></i> Gestion de Congés
                        </a>
                    </li>
                </ul>
            </li>

            {{-- SECTION DÉPENSES (RÉSERVÉE AU SUPERADMIN) --}}
            @if(auth()->user()->role == 'superadmin')
                <li class="nav-item flex-column">
                    @php 
                        // Vérification des routes pour le Loyer
                        $isRentRoute = Route::is('rentList') || Route::is('rentCreate') || Route::is('rentEdit') || Route::is('rentDetails');
                        
                        // Vérification des routes pour les Impôts
                        $isTaxRoute = Route::is('taxList') || Route::is('taxCreate') || Route::is('taxEdit') || Route::is('taxDetails');

                        // Vérification des routes pour Eau / Élec / Internet
                        $isUtilityRoute = Route::is('utilityList') || Route::is('utilityCreate') || Route::is('utilityEdit') || Route::is('utilityDetails');
                        
                        // Vérification des routes pour les Salaires
                        $isSalaryRoute = Route::is('salaryList') || Route::is('salaryCreate') || Route::is('salaryEdit') || Route::is('salaryDetails');

                        // Vérification des routes pour Salaire INPS
                        $isInpsRoute = Route::is('inpsList') || Route::is('inpsCreate') || Route::is('inpsEdit') || Route::is('inpsDetails');

                        // Vérification des routes pour Autres Dépenses
                        $isOtherExpenseRoute = Route::is('otherExpenseList') || Route::is('otherExpenseCreate') || Route::is('otherExpenseEdit') || Route::is('otherExpenseDetails');

                        // Le menu "Dépenses" est actif si l'un des sous-menus est actif
                        $isExpenseActive = $isRentRoute || $isTaxRoute || $isUtilityRoute || $isSalaryRoute || $isInpsRoute || $isOtherExpenseRoute; 
                    @endphp

                    <a class="btn text-start mb-2 text-white toggle-submenu {{ $isExpenseActive ? 'active' : '' }}" href="#" style="background-color:#6c757d;">
                        <i class="fa-solid fa-money-bill-transfer me-1"></i> Dépenses
                    </a>

                    <ul class="submenu list-unstyled {{ $isExpenseActive ? '' : 'd-none' }}">
                        {{-- LOYER --}}
                        <li>
                            <a class="submenu-btn {{ $isRentRoute ? 'active-sub' : '' }}" href="{{ route('rentList') }}">
                                <i class="fa-solid fa-house-chimney me-1"></i> Loyer
                            </a>
                        </li>

                        {{-- IMPÔTS --}}
                        <li>
                            <a class="submenu-btn {{ $isTaxRoute ? 'active-sub' : '' }}" href="{{ route('taxList') }}">
                                <i class="fa-solid fa-scale-balanced me-1"></i> Impôts
                            </a>
                        </li>

                        {{-- EAU / ÉLEC / INTERNET --}}
                        <li>
                            <a class="submenu-btn {{ $isUtilityRoute ? 'active-sub' : '' }}" href="{{ route('utilityList') }}">
                                <i class="fa-solid fa-bolt-lightning me-1"></i> Eau/Élec/Internet
                            </a>
                        </li>

                        {{-- SALAIRE --}}
                        <li>
                            <a class="submenu-btn {{ $isSalaryRoute ? 'active-sub' : '' }}" href="{{ route('salaryList') }}">
                                <i class="fa-solid fa-hand-holding-dollar me-1"></i> Salaire
                            </a>
                        </li>

                        {{-- SALAIRE INPS --}}
                        <li>
                            <a class="submenu-btn {{ $isInpsRoute ? 'active-sub' : '' }}" href="{{ route('inpsList') }}">
                                <i class="fa-solid fa-shield-halved me-1"></i> Salaire INPS
                            </a>
                        </li>

                        {{-- AUTRES --}}
                        <li>
                            <a class="submenu-btn {{ $isOtherExpenseRoute ? 'active-sub' : '' }}" href="{{ route('otherExpenseList') }}">
                                <i class="fa-solid fa-ellipsis-vertical me-1"></i> Autres
                            </a>
                        </li>
                    </ul>
                </li>
            @endif

            <!-- SECTION UTILISATEURS (RÉSERVÉE AU SUPERADMIN) -->
            @if (auth()->user()->role == 'superadmin')
                <li class="nav-item flex-column">
                    @php $isUserRoute = Route::is('createAdminAccount') || Route::is('resetPasswordPage') || Route::is('adminList'); @endphp
                    <a class="btn text-start mb-2 text-white toggle-submenu {{ $isUserRoute ? 'active' : '' }}" href="#" style="background-color:#6c757d;">
                        <i class="fa-solid fa-users"></i> Utilisateurs
                    </a>
                    <ul class="submenu list-unstyled {{ $isUserRoute ? '' : 'd-none' }}">
                        <li>
                            <a class="submenu-btn {{ Route::is('createAdminAccount') ? 'active-sub' : '' }}" href="{{ route('createAdminAccount') }}">
                                <i class="fa-solid fa-user-plus"></i> Ajouter
                            </a>
                        </li>
                        <li>
                            <a class="submenu-btn {{ Route::is('resetPasswordPage') ? 'active-sub' : '' }}" href="{{ route('resetPasswordPage') }}">
                                <i class="fas fa-lock"></i> Mot de passe
                            </a>
                        </li>
                        <li>
                            <a class="submenu-btn {{ Route::is('adminList') ? 'active-sub' : '' }}" href="{{ route('adminList') }}">
                                <i class="fa-solid fa-user-tie"></i> Profils
                            </a>
                        </li>
                    </ul>
                </li>
            @endif

            <hr class="sidebar-divider my-2">

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <li class="nav-item" style="padding-bottom: 20px;">
                    <button type="submit" class="btn text-start mb-2 text-white w-100" style="background-color:#6c757d;">
                        <i class="fas fa-sign-out-alt"></i> Déconnexion
                    </button>
                </li>
            </form>
        </ul>

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column" style="background-color: #f8f9fc; width: 100%;">
            <div id="content">
                <nav class="navbar navbar-expand navbar-light bg-white shadow mb-4 px-3 sticky-top">
                    {{-- MASQUÉ POUR LE RÔLE ADMIN --}}
                    @if(auth()->user()->role !== 'admin')
                    <span class="navbar-text me-auto" style="color: #6c757d; font-size: 1.4rem; font-weight: 700; margin-left: 30px;">
                        Bienvenue sur la plateforme de gestion d'achats et ventes d'ApproLog
                    </span>
                    @endif

                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-dark font-weight-bold">{{ auth()->user()->name ?? auth()->user()->nickname }}</span>
                                <img class="rounded-circle shadow-sm" width="40" height="40" src="{{ auth()->user()->profile ? asset('adminProfile/' . auth()->user()->profile) : asset('admin/img/undraw_profile.svg') }}">
                            </a>
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in p-0" aria-labelledby="userDropdown" style="border: none;">
                                <a class="dropdown-item custom-dropdown-item py-2" href="{{ route('profileDetails') }}">
                                    <i class="fas fa-user fa-sm fa-fw mr-2"></i> Profil
                                </a>
                                <form method="POST" action="{{ route('logout') }}" class="m-0">
                                    @csrf
                                    <button type="submit" class="dropdown-item custom-dropdown-item py-2" style="border-bottom: none;">
                                        <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2"></i> Déconnexion
                                    </button>
                                </form>
                            </div>
                        </li>
                    </ul>
                </nav>
                
                <!-- BLOC DES ALERTES DE RÈGLEMENTS (RÉSERVÉ AU SUPERADMIN) -->
                @if(Route::is('adminDashboard') && auth()->user()->role == 'superadmin')
                    @php
                        $settlementAlerts = \App\Models\Settlement::where('status', 'pending')
                                    ->orderBy('due_date', 'asc')
                                    ->get();
                    @endphp

                    @if($settlementAlerts->count() > 0)
                        <div class="container-fluid mt-2">
                            <audio id="alertNotificationSound" preload="auto">
                                <source src="{{ asset('admin/audio/notification.mp3') }}" type="audio/mpeg">
                            </audio>

                            @foreach($settlementAlerts as $alert)
                                @php
                                    $daysRemaining = (int) now()->startOfDay()->diffInDays($alert->due_date->startOfDay(), false);
                                    // Rouge si retard (< 0), Orange personnalisé si aujourd'hui ou à venir (>= 0)
                                    $alertClass = ($daysRemaining < 0) ? 'danger' : 'custom-orange';
                                @endphp
                                
                                <div class="alert alert-{{ $alertClass }} settlement-alert alert-dismissible fade show shadow-sm" role="alert">
                                    <i class="fa-solid fa-triangle-exclamation mr-2"></i>
                                    <strong>Rappel de règlement :</strong> 
                                    @if($alert->type == 'debt')
                                        Le client <strong>{{ $alert->entity_name }}</strong> doit régler 
                                        <strong>{{ number_format($alert->amount, 0, '', ' ') }} {{ $alert->currency }}</strong>
                                        @if($alert->currency !== 'FCFA')
                                            ({{ number_format($alert->amount_fcfa, 0, '', ' ') }} FCFA)
                                        @endif
                                        avant le <strong>{{ $alert->due_date->format('d/m/Y') }}</strong>
                                    @else
                                        Nous devons régler 
                                        <strong>{{ number_format($alert->amount, 0, '', ' ') }} {{ $alert->currency }}</strong>
                                        @if($alert->currency !== 'FCFA')
                                            ({{ number_format($alert->amount_fcfa, 0, '', ' ') }} FCFA)
                                        @endif
                                        à <strong>{{ $alert->entity_name }}</strong> avant le <strong>{{ $alert->due_date->format('d/m/Y') }}</strong>
                                    @endif
                                    
                                    <span class="mx-2"></span> 
                                    <span class="badge badge-dark ml-2">
                                        @if($daysRemaining < 0)
                                            Retard de {{ abs($daysRemaining) }} jours
                                        @elseif($daysRemaining == 0)
                                            C'est aujourd'hui !
                                        @else
                                            Dans {{ $daysRemaining }} jours
                                        @endif
                                    </span>
                                    
                                    <form action="{{ route('settlementUpdateStatus', $alert->id) }}" method="POST" class="d-inline ml-3">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-success font-weight-bold">Marquer comme payé</button>
                                    </form>
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close" style="color:white;">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endforeach
                        </div>

                        <script>
                            document.addEventListener("DOMContentLoaded", function() {
                                const audio = document.getElementById('alertNotificationSound');
                                if (!sessionStorage.getItem('dashboard_sound_played')) {
                                    const playSound = () => {
                                        audio.play().then(() => {
                                            sessionStorage.setItem('dashboard_sound_played', 'true');
                                        }).catch(error => { console.log("Autoplay bloqué."); });
                                    };
                                    playSound();
                                    document.addEventListener('click', function() {
                                        if (!sessionStorage.getItem('dashboard_sound_played')) {
                                            audio.play().then(() => { sessionStorage.setItem('dashboard_sound_played', 'true'); });
                                        }
                                    }, { once: true });
                                }
                            });
                        </script>
                    @endif
                @else
                    <script>
                        sessionStorage.removeItem('dashboard_sound_played');
                    </script>
                @endif

                <div class="container-fluid">
                    @yield('content')
                </div>
                @include('sweetalert::alert')
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('admin/vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('admin/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('admin/vendor/jquery-easing/jquery.easing.min.js') }}"></script>
    <script src="{{ asset('admin/js/sb-admin-2.min.js') }}"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const activeItem = document.querySelector('.nav-item .btn.active');
            if (activeItem) {
                activeItem.scrollIntoView({ behavior: 'auto', block: 'center' });
            }

            $(".toggle-submenu").click(function(e) {
                e.preventDefault();
                $(this).next(".submenu").toggleClass('d-none');
            });
        });
    </script>
</body>
@yield('js-section')
</html>