<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Approlog</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Raleway:wght@600;800&display=swap" rel="stylesheet">

    <!-- Icon Font Stylesheet -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="{{asset ('customer/lib/lightbox/css/lightbox.min.css')}}" rel="stylesheet">
    <link href="{{asset ('customer/lib/owlcarousel/assets/owl.carousel.min.css')}}" rel="stylesheet">


    <!-- Customized Bootstrap Stylesheet -->
    <link href="{{asset ('customer/css/bootstrap.min.css')}}" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="{{asset ('customer/css/style.css')}}" rel="stylesheet">

    <!-- {{-- custom css link --}} -->
    <link rel="stylesheet" href="{{asset ('customer/css/custom.css')}}">
</head>

    <body>

        <!-- Spinner Start -->
        <div id="spinner" class="show w-100 vh-100 position-fixed top-0 start-0 d-flex align-items-center justify-content-center bg-white bg-opacity-75" style="z-index: 1050;">
            <div class="pulse-loader"></div>
        </div>
        <!-- Spinner End -->


        <!-- Navbar start -->
        <div class="container-fluid fixed-top">
            <div class="container topbar d-none d-lg-block" style="background-color: #191970">
                <div class="d-flex justify-content-between">
                <div class="top-info ps-2">
                    <small class="me-3"
                    ><i class="fas fa-map-marker-alt me-2 text-secondary"></i>
                    <a href="#" class="text-white">123 Street, New York</a></small
                    >
                    <small class="me-3"
                    ><i class="fas fa-envelope me-2 text-secondary"></i
                    ><a href="#" class="text-white">Email@Example.com</a></small
                    >
                </div>
                <div class="top-link pe-2">
                    <a href="#" class="text-white"
                    ><small class="text-white mx-2">politique de confidentialité</small>/</a
                    >
                    <a href="#" class="text-white"
                    ><small class="text-white mx-2">Conditions d'utilisation</small>/</a
                    >
                    <a href="#" class="text-white"
                    ><small class="text-white ms-2">Ventes et remboursements</small></a
                    >
                </div>
                </div>
            </div>

            <div class="container px-0">
                <nav class="navbar navbar-light bg-white navbar-expand-xl">
                           <a href="index.html" class="navbar-brand"
                                ><h1 class="text-dark display-6">FLORAL</h1></a>
                            <button class="navbar-toggler py-1 px-3 border-1" type="button"
                                data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
                                <span class="fa fa-bars"></span>
                            </button>

                            <div class="collapse navbar-collapse bg-white" id="navbarCollapse">
                                <div class="navbar-nav mx-auto">
                                    <a href="{{route('userDashboard')}}" class="nav-item nav-link active text-dark fw-semibold">Accueil</a>
                                    <a href="{{route('shopList')}}" class="nav-item nav-link text-dark fw-semibold">Achats</a>
                                    <a href="{{route('contactUs')}}" class="nav-item nav-link text-dark fw-semibold">Contact</a>
                                    <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                        @csrf
                                        <input type="submit" value="Logout" class="btn btn-warning rounded-pill px-4">
                                    </form>
                                </div>

                                <div class="d-flex align-items-center gap-4">
                                    <a href="{{route('cart')}}" class="text-dark position-relative">
                                        <i class="fa-solid fa-cart-shopping fa-2x"></i>
                                        @if($cartCount > 0)
                                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                                {{ $cartCount }}
                                            </span>
                                        @endif
                                    </a>
                                    <a href="{{route('orderList')}}" class="text-dark position-relative">
                                        <i class="fa fa-shopping-bag fa-2x"></i>
                                    </a>

                                    <div class="dropdown">
                                        <a href="#" class="d-flex align-items-center text-dark dropdown-toggle" id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="fas fa-user fa-2x me-2"></i>
                                            <span class="fw-semibold">
                                                @if (auth()->user()->name != null)
                                                    {{auth()->user()->name}}
                                                @else
                                                    {{auth()->user()->nickname}}
                                                @endif
                                            </span>
                                        </a>
                                        <ul class="dropdown-menu dropdown-menu-end shadow-sm rounded-3">
                                            <li><a class="dropdown-item" href="{{route('userProfileDetails')}}">Profil</a></li>
                                            <li><a class="dropdown-item" href="{{route('changePassword')}}">Changer le mot de passe</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                </nav>
            </div>
        </div>

        @yield('content')

    <!-- Footer Start -->
    <div class="container-fluid text-white-50 footer pt-5 mt-5" style="background-color:rgb(9, 9, 71);">
        <div class="container py-5">
            <div class="pb-4 mb-4" style="border-bottom: 1px solid rgba(226, 175, 24, 0.5) ;">
                <div class="row g-4">
                    <div class="col-lg-3">
                        <a href="#">
                            <h1 class="text-white mb-0">L'équipement Numéro 1</h1>
                            <p class="text-secondary mb-0">depuis 1995</p>
                        </a>
                    </div>
                    <div class="col-lg-6">
                        <div class="position-relative mx-auto">
                            <input class="form-control border-0 w-100 py-3 px-4 rounded-pill" type="number" placeholder="Your Email">
                            <button type="submit" class="btn btn-primary border-0 border-secondary py-3 px-4 position-absolute rounded-pill text-white" style="top: 0; right: 0;">Abonnez-vous maintenant</button>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="d-flex justify-content-end pt-3">
                            <a class="btn  btn-outline-secondary me-2 btn-md-square rounded-circle" href="https://www.tiktok.com/@sourcecode"><i class="fab fa-tiktok"></i></a>
                            <a class="btn btn-outline-secondary me-2 btn-md-square rounded-circle" href="https://www.facebook.com/joincoder404"><i class="fab fa-facebook-f"></i></a>
                            <a class="btn btn-outline-secondary me-2 btn-md-square rounded-circle" href="https://www.youtube.com/@joincoder"><i class="fab fa-youtube"></i></a>
                            <a class="btn btn-outline-secondary btn-md-square rounded-circle" href="https://t.me/reansourcecode"><i class="fab fa-telegram"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row g-5">
                <div class="col-lg-3 col-md-6">
                    <div class="footer-item">
                        <h4 class="text-light mb-3">Pourquoi les gens nous aiment !</h4>
                        <p class="mb-4">typesetting, remaining essentially unchanged. It was
                            popularised in the 1995s with the like Aldus PageMaker including of Lorem Ipsum.</p>
                        <a href="" class="btn border-secondary py-2 px-4 rounded-pill text-white">En savoir plus</a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="d-flex flex-column text-start footer-item">
                        <h4 class="text-light mb-3">Informations sur nous</h4>
                        <a class="btn-link text-white" href="">À propos de nous</a>
                        <a class="btn-link text-white" href="">Contactez-nous</a>
                        <a class="btn-link text-white" href="">politique de confidentialité</a>
                        <a class="btn-link text-white" href="">Conditions générales</a>
                        <a class="btn-link text-white" href="">Politique de retour</a>
                        <a class="btn-link text-white" href="">FAQ et aide</a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="d-flex flex-column text-start footer-item">
                        <h4 class="text-light mb-3">Compte</h4>
                        <a class="btn-link text-white" href="">Mon compte</a>
                        <a class="btn-link text-white" href="">Détails de l'achat</a>
                        <a class="btn-link text-white" href="">Panier</a>
                        <a class="btn-link text-white" href="">Liste de souhaits</a>
                        <a class="btn-link text-white" href="">historique des commandes</a>
                        <a class="btn-link text-white" href="">Commandes internationales</a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="footer-item">
                        <h4 class="text-light mb-3">Contact</h4>
                        <p>Adresse: 1123 Pyay Rd, Yangon</p>
                        <p>Email: florisygn@gmail.com</p>
                        <p>Téléphonehone: +0124567 8910</p>
                        <p>Paiement accepté</p>
                        <img src="img/payment.png" class="img-fluid" alt="">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Footer End -->

    <!-- Copyright Start -->
    <div class="container-fluid copyright py-4" style="background-color: rgb(9, 9, 71);">
        <div class="container">
            <div class="row">
                <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                    <span class="text-light"><a href="#"><i class="fas fa-copyright text-light me-2"></i>Approlog</a>, Tous droits réservés.</span>
                </div>
                <div class="col-md-6 my-auto text-center text-md-end text-white">
                Conçu par Lassana DEMBE <a class="border-bottom" href=""></a> Distributed By.....<a class="border-bottom" href=""></a>
                </div>
            </div>
        </div>
    </div>
    <!-- Copyright End -->

    <!-- JavaScript Libraries -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>

    <script src="{{asset ('customer/lib/easing/easing.min.js')}}"></script>
    <script src="{{asset ('customer/lib/waypoints/waypoints.min.js')}}"></script>
    <script src="{{asset ('customer/lib/lightbox/js/lightbox.min.js')}}"></script>
    <script src="{{asset ('customer/lib/owlcarousel/owl.carousel.min.js')}}"></script>

    <!-- Template Javascript -->
    <script src="{{asset ('customer/js/main.js')}}"></script>

    <script>
        function loadFile(event) {
            var reader = new FileReader();

            reader.onload = function() {
                var output = document.getElementById('output');

                output.src = reader.result;
            }
            reader.readAsDataURL(event.target.files[0])
        }


    </script>
    </body>

    @yield('js-section')

</html>
