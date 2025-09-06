<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function index()
    {
        $now = Carbon::now();
    $user = Auth::user();

    $attendance = Attendance::where('employee_name', $user->name)
        ->whereDate('work_date', $now->toDateString())
        ->first();

    // 曜日の配列
    $weekdays = ['日', '月', '火', '水', '木', '金', '土'];
    $weekday = $weekdays[$now->dayOfWeek]; // 0（日曜）〜6（土曜）

    return view('attendance.index', compact('now', 'attendance', 'weekday'));
    }

    public function clockIn()
    {
        $now = Carbon::now();
        $user = Auth::user();

        // すでに出勤済みかチェック
        $exists = Attendance::where('employee_name', $user->name)
            ->whereDate('work_date', $now->toDateString())
            ->exists();

        if ($exists) {
            return redirect()->back()->with('error', '本日はすでに出勤済みです。');
        }

        Attendance::create([
            'employee_name' => $user->name,
            'start_time' => $now->format('H:i:s'),
            'work_date' => $now->toDateString(),
        ]);

        return redirect()->back()->with('success', '出勤しました。');
    }

    public function clockOut()
    {
        $now = Carbon::now();
        $user = Auth::user();

        $attendance = Attendance::where('employee_name', $user->name)
            ->whereDate('work_date', $now->toDateString())
            ->first();

        if (!$attendance || $attendance->end_time) {
            return redirect()->back()->with('error', '退勤できません。');
        }

        $attendance->end_time = $now->format('H:i:s');
        // 労働時間計算などあればここで実装
        $attendance->save();

        return redirect()->back()->with('success', '退勤しました。');
    }

    public function breakStart()
    {
        // 休憩開始処理の例（モデルやDB設計に応じてカスタマイズしてください）
        $now = Carbon::now();
        $user = Auth::user();

        $attendance = Attendance::where('employee_name', $user->name)
            ->whereDate('work_date', $now->toDateString())
            ->first();

        if (!$attendance) {
            return redirect()->back()->with('error', '出勤記録がありません。');
        }

        $attendance->break_start_time = $now->format('H:i:s'); // break_start_time カラムを追加している場合
        $attendance->save();

        return redirect()->back()->with('success', '休憩開始しました。');
    }

    public function breakEnd()
    {
        $now = Carbon::now();
        $user = Auth::user();

        $attendance = Attendance::where('employee_name', $user->name)
            ->whereDate('work_date', $now->toDateString())
            ->first();

        if (!$attendance) {
            return redirect()->back()->with('error', '出勤記録がありません。');
        }

        $attendance->break_end_time = $now->format('H:i:s'); // break_end_time カラムを追加している場合
        $attendance->save();

        return redirect()->back()->with('success', '休憩終了しました。');
    }
}
