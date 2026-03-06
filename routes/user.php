<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\ShopController;
use App\Http\Controllers\User\UserDashboardController;


//customer
Route::group(['prefix' => 'user' , 'middleware' => ['auth','user']], function(){
    Route::get('/home', [UserDashboardController::class, 'index'])->name('userDashboard');

    Route::get('/shop/{category_id?}', [ShopController::class, 'shop'])->name('shopList');

    Route::get('details/{id}', [ShopController::class, 'details'])->name('shopDetails');

    Route::post('comment', [ShopController::class, 'comment'])->name('comment');

    Route::post('addRating', [ShopController::class, 'addRating'])->name('addRating');


//profile
    Route::get('detail', [UserDashboardController::class, 'profileDetails'])->name('userProfileDetails');
    Route::post('update', [UserDashboardController::class, 'profileUpdate'])->name('profileUpdate');

//password
    Route::get('changePassword', [UserDashboardController::class, 'changePassword'])->name('changePassword');
    Route::post('changeUserPassword', [UserDashboardController::class, 'changeUserPassword'])->name('changeUserPassword');

//cart
    Route::get('cart',[ShopController::class, 'cart'])->name('cart');
    Route::post('addToCart',[ShopController::class, 'addToCart'])->name('addToCart');
    Route::get('remove/cart',[ShopController::class, 'removeCart'])->name('removeCart');

//order
    Route::get('order',[ShopController::class, 'order'])->name('order');
    Route::get('orderList',[ShopController::class, 'orderList'])->name('orderList');
    Route::get('customerOrders/{orderCode}',[ShopController::class, 'customerOrders'])->name('customerOrders');


//payment
    Route::get('userPayment',[ShopController::class, 'userPayment'])->name('userPayment');
    Route::post('order/product',[ShopController::class, 'orderProduct'])->name('orderProduct');

//contact
    Route::get('contactus',[UserDashboardController::class, 'contactUs'])->name('contactUs');
    Route::post('sendMessage',[UserDashboardController::class, 'sendMessage'])->name('sendMessage');


});
