<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\Auth\UserLoginController;
use App\Http\Controllers\Admin\Auth\AdminLoginController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\User\Auth\UserRegisterController;
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

// 一般ユーザー用
Route::get('/login', [UserLoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [UserLoginController::class, 'login']);
Route::post('/logout', [UserLoginController::class, 'logout'])->name('logout');

Route::middleware('auth:web')->group(function () {
    Route::get('/attendance', function () {
        return view('attendance.index'); // 一般ユーザーの勤怠ページなど
    })->name('attendance.index');
});

// 管理者用
Route::prefix('admin')->group(function () {
    Route::get('/login', [AdminLoginController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/login', [AdminLoginController::class, 'login']);
    Route::post('/logout', [AdminLoginController::class, 'logout'])->name('admin.logout');

    Route::middleware('auth:admin')->group(function () {
        Route::get('/admin.attendances', function () {
            return view('admin.attendances'); // 管理者用勤怠ページ
        })->name('admin.attendances');
    });
});

Route::get('/user_register', [UserRegisterController::class, 'show'])->name('user.register.show');
Route::post('/user_register', [UserRegisterController::class, 'register'])->name('user.register');

