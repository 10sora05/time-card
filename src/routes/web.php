<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\Auth\UserLoginController;
use App\Http\Controllers\Admin\Auth\AdminLoginController;
use App\Http\Controllers\User\Auth\UserRegisterController;
use App\Http\Controllers\Admin\AdminAttendanceController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\User\AttendanceController;
use App\Http\Controllers\Admin\AdminAttendanceCorrectionController;
use App\Http\Controllers\User\AttendanceCorrectionController;
use App\Http\Controllers\Admin\UserController;



// 一般ユーザー用ログイン・ログアウト
Route::get('/login', [UserLoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [UserLoginController::class, 'login']);
Route::post('/logout', [UserLoginController::class, 'logout'])->name('logout');

// ユーザー登録
Route::get('/user_register', [UserRegisterController::class, 'show'])->name('user.register.show');
Route::post('/user_register', [UserRegisterController::class, 'register'])->name('user.register');

// 管理者ログイン処理
Route::prefix('admin')->group(function () {
    Route::get('/login', [AdminLoginController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/login', [AdminLoginController::class, 'login']);
    Route::post('/logout', [AdminLoginController::class, 'logout'])->name('admin.logout');
});

// 一般ユーザー用ルート
Route::middleware('auth:web')->group(function () {

    // 勤怠関連
    Route::prefix('attendance')->group(function () {
        Route::get('/', [AttendanceController::class, 'index'])->name('attendance.index');
        Route::post('/clockin', [AttendanceController::class, 'clockIn'])->name('attendance.clockin');
        Route::post('/clockout', [AttendanceController::class, 'clockOut'])->name('attendance.clockout');
        Route::post('/break_start', [AttendanceController::class, 'breakStart'])->name('attendance.break_start');
        Route::post('/break_end', [AttendanceController::class, 'breakEnd'])->name('attendance.break_end');

        Route::get('/list', [AttendanceController::class, 'list'])->name('attendance.list');
        Route::get('/detail/{id}', [AttendanceController::class, 'show'])->name('attendance.detail');
        Route::put('/detail/{id}', [AttendanceController::class, 'update'])->name('attendance.update');

        // 勤怠修正申請
        Route::post('/{id}/correction', [AttendanceCorrectionController::class, 'store'])->name('user.attendance.correction.store');
    });

            // 勤怠修正申請一覧（URLを変更）
    Route::get('/stamp_correction_request/list', [AttendanceCorrectionController::class, 'list'])
        ->name('user.attendance_corrections.list');
});

Route::prefix('admin')->name('admin.')->middleware('auth:admin')->group(function () {
    // 勤怠修正申請（申請一覧・詳細・承認・却下）
    Route::get('requests', [AdminAttendanceCorrectionController::class, 'index'])->name('attendance_corrections.index');
    Route::get('requests/{id}', [AdminAttendanceCorrectionController::class, 'show'])->name('attendance_corrections.show');
    Route::put('requests/{id}', [AdminAttendanceCorrectionController::class, 'update'])->name('attendance_corrections.update');

    // スタッフ別勤怠（月間）
    Route::get('users', [AdminUserController::class, 'index'])->name('users.index');
    Route::get('users/{user}/attendances', [AdminAttendanceController::class, 'showUserAttendances'])->name('users.attendances.index');

    Route::get('attendances', [AdminAttendanceController::class, 'index'])->name('attendances.index');
    Route::get('attendances/{id}', [AdminAttendanceController::class, 'show'])->name('attendances.show');
    Route::put('attendances/{id}', [AdminAttendanceController::class, 'update'])->name('attendances.update');
});
