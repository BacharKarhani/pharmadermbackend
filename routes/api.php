<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


use App\Http\Controllers\AuthController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);
Route::get('/test', [AuthController::class, 'test']);


use App\Http\Controllers\CategoryController;

Route::get('/categories', [CategoryController::class, 'index']); // public or logged-in

Route::middleware(['auth:sanctum', 'is_admin'])->group(function () {
    Route::post('/categories', [CategoryController::class, 'store']);
    Route::put('/categories/{category}', [CategoryController::class, 'update']);
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy']);
    Route::get('/categories/{category}', [CategoryController::class, 'show']);

});


use App\Http\Controllers\ProductController;

// Public: anyone can view products
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{product}', [ProductController::class, 'show']);
Route::get('/products/{product}/related', [ProductController::class, 'related']);

// Admin: auth + role check
Route::middleware(['auth:sanctum', 'is_admin'])->group(function () {
    Route::post('/products', [ProductController::class, 'store']);
    Route::put('/products/{product}', [ProductController::class, 'update']);
    Route::delete('/products/{product}', [ProductController::class, 'destroy']);
});


use App\Http\Controllers\WishlistController;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/wishlist', [WishlistController::class, 'index']);
    Route::post('/wishlist', [WishlistController::class, 'store']);
    Route::delete('/wishlist/{product_id}', [WishlistController::class, 'destroy']);
});


use App\Http\Controllers\CartController;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/cart', [CartController::class, 'index']);
    Route::post('/cart', [CartController::class, 'store']);
    Route::put('/cart/{product_id}', [CartController::class, 'update']);
    Route::delete('/cart/{product_id}', [CartController::class, 'destroy']);
});

use App\Http\Controllers\AddressController;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/addresses', [AddressController::class, 'index']);
    Route::post('/addresses', [AddressController::class, 'store']);
    Route::put('/addresses/{address}', [AddressController::class, 'update']);
    Route::delete('/addresses/{address}', [AddressController::class, 'destroy']);
    Route::get('/addresses/{address}', [AddressController::class, 'show']);
});


use App\Http\Controllers\ZoneController;

Route::get('/zones', [ZoneController::class, 'index']);

use App\Http\Controllers\CheckoutController;

Route::middleware('auth:sanctum')->post('/checkout', [CheckoutController::class, 'store']);

use App\Http\Controllers\OrderController;



Route::middleware(['auth:sanctum', 'is_admin'])->group(function () {
    Route::get('/orders', [OrderController::class, 'indexPaginated']);
    Route::get('/orders/{order_id}', [OrderController::class, 'show']);
    Route::put('/orders/{order_id}/update-status', [OrderController::class, 'updateStatus']); // <-- ADD THIS

});



Route::middleware(['auth:sanctum', 'is_admin'])->group(function () {
    Route::get('/users/search-by-name', [AuthController::class, 'searchUserByName']);
    Route::put('/users/{userId}/promote', [AuthController::class, 'promoteToAdmin']);
    Route::get('/orders/{order_id}/profit', [OrderController::class, 'getOrderProfit']); // ✅ Profit API

});


