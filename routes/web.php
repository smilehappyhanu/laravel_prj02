<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\BrandController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\AuthAdmin;

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

// Route::get('/', function () {
//     return view('welcome');
// });

// Route for front site
Auth::routes();

Route::get('/', [HomeController::class, 'index'])->name('front.home');

Route::middleware(['auth'])->group(function(){
    Route::get('/account-dashboard', [UserController::class, 'index'])->name('user.index');

});

// Route for admin site
Route::middleware(['auth',AuthAdmin::class])->group(function(){
    // Dashboard routes
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');

    // Brand routes
    Route::get('/admin/brands/', [BrandController::class, 'index'])->name('brands.index');
    Route::get('/admin/brands/create', [BrandController::class, 'create'])->name('brands.create');
    Route::post('/admin/brands/store', [BrandController::class, 'store'])->name('brands.store');
    Route::get('/admin/brands/{id}/edit', [BrandController::class, 'edit'])->name('brands.edit');
    Route::put('/admin/brands/{id}/update', [BrandController::class, 'update'])->name('brands.update');
    Route::delete('/admin/brands/{id}/delete', [BrandController::class, 'destroy'])->name('brands.delete');

});


