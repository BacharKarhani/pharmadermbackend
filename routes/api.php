<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\ZoneController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;

// Auth routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/test', [AuthController::class, 'test']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);

// Public routes
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/trending', [ProductController::class, 'trending']); 
Route::get('/products/{product}', [ProductController::class, 'show']);
Route::get('/products/{product}/related', [ProductController::class, 'related']);
Route::get('/zones', [ZoneController::class, 'index']);


// Authenticated user routes
Route::middleware('auth:sanctum')->group(function () {
    // User data
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Wishlist
    Route::get('/wishlist', [WishlistController::class, 'index']);
    Route::post('/wishlist', [WishlistController::class, 'store']);
    Route::delete('/wishlist/{product_id}', [WishlistController::class, 'destroy']);

    // Cart
    Route::get('/cart', [CartController::class, 'index']);
    Route::post('/cart', [CartController::class, 'store']);
    Route::put('/cart/{product_id}', [CartController::class, 'update']);
    Route::delete('/cart/{product_id}', [CartController::class, 'destroy']);

    // Addresses
    Route::get('/addresses', [AddressController::class, 'index']);
    Route::post('/addresses', [AddressController::class, 'store']);
    Route::put('/addresses/{address}', [AddressController::class, 'update']);
    Route::delete('/addresses/{address}', [AddressController::class, 'destroy']);
    Route::get('/addresses/{address}', [AddressController::class, 'show']);

    // Checkout
    Route::post('/checkout', [CheckoutController::class, 'store']);

    // Users see their own orders
    Route::get('/orders/my', [OrderController::class, 'myOrders']);
});

// Admin-only routes
Route::middleware(['auth:sanctum', 'is_admin'])->group(function () {
    // Categories
    Route::post('/categories', [CategoryController::class, 'store']);
    Route::put('/categories/{category}', [CategoryController::class, 'update']);
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy']);
    Route::get('/categories/{category}', [CategoryController::class, 'show']);

    // Products
    Route::post('/products', [ProductController::class, 'store']);
    Route::put('/products/{product}', [ProductController::class, 'update']);
    Route::delete('/products/{product}', [ProductController::class, 'destroy']);

    // Admin order management
    Route::get('/orders', [OrderController::class, 'indexPaginated']);
    Route::get('/orders/{order_id}', [OrderController::class, 'show']);
    Route::put('/orders/{order_id}/update-status', [OrderController::class, 'updateStatus']);
    Route::get('/orders/{order_id}/profit', [OrderController::class, 'getOrderProfit']);

    // Admin user management
    Route::get('/users/search-by-name', [AuthController::class, 'searchUserByName']);
    Route::put('/users/{userId}/promote', [AuthController::class, 'promoteToAdmin']);
});

// Whish Payment (open for callbacks)
Route::get('/payment/whish/callback/success/{order_id}', [PaymentController::class, 'whishCallbackSuccess'])->name('api.whish.callback.success');
Route::get('/payment/whish/callback/failure/{order_id}', [PaymentController::class, 'whishCallbackFailure'])->name('api.whish.callback.failure');
Route::get('/payment/whish/redirect/success/{order_id}', [PaymentController::class, 'whishRedirectSuccess'])->name('api.whish.redirect.success');
Route::get('/payment/whish/redirect/failure/{order_id}', [PaymentController::class, 'whishRedirectFailure'])->name('api.whish.redirect.failure');


Route::middleware('auth:sanctum')->group(function () {
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::post('/change-password', [AuthController::class, 'changePassword']);
});
