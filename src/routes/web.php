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
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

// メール認証通知表示ページ
Route::get('/email/verify', function () {
    return view('auth.verify-email'); // 標準的なビューを用意しておく
})->middleware('auth')->name('verification.notice');

// メール認証リンク処理
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill(); // 認証完了処理

    return redirect('/attendance'); // 認証後のリダイレクト先
})->middleware(['auth', 'signed'])->name('verification.verify');

// メール認証再送信処理
Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();

    // メール再送信後にトップページへ移動
    return redirect('/attendance')->with('message', '認証メールを再送信しました。');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

// ユーザー登録
Route::get('/user_register', [UserRegisterController::class, 'show'])->name('user.register.show');
Route::post('/user_register', [UserRegisterController::class, 'register'])->name('user.register');

// 一般ユーザー用ログイン・ログアウト
Route::get('/login', [UserLoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [UserLoginController::class, 'login']);
Route::post('/logout', [UserLoginController::class, 'logout'])->name('logout');

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

// 管理者ユーザー用ルート
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
