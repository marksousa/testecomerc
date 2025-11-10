<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/status', function () {
    return response()->json(['status' => 'API is working']);
});

Route::apiResource('customers', \App\Http\Controllers\CustomerController::class);
Route::apiResource('products', \App\Http\Controllers\ProductController::class);
Route::apiResource('orders', \App\Http\Controllers\OrderController::class);

Route::get('customers/{customerId}/orders', [\App\Http\Controllers\OrderController::class, 'getByCustomer']);
