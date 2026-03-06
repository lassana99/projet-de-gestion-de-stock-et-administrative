@extends('user.layouts.master')

@section('content')
    <!-- Single Page Header start -->
    <div class="container-fluid page-header py-5">
        <h1 class="text-center text-white display-6">Panier</h1>
        <ol class="breadcrumb justify-content-center mb-0">
            <li class="breadcrumb-item"><a href="{{route('userDashboard')}}">Accueil</a></li>
            <li class="breadcrumb-item"><a href="#">Pages</a></li>
            <li class="breadcrumb-item active text-white">Panier</li>
        </ol>
    </div>
    <!-- Single Page Header End -->

    <div class="card-body" >
        <div class="card shadow mb-4" style="background: linear-gradient(135deg, #191970, #ffd700);">
            <div class="container py-5 text-white">
                <div class="row g-4 mb-5">
                    <div class="col-lg-8 offset-2">
                        <a href="{{ route('shopList') }}" class="fs-6 fw-bold text-white"><i class="fa-solid fa-arrow-left m-3"></i>Retour</a>
                        <div class="row g-4">
                            <div class="col-lg-6">
                                <div class="border rounded">
                                    <a href="#">
                                        <img src="{{ asset('productImages/' . $product->image) }}" class="img-fluid rounded"
                                            alt="Image">
                                    </a>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <h4 class="text-white fw-bold mb-3">{{ $product->name }}</h4>
                                <p class="mb-3">Category: {{ $product->category_name }}</p>
                                <h5 class="text-white fw-bold mb-3">{{ $product->price }} FCFA</h5>
                                <div class="d-flex mb-4">
                                    @php
                                        $stars = number_format($productRating);
                                    @endphp
                                    @for ($i = 1; $i <= $stars; $i++)
                                        <i class="fa fa-star text-secondary"></i>
                                    @endfor

                                    @for ($j = $stars + 1; $j <= 5; $j++)
                                        <i class="fa fa-star"></i>
                                    @endfor
                                    <span class=" ms-2">{{ $ratingCount->count() }} Notes</span>
                                </div>

                                <p class="mb-4">{{ $product->description }}</p>
                                    <form action="{{ route('addToCart') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="productID" value="{{ $product->id }}">

                                        <div class="input-group quantity mb-5" style="width: 100px;">
                                            <div class="input-group-btn">
                                                <button type="button"
                                                    class="btn btn-sm btn-minus rounded-circle bg-light border">
                                                    <i class="fa fa-minus"></i>
                                                </button>
                                            </div>

                                            <input type="text" name="qty"
                                                class="form-control form-control-sm text-center border-0" value="1">
                                            <div class="input-group-btn">
                                                <button type="button"
                                                    class="btn btn-sm btn-plus rounded-circle bg-light border">
                                                    <i class="fa fa-plus"></i>
                                                </button>
                                            </div>
                                        </div>
                                        {{-- @foreach ($checkStock as $item) --}}
                                        <button type="submit"
                                            class="btn border border-secondary rounded-pill px-4 py-2 mb-4 text-primary"
                                            @if ($product->remaining_stock == 0) disabled @endif>
                                            <i class="fa fa-shopping-bag me-2 text-primary"></i>
                                            Add to cart
                                        </button>
                                        {{-- @endforeach --}}
                                    </form>


                                <!-- {{-- rating start --}} -->

                                <!-- Button trigger modal -->
                                <form action="{{ route('addRating') }}" method="POST">
                                    @csrf
                                    <button type="button" class="btn btn-secondary" data-bs-toggle="modal"
                                        data-bs-target="#exampleModal">
                                        Évaluer ce produit
                                    </button>

                                    <!-- Modal -->
                                    <div class="modal fade" id="exampleModal" tabindex="-1"
                                        aria-labelledby="exampleModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h1 class="modal-title" id="exampleModalLabel">Évaluation des produits</h1>
                                                    <button type="button" class="btn-close btn-secondary" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <div class="card card-body mb-2">
                                                    <div class="rating-css">
                                                        <div class="star-icon">
                                                            @if ($user_Rating != 0)
                                                                @php $userRating =number_format($user_Rating) @endphp

                                                                @for ($i = 1; $i <= $userRating; $i++)
                                                                    <input type="radio" value="{{ $i }}"
                                                                        name="productRating" checked
                                                                        id="rating{{ $i }}">
                                                                    <label for="rating{{ $i }}"
                                                                        class="fa fa-star checked"></label>
                                                                @endfor

                                                                @for ($j = $userRating + 1; $j <= 5; $j++)
                                                                    <input type="radio" value="{{ $j }}"
                                                                        name="productRating"
                                                                        id="rating{{ $j }}">
                                                                    <label for="rating{{ $j }}"
                                                                        class="fa fa-star"></label>
                                                                @endfor
                                                            @else
                                                                <input type="radio" value="1" name="productRating"
                                                                    checked id="rating1">
                                                                <label for="rating1" class="fa fa-star"></label>
                                                                <input type="radio" value="2" name="productRating"
                                                                    id="rating2">
                                                                <label for="rating2" class="fa fa-star"></label>
                                                                <input type="radio" value="3" name="productRating"
                                                                    id="rating3">
                                                                <label for="rating3" class="fa fa-star"></label>
                                                                <input type="radio" value="4" name="productRating"
                                                                    id="rating4">
                                                                <label for="rating4" class="fa fa-star"></label>
                                                                <input type="radio" value="5" name="productRating"
                                                                    id="rating5">
                                                                <label for="rating5" class="fa fa-star"></label>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                                <input type="hidden" name="productID" value="{{ $product->id }}">
                                                <input type="hidden" name="userID" value="{{ auth()->user()->id }}">


                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Fermer</button>
                                                    <button type="submit" class="btn btn-secondary">Soumettre</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- {{-- rating end --}} -->
                                </div>
                            </form>

                            <div class="col-lg-12">
                                <nav>
                                    <div class="nav nav-tabs mb-3">
                                        <button class="nav-link active border-white border-bottom-0" type="button"
                                            role="tab" id="nav-about-tab" data-bs-toggle="tab"
                                            data-bs-target="#nav-about" aria-controls="nav-about"
                                            aria-selected="true">Description</button>
                                        <button class="nav-link border-white border-bottom-0" type="button"
                                            role="tab" id="nav-mission-tab" data-bs-toggle="tab"
                                            data-bs-target="#nav-mission" aria-controls="nav-mission"
                                            aria-selected="false">Avis</button>
                                    </div>
                                </nav>
                                <div class="tab-content mb-5">
                                    <div class="tab-pane active" id="nav-about" role="tabpanel"
                                        aria-labelledby="nav-about-tab">
                                        <p>{{ $product->description }}</p>
                                    </div>
                                    <div class="tab-pane" id="nav-mission" role="tabpanel"
                                        aria-labelledby="nav-mission-tab">
                                        @foreach ($comment as $item)
                                            <div class="d-flex">
                                                @if ($item->userprofile != null)
                                                    <img src="{{ asset('userProfile/' . $item->userprofile) }}"
                                                        class="img-fluid rounded-circle p-3"
                                                        style="width: 100px; height: 100px;" alt="">
                                                @else
                                                    <img src="{{ asset('admin/img/undraw_profile.svg') }}"
                                                        class="img-fluid rounded-circle p-3"
                                                        style="width: 100px; height: 100px;" alt="">
                                                @endif

                                                <div class="">
                                                    <p class="mb-2" style="font-size: 14px;">
                                                        {{ $item->created_at->format('j-F-y') }}</p>
                                                    <div class="d-flex justify-content-between">
                                                        <h5>{{ $item->username }}</h5>
                                                    </div>
                                                    <p>{{ $item->message }}</p>
                                                </div>
                                            </div>
                                            <hr>
                                        @endforeach
                                    </div>
                                    <div class="tab-pane" id="nav-vision" role="tabpanel">
                                        <p class="text-dark">Tempor erat elitr rebum at clita. Diam dolor diam ipsum et
                                            tempor
                                            sit. Aliqu diam
                                            amet diam et eos labore. 3</p>
                                        <p class="mb-0">Diam dolor diam ipsum et tempor sit. Aliqu diam amet diam et eos
                                            labore.
                                            Clita erat ipsum et lorem et sit</p>
                                    </div>
                                </div>
                            </div>
                            <form action="{{ route('comment') }}" method="post">
                                @csrf
                                <h4 class="text-white fw-bold">Laisser un commentaire</h4>
                                <input type="hidden" name="userID" value="{{ auth()->user()->id }}">
                                <input type="hidden" name="productID" value="{{ $product->id }}">

                                <div class="row g-4">
                                    <div class="col-lg-12">
                                        <div class="border-bottom rounded my-2">
                                            <textarea name="message" id="" class="form-control border-0 @error('message') is-invalid @enderror"
                                                cols="30" rows="8" placeholder="Your Review *" spellcheck="false">{{ old('message') }}</textarea>
                                            @error('message')
                                                <small class="invalid-feedback">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="d-flex justify-content-between">
                                            <button type="submit"
                                                class="btn border border-secondary text-primary rounded-pill px-4 py-3">
                                                Poste
                                                Commentaire</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                </div>
                <h1 class="text-white fw-bold mb-0">Produits connexes</h1>
                <div class="vesitable">
                    <div class="owl-carousel vegetable-carousel justify-content-center" >

                        @foreach ($productList as $item)
                            @if ($product->id != $item->id)
                                <div class="border border-primary rounded position-relative vesitable-item" style="background: linear-gradient(135deg, #ffd700, #191970);">
                                    <div class="vesitable-img">
                                        <a href="{{ route('shopDetails', $item->id) }}">
                                            <img style="height:250px" src="{{ asset('productImages/' . $item->image) }}"
                                                class="img-fluid w-100 rounded-top" alt="">
                                        </a>
                                    </div>
                                    <div class="text-white bg-secondary px-3 py-1 rounded position-absolute"
                                        style="top: 10px; right: 10px;">{{ $item->category_name }}</div>
                                    <div class="p-4 pb-0 rounded-bottom">
                                        <h4 class="text-white">{{ $item->name }}</h4>
                                        <p class="text-white fs-5 fw-bold">{{ $item->price }} FCFA</p>
                                        <p>{{ Str::words($item->description, 10, '...') }}</p>
                                        <div class="d-flex justify-content-between flex-lg-wrap">

                                        <form action="{{ route('addToCart') }}" method="POST">
                                                            @csrf
                                                            <input type="hidden" name="productID" value="{{ $item->id }}">
                                                            <input type="hidden" name="qty" value="1"> <!-- Default quantity -->
                                                            <button type="submit" class="btn border border-secondary rounded-pill px-3 py-1 mb-4 text-primary">
                                                                <i class="fa fa-shopping-bag me-2 text-primary"></i> Ajouter au panier
                                                            </button>
                                        </form>

                                        </div>

                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Single Product End -->
@endsection
