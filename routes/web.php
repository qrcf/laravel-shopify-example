<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ShopifyStoreController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::get('/register-store', [ShopifyStoreController::class, 'create'])->middleware(['auth'])->name('register-store');
Route::get('/store/{id}', [ShopifyStoreController::class, 'dashboard'])->middleware(['auth'])->name('store-dashboard');
Route::get('/store/{id}/products/{product_id}', [ShopifyStoreController::class, 'product'])->middleware(['auth'])->name('store-product');

Route::get('/', [UserController::class, 'dashboard'])->middleware(['auth'])->name('dashboard');
Route::post('/store', [ShopifyStoreController::class, 'store'])->middleware(['auth'])->name('store');
Route::post('/remove-store', [ShopifyStoreController::class, 'remove'])->middleware(['auth'])->name('remove-store');
Route::post('/refresh-token', [UserController::class, 'refresh_token'])->middleware(['auth'])->name('refresh-token');
Route::post('/store/{id}/products/{product_id}', [ShopifyStoreController::class, 'save_product'])->middleware(['auth'])->name('save-product');

require __DIR__ . '/auth.php';
