<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\Auth\UserLoginController;
use App\Http\Controllers\Admin\Auth\AdminLoginController;
use App\Http\Controllers\User\Auth\UserRegisterController;
use App\Http\Controllers\User\AttendanceController;
use App\Http\Controllers\Admin\AdminAttendanceController;

// 一般ユーザー用ログイン・ログアウト
Route::get('/login', [UserLoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [UserLoginController::class, 'login']);
Route::post('/logout', [UserLoginController::class, 'logout'])->name('logout');

// 一般ユーザー用勤怠（出勤打刻）
Route::middleware('auth:web')->group(function () {
    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::post('/attendance/clock-in', [AttendanceController::class, 'clockIn'])->name('attendance.clockin');
});

// 管理者用ルート
Route::prefix('admin')->group(function () {
    Route::get('/login', [AdminLoginController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/login', [AdminLoginController::class, 'login']);
    Route::post('/logout', [AdminLoginController::class, 'logout'])->name('admin.logout');

    Route::middleware('auth:admin')->group(function () {
        Route::get('/attendances', [AttendanceController::class, 'adminIndex'])->name('admin.attendances');
    });
});

// ユーザー登録
Route::get('/user_register', [UserRegisterController::class, 'show'])->name('user.register.show');
Route::post('/user_register', [UserRegisterController::class, 'register'])->name('user.register');

Route::middleware('auth:web')->group(function () {
    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');

    Route::post('/attendance/clockin', [AttendanceController::class, 'clockIn'])->name('attendance.clockin');
    Route::post('/attendance/clockout', [AttendanceController::class, 'clockOut'])->name('attendance.clockout');
    Route::post('/attendance/break_start', [AttendanceController::class, 'breakStart'])->name('attendance.break_start');
    Route::post('/attendance/break_end', [AttendanceController::class, 'breakEnd'])->name('attendance.break_end');
});