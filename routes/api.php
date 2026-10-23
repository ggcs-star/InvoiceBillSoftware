<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\InvoiceController;
use App\Http\Controllers\Api\MenuController;
use App\Http\Controllers\Api\OrderController;
use Illuminate\Support\Facades\Route;

Route::post('/v1/login', [AuthController::class, 'login']);

Route::middleware(['auth:api', 'role:user,admin'])->prefix('v1')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/menu', [MenuController::class, 'index']);

    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/orders/{order}', [OrderController::class, 'show']);
    Route::post('/orders/{order}/items', [OrderController::class, 'addItem']);
    Route::put('/orders/{order}/items/{item}', [OrderController::class, 'updateItem']);
    Route::delete('/orders/{order}/items/{item}', [OrderController::class, 'removeItem']);
    Route::post('/orders/{order}/discount', [OrderController::class, 'discount']);
    Route::put('/orders/{order}/customer', [OrderController::class, 'updateCustomer']);
    Route::post('/orders/{order}/checkout', [OrderController::class, 'checkout']);
    Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel']);

    Route::get('/invoices/{invoice}', [InvoiceController::class, 'show']);
    Route::post('/invoices/{invoice}/print', [InvoiceController::class, 'print']);
});
