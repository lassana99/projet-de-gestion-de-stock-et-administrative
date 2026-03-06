@extends('user.layouts.master')
@section('content')

{{-- On récupère le paramètre category_id de l'URL s'il existe --}}
@php
    $currentCategory = request()->route('category_id');
@endphp

    <div class="container-fluid page-header py-5">
        <h1 class="text-center text-white display-6">Achat</h1>
        <ol class="breadcrumb justify-content-center mb-0">
            <li class="breadcrumb-item"><a href="{{route('userDashboard')}}">Accueil</a></li>
            <li class="breadcrumb-item "><a href="#">Pages</a></li>
            <li class="breadcrumb-item active text-white">Achat</li>
        </ol>
    </div>
    <div class="container-fluid products">
        <div class="container py-3">
            <h1 class="mb-3 text-white">Choisissez vos produits</h1>
            <div class="row g-4">
                <div class="col-lg-12">
                    <div class="row g-4">
                        <div class="col-xl-3">
                            {{-- Le formulaire de recherche doit préserver le filtre de catégorie actuel --}}
                            <form action="{{ route('shopList', ['category_id' => $currentCategory]) }}" method="get">
                                @csrf
                                <div class="input-group w-100 mx-auto d-flex">
                                    <input type="search" class="form-control p-3" value="{{ request('searchKey') }}"
                                        name="searchKey" placeholder="keywords">
                                    <button type="submit" class="input-group-text p-3"><i
                                            class="fa fa-search"></i></button>
                                </div>
                            </form>
                        </div>

                        <div class="row g-4">
                            <div class="col-lg-3">
                                <div class="row g-4">
                                    <div class="col-lg-12">
                                        <div class="mb-3">
                                            <h4 class="text-white">Filtres produits</h4> {{-- Renommé pour clarté --}}
                                            <ul class="text-white list-unstyled products-categorie">
                                                <li>
                                                    <div class="d-flex justify-content-between products-name ">
                                                        {{-- Lien pour afficher tous les produits (sans filtre) --}}
                                                        <a href="{{ route('shopList') }}"><i class="fa-solid fa-clover"></i>
                                                            Tous les produits</a>
                                                    </div>
                                                </li>
                                                {{-- J'ai conservé $categories mais vous devriez la renommer dans votre contrôleur si le contenu est différent --}}
                                                @foreach ($categories as $filter) {{-- Utilisation de $filter au lieu de $item --}}
                                                    <li>
                                                        <div class="d-flex justify-content-between products-name">
                                                            {{-- $filter->id doit pointer vers l'ID de la catégorie/du filtre --}}
                                                            <a href="{{ route('shopList', $filter->id) }}"><i class="fa-solid fa-clover"></i> {{ $filter->name }}</a>
                                                            {{-- <span>(3)</span> --}}
                                                        </div>
                                                    </li>
                                                @endforeach

                                            </ul>
                                        </div>
                                    </div>


                                    <div class="col-lg-12">
                                        {{-- Le formulaire de prix doit préserver le filtre de catégorie actuel --}}
                                        <form action="{{ route('shopList', ['category_id' => $currentCategory]) }}" method="get">
                                            @csrf
                                            <p class="text-white">Prix</p>
                                            <input type="text" name="minPrice" value="{{ request('minPrice') }}"
                                                class="form-control my-2" placeholder="Minimum">
                                            <input type="text" name="maxPrice" value="{{ request('maxPrice') }}"
                                                class="form-control my-2" placeholder="Maximum">
                                            <input type="submit" class="btn-warning my-2" value="Filtrer">
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-9">
                                <div class="row g-4">
                                    @foreach ($products as $item)
                                        <div class="col-md-6 col-lg-4 col-xl-4">
                                            <div class="rounded position-relative products-item" style="background: linear-gradient(135deg, #ffd700, #191970);">
                                                <div class="products-img">
                                                    {{-- Ceci reste la bonne façon d'afficher les détails du produit --}}
                                                    <a href="{{ route('shopDetails', $item->id) }}">
                                                        <img style="height:250px"
                                                            src="{{ asset('productImages/' . $item->image) }}"
                                                            class="img-fluid w-100 rounded-top" alt="">
                                                    </a>
                                                </div>
                                                <div class="text-white bg-secondary px-3 py-1 rounded position-absolute"
                                                    style="top: 10px; left: 10px;">{{ $item->category_name }}</div>
                                                <div
                                                    class="p-4 text-white border border-secondary border-top-0 rounded-bottom">
                                                    <h4 class="text-white">{{ $item->name }}</h4>
                                                    <p>{{ Str::words($item->description, 10, '...') }}</p>

                                                    <div class="d-flex flex-lg-wrap">
                                                        <p class="text-white fs-5 fw-bold mb-2">{{ $item->price }} FCFA</p>
                                                        <form action="{{ route('addToCart') }}" method="POST">
                                                            @csrf
                                                            <input type="hidden" name="productID" value="{{ $item->id }}">
                                                            <input type="hidden" name="qty" value="1"> <button type="submit" class="btn border border-secondary rounded-pill px-3 text-primary">
                                                                <i class="fa fa-shopping-bag me-2 text-primary"></i> Ajouter au panier
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    @endforeach
                                    <div class="col-12">
                                        <div class="pagination d-flex justify-content-center mt-5">
                                            {{ $products->links() }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <a href="#" class="btn border-3 border-primary rounded-circle back-to-top"><i
                class="fa fa-arrow-up"></i></a>
    @endsection