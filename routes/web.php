<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\BloggerController;
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
Route::middleware(['guest:admin'])->group(function () {

    
Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/register-admin', [AdminController::class, 'create'])
                ->name('register-admin');

Route::post('/register-admin', [AdminController::class, 'store']);

Route::get('/login-admin', [AdminController::class, 'login_admin_form'])
                ->name('login-admin');

Route::post('/login-admin', [AdminController::class, 'login_admin']);


});



Route::middleware(['auth:admin'])->group(function () {


Route::get('/dashboard-admin', [AdminController::class, 'dashboard_admin'])
                ->name('dashboard-admin');

Route::view('/setting-admin','admin.admin_setting');
            
 });