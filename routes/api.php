<?php

use App\Http\Controllers\LoginController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
Route::post('/login', [LoginController::class, 'createToken']);
Route::post('/logout', [LoginController::class, 'deleteToken'])
    ->middleware('auth:sanctum');

Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{product}', [ProductController::class, 'show']);

Route::post('/orders/create', [OrderController::class, 'create'])
    ->middleware('auth:sanctum');
Route::get('/orders/show/{order}', [OrderController::class, 'show'])
    ->middleware('auth:sanctum');
