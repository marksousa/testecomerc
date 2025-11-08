<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomerController;

Route::get('/status', function () {
    return response()->json(['status' => 'API is working']);
});

Route::resource('customers', CustomerController::class);
