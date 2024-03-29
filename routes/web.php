<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\BloggerController;
use App\Http\Controllers\LogoutController;
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
Route::middleware(['guest:admin','guest:blogger','disable_back_btn'])->group(function () {

    
Route::get('/', function () {
    return view('home');
})->name('home');

Route::get('/register-admin', [AdminController::class, 'create'])
                ->name('register-admin');

Route::post('/register-admin', [AdminController::class, 'store']);

Route::get('/login-admin', [AdminController::class, 'login_admin_form'])
                ->name('login-admin');

Route::post('/login-admin', [AdminController::class, 'login_admin']);


Route::get('/register-blogger', [BloggerController::class, 'create'])
                ->name('register-blogger');

Route::post('/register-blogger', [BloggerController::class, 'store']);

Route::get('/login-blogger', [BloggerController::class, 'login_blogger_form'])
                ->name('login-blogger');

Route::post('/login-blogger', [BloggerController::class, 'login_blogger']);


});



Route::middleware(['auth:admin','disable_back_btn','is_verify_admin_email'])->group(function () {


Route::get('/dashboard-admin', [AdminController::class, 'dashboard_admin'])
                ->name('dashboard-admin');

Route::view('/setting-admin','admin.admin_setting')->name('setting-admin');
      

 });



Route::middleware(['auth:blogger','disable_back_btn','is_verify_blogger_email'])->group(function () {


Route::get('/dashboard-blogger', [BloggerController::class, 'dashboard_blogger'])
                ->name('dashboard-blogger');

Route::view('/setting-blogger','blogger.blogger_setting')->name('setting-blogger');


 });


Route::middleware('operations_for_admin_and_blogger')->group(function () {


Route::post('/logout', [LogoutController::class, 'destroy'])
                ->name('logout');

});



//Custom Email Verification
Route::middleware(['auth:admin','disable_back_btn'])->group(function () {


Route::get('admin/account/verify/{token}', [AdminController::class, 'verify_account'])->name('admin-verify');

Route::get('admin/account/email/verification/notice', [AdminController::class, 'verify_account_notice'])->name('admin-verify-notice');

Route::post('admin/account/email/resend', [AdminController::class, 'verify_account_email_resend'])->name('admin-verify-email-resend');


});



Route::middleware(['auth:blogger','disable_back_btn'])->group(function () {


Route::get('blogger/account/verify/{token}', [BloggerController::class, 'verify_account'])->name('blogger-verify');

Route::get('blogger/account/email/verification/notice', [BloggerController::class, 'verify_account_notice'])->name('blogger-verify-notice');

Route::post('blogger/account/email/resend', [BloggerController::class, 'verify_account_email_resend'])->name('blogger-verify-email-resend');


});
//End Custom Email verification



//Custom Password Reset
Route::middleware(['guest:admin','guest:blogger','disable_back_btn'])->group(function () {

//Password Reset for Admin
Route::get('admin/forgot_password',[AdminController::class, 'forgot_password'])
->name('admin-password-request');

Route::post('admin/forgot_password',[AdminController::class, 'forgot_password_handle'])
->name('admin-password-email');

Route::get('admin/reset_password/{token}/{email}',[AdminController::class, 'reset_password'])
->name('admin-password-reset');

Route::post('admin/reset_password_handle', [AdminController::class, 'reset_password_handle'])
->name('admin-password-update');


//Password Reset for Blogger
Route::get('blogger/forgot_password',[BloggerController::class, 'forgot_password'])
->name('blogger-password-request');

Route::post('blogger/forgot_password',[BloggerController::class, 'forgot_password_handle'])
->name('blogger-password-email');

Route::get('blogger/reset_password/{token}/{email}',[BloggerController::class, 'reset_password'])
->name('blogger-password-reset');

Route::post('blogger/reset_password_handle', [BloggerController::class, 'reset_password_handle'])
->name('blogger-password-update');


});
//End Custom Password Reset


                
Route::fallback(function () {
  return redirect()->route('home');
});