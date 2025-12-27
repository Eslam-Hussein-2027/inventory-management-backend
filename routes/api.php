<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\ProductController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\OrderController;
use App\Http\Controllers\Api\V1\SupplierController;
use App\Http\Controllers\Api\V1\DashboardController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| Base URL: /api/v1
|
*/

Route::prefix('v1')->group(function () {

  /*
  |--------------------------------------------------------------------------
  | Public Routes
  |--------------------------------------------------------------------------
  */
  Route::prefix('auth')->name('auth.')->group(function () {
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/register', [AuthController::class, 'register'])->name('register');
  });

  /*
  |--------------------------------------------------------------------------
  | Protected Routes (Authenticated Users)
  |--------------------------------------------------------------------------
  */
  Route::middleware('auth:sanctum')->group(function () {

    // Auth Routes
    Route::prefix('auth')->name('auth.')->group(function () {
      Route::get('/me', [AuthController::class, 'me'])->name('me');
      Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
      Route::put('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');
      Route::post('/change-password', [AuthController::class, 'changePassword'])->name('password.change');
    });

    /*
    |--------------------------------------------------------------------------
    | Shared Routes (Both Admin & User)
    |--------------------------------------------------------------------------
    */

    // Products - Read Only for Users
    Route::apiResource('products', ProductController::class)
      ->only(['index', 'show']);

    // Categories - Read Only for Users
    Route::apiResource('categories', CategoryController::class)
      ->only(['index', 'show']);

    // Suppliers - Read Only for Users
    Route::apiResource('suppliers', SupplierController::class)
      ->only(['index', 'show']);

    // Orders - User can view own and create
    Route::prefix('orders')->name('orders.')->group(function () {
      Route::get('/my-orders', [OrderController::class, 'myOrders'])->name('my-orders');
      Route::post('/', [OrderController::class, 'store'])->name('store');
      Route::get('/{order}', [OrderController::class, 'show'])->name('show');
    });

    /*
    |--------------------------------------------------------------------------
    | Admin Only Routes
    |--------------------------------------------------------------------------
    */
    Route::middleware('permission:products.create,sanctum')->group(function () {

      // Products - Full CRUD for Admin
      Route::apiResource('products', ProductController::class)
        ->only(['store', 'update', 'destroy']);

      // Product Stats
      Route::prefix('products/stats')->name('products.stats.')->group(function () {
        Route::get('/low-stock', [ProductController::class, 'lowStock'])->name('low-stock');
        Route::get('/best-selling', [ProductController::class, 'bestSelling'])->name('best-selling');
      });
    });

    Route::middleware('permission:categories.create,sanctum')->group(function () {
      // Categories - Full CRUD for Admin
      Route::apiResource('categories', CategoryController::class)
        ->only(['store', 'update', 'destroy']);
    });

    Route::middleware('permission:suppliers.create,sanctum')->group(function () {
      // Suppliers - Full CRUD for Admin
      Route::apiResource('suppliers', SupplierController::class)
        ->only(['store', 'update', 'destroy']);
    });

    Route::middleware('permission:users.view,sanctum')->group(function () {
      // Users - Full CRUD for Admin
      Route::apiResource('users', UserController::class);
    });

    Route::middleware('permission:orders.view,sanctum')->group(function () {
      // Orders - Admin can view all, update, delete
      Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
      Route::put('/orders/{order}', [OrderController::class, 'update'])->name('orders.update');
      Route::delete('/orders/{order}', [OrderController::class, 'destroy'])->name('orders.destroy');
    });

    Route::middleware('permission:dashboard.view,sanctum')->group(function () {
      // Dashboard
      Route::get('/dashboard/stats', [DashboardController::class, 'stats'])->name('dashboard.stats');
    });
  });
});

/*
|--------------------------------------------------------------------------
| Fallback Route
|--------------------------------------------------------------------------
*/
Route::fallback(function () {
  return response()->json([
    'success' => false,
    'message' => 'API endpoint not found.',
  ], 404);
});
