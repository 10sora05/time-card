<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\Auth\UserLoginController;
use App\Http\Controllers\Admin\Auth\AdminLoginController;
use App\Http\Controllers\User\Auth\UserRegisterController;
use App\Http\Controllers\Admin\AdminAttendanceController;
use App\Http\Controllers\User\AttendanceController;
use App\Http\Controllers\Admin\AdminAttendanceCorrectionController;
use App\Http\Controllers\User\AttendanceCorrectionController;


// 一般ユーザー用ログイン・ログアウト
Route::get('/login', [UserLoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [UserLoginController::class, 'login']);
Route::post('/logout', [UserLoginController::class, 'logout'])->name('logout');

// ユーザー登録
Route::get('/user_register', [UserRegisterController::class, 'show'])->name('user.register.show');
Route::post('/user_register', [UserRegisterController::class, 'register'])->name('user.register');

// 管理者ログイン処理（変更なし）
Route::prefix('admin')->group(function () {
    Route::get('/login', [AdminLoginController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/login', [AdminLoginController::class, 'login']);
    Route::post('/logout', [AdminLoginController::class, 'logout'])->name('admin.logout');
});

// 管理者用勤怠一覧（adminガード + isAdmin権限チェックなども入れると良い）
Route::prefix('admin')->middleware(['auth:admin'])->group(function () {
    Route::get('/attendances', [AdminAttendanceController::class, 'index'])->name('admin.attendances');
});

// 一般ユーザー用勤怠ルート
Route::middleware('auth:web')->group(function () {
    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');

    Route::post('/attendance/clockin', [AttendanceController::class, 'clockIn'])->name('attendance.clockin');
    Route::post('/attendance/clockout', [AttendanceController::class, 'clockOut'])->name('attendance.clockout');
    Route::post('/attendance/break_start', [AttendanceController::class, 'breakStart'])->name('attendance.break_start');
    Route::post('/attendance/break_end', [AttendanceController::class, 'breakEnd'])->name('attendance.break_end');

    Route::get('/attendance/list', [AttendanceController::class, 'list'])->name('attendance.list');
    Route::get('/attendance/{id}', [AttendanceController::class, 'show'])->name('attendance.show');
    Route::put('/attendance/{id}', [AttendanceController::class, 'update'])->name('attendance.update');
});

// 管理者用ルート
Route::prefix('admin')->middleware(['auth:admin'])->group(function () {
    Route::get('attendances', [AdminAttendanceController::class, 'index'])->name('admin.attendances');
    Route::get('attendances/{id}', [AdminAttendanceController::class, 'show'])->name('admin.attendances.show');
    Route::put('attendances/{id}', [AdminAttendanceController::class, 'update'])->name('admin.attendance.update');
});

// 一般ユーザー用 勤怠修正申請
Route::middleware('auth:web')->group(function () {
    Route::post('/attendance/{id}/correction', [AttendanceCorrectionController::class, 'store'])->name('user.attendance.correction.store');
});

// 管理者用: 修正申請の承認・拒否
Route::prefix('admin')->middleware(['auth:admin'])->group(function () {
    Route::put('/attendance_correction/{id}', [AdminAttendanceCorrectionController::class, 'update'])->name('admin.attendance_correction.update');
});

Route::prefix('admin')->name('admin.')->middleware('auth:admin')->group(function () {
    Route::get('requests', [AdminAttendanceCorrectionController::class, 'index'])->name('attendance_corrections.index');
    Route::get('requests/{id}', [AdminAttendanceCorrectionController::class, 'show'])->name('attendance_corrections.show');
    Route::put('requests/{id}', [AdminAttendanceCorrectionController::class, 'update'])->name('attendance_corrections.update');
});

Route::middleware(['auth:web'])->group(function () {
    Route::get('/stamp_correction_request/list', [AttendanceCorrectionController::class, 'list'])->name('user.attendance_corrections.list');
    Route::get('/stamp_correction_request/{id}', [AttendanceCorrectionController::class, 'show'])->name('user.attendance_corrections.show');
});
