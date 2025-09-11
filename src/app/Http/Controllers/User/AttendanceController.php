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
        $weekday = $weekdays[$now->dayOfWeek];

        // 勤務外判定（9:00〜18:00 の間以外は勤務外）
        $isOutsideWorkHours = $now->lt(Carbon::createFromTime(9, 0)) || $now->gt(Carbon::createFromTime(18, 0));

        return view('attendance.index', compact('now', 'attendance', 'weekday', 'isOutsideWorkHours'));
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

    public function list(Request $request)
    {
        $user = Auth::user();
        $now = Carbon::now();

        // 対象月（クエリまたは今月）
        $targetMonth = $request->input('month', $now->format('Y-m'));
        $target = Carbon::parse($targetMonth);

        $startOfMonth = $target->copy()->startOfMonth();
        $endOfMonth = $target->copy()->endOfMonth();

        // 勤怠データ取得（社員名＆日付）を日付でキー付け
        $attendances = Attendance::where('employee_name', $user->name)
            ->whereBetween('work_date', [$startOfMonth->toDateString(), $endOfMonth->toDateString()])
            ->orderBy('work_date')
            ->get()
            ->keyBy('work_date');

        // 表示用データ（日付・曜日・勤怠）を作成
        $days = [];
        $weekdaysJP = ['日','月','火','水','木','金','土'];

        for ($date = $startOfMonth->copy(); $date->lte($endOfMonth); $date->addDay()) {
            $dateStr = $date->toDateString();
            $days[] = [
                'date' => $dateStr,                                // 2025-09-01
                'formatted' => $date->format('m/d'),               // 09/01
                'weekday' => $weekdaysJP[$date->dayOfWeek],        // 月など
                'attendance' => $attendances->get($dateStr),       // 勤怠データ（null含む）
            ];
        }

        return view('attendance.list', [
            'days' => $days,
            'targetMonth' => $target->format('Y-m'),
            'displayMonth' => $target->format('Y/m'),
            'prevMonth' => $target->copy()->subMonth()->format('Y-m'),
            'nextMonth' => $target->copy()->addMonth()->format('Y-m'),
        ]);
    }

}
