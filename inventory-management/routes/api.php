<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\ProductController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\OrderController;
use App\Http\Controllers\Api\V1\DashboardController;



Route::prefix('v1')->group(function () {
    Route::prefix('auth')->name('auth.')->group(function () {
        Route::post('/login', [AuthController::class, 'login'])->name('login');
        Route::post('/register', [AuthController::class, 'register'])->name('register');
    });
    Route::middleware('auth:sanctum')->group(function () {

        Route::prefix('auth')->name('auth.')->group(function () {
            Route::get('/me', [AuthController::class, 'me'])->name('me');
            Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
        });

        Route::apiResource('products', ProductController::class)
            ->only(['index', 'show']);

        Route::apiResource('categories', CategoryController::class)
            ->only(['index', 'show']);

        Route::prefix('orders')->name('orders.')->group(function () {
            Route::get('/my-orders', [OrderController::class, 'myOrders'])->name('my-orders');
            Route::post('/', [OrderController::class, 'store'])->name('store');
            Route::get('/{order}', [OrderController::class, 'show'])->name('show');
        });

        Route::middleware('permission:products.create')->group(function () {

            Route::apiResource('products', ProductController::class)
                ->only(['store', 'update', 'destroy']);

            Route::prefix('products/stats')->name('products.stats.')->group(function () {
                Route::get('/low-stock', [ProductController::class, 'lowStock'])->name('low-stock');
                Route::get('/best-selling', [ProductController::class, 'bestSelling'])->name('best-selling');
            });
        });

        Route::middleware('permission:categories.create')->group(function () {
            Route::apiResource('categories', CategoryController::class)
                ->only(['store', 'update', 'destroy']);
        });

        Route::middleware('permission:users.view')->group(function () {
            Route::apiResource('users', UserController::class);
        });

        Route::middleware('permission:orders.view')->group(function () {
            Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
            Route::put('/orders/{order}', [OrderController::class, 'update'])->name('orders.update');
            Route::delete('/orders/{order}', [OrderController::class, 'destroy'])->name('orders.destroy');
        });

        Route::middleware('permission:dashboard.view')->group(function () {
            Route::get('/dashboard/stats', [DashboardController::class, 'stats'])->name('dashboard.stats');
        });
    });
});
Route::fallback(function () {
    return response()->json([
        'success' => false,
        'message' => 'API endpoint not found.',
    ], 404);
});
