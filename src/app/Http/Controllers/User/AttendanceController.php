<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use App\Models\AttendanceCorrection;
use App\Http\Requests\UpdateAttendanceRequest;
use Illuminate\Support\Facades\Log;

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

        //  勤務時間と休憩時間の計算ロジック
        $workDuration = null;
        $breakDuration = null;

        if ($attendance) {
            $startTime = $attendance->start_time ? Carbon::createFromFormat('H:i:s', $attendance->start_time) : null;
            $endTime = $attendance->end_time ? Carbon::createFromFormat('H:i:s', $attendance->end_time) : null;

            // 休憩1
            $breakStart1 = $attendance->break_start_time ? Carbon::createFromFormat('H:i:s', $attendance->break_start_time) : null;
            $breakEnd1 = $attendance->break_end_time ? Carbon::createFromFormat('H:i:s', $attendance->break_end_time) : null;

            // 休憩2
            $breakStart2 = $attendance->break2_start_time ? Carbon::createFromFormat('H:i:s', $attendance->break2_start_time) : null;
            $breakEnd2 = $attendance->break2_end_time ? Carbon::createFromFormat('H:i:s', $attendance->break2_end_time) : null;

            // 休憩の合計秒数
            $totalBreakSeconds = 0;

            if ($breakStart1 && $breakEnd1 && $breakEnd1->gt($breakStart1)) {
                $totalBreakSeconds += $breakEnd1->diffInSeconds($breakStart1);
            }

            if ($breakStart2 && $breakEnd2 && $breakEnd2->gt($breakStart2)) {
                $totalBreakSeconds += $breakEnd2->diffInSeconds($breakStart2);
            }

            // 休憩時間（CarbonIntervalに変換）
            if ($totalBreakSeconds > 0) {
                $breakDuration = CarbonInterval::seconds($totalBreakSeconds);
            }

            // 勤務時間（休憩を除く）
            if ($startTime && $endTime && $endTime->gt($startTime)) {
                $totalWorkSeconds = $endTime->diffInSeconds($startTime);
                $workSeconds = max(0, $totalWorkSeconds - $totalBreakSeconds);
                $workDuration = CarbonInterval::seconds($workSeconds);
            }

            if ($attendance) {
                // DBに保存されている total_minutes / break_minutes を CarbonInterval に変換
                if ($attendance->total_minutes !== null) {
                    $workDuration = CarbonInterval::minutes($attendance->total_minutes);
                }

                if ($attendance->break_minutes !== null) {
                    $breakDuration = CarbonInterval::minutes($attendance->break_minutes);
                }
            }

        }

        return view('attendance.index', compact(
            'now',
            'attendance',
            'weekday',
            'isOutsideWorkHours',
            'workDuration',
            'breakDuration'
        ));
    }

    public function clockIn()
    {
        $now = Carbon::now();
        $user = Auth::user();

        // 出勤済みか確認
        $exists = Attendance::where('employee_name', $user->name)
            ->whereDate('work_date', $now->toDateString())
            ->exists();

        if (app()->environment('production') && $exists) {
            return redirect()->back()->with('error', '本日はすでに出勤済みです。');
        }

        // 出勤記録を作成
        Attendance::create([
            'user_id' => $user->id,
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

        // 終了時間を保存
        $attendance->end_time = $now->format('H:i:s');

        // ===== 勤務時間・休憩時間の計算 =====
        $startTime = $attendance->start_time ? Carbon::createFromFormat('H:i:s', $attendance->start_time) : null;
        if (!$startTime) {
            return redirect()->back()->with('error', '出勤時間が記録されていません。');
        }

        $breakStart1 = $attendance->break_start_time ? Carbon::createFromFormat('H:i:s', $attendance->break_start_time) : null;
        $breakEnd1 = $attendance->break_end_time ? Carbon::createFromFormat('H:i:s', $attendance->break_end_time) : null;

        $breakStart2 = $attendance->break2_start_time ? Carbon::createFromFormat('H:i:s', $attendance->break2_start_time) : null;
        $breakEnd2 = $attendance->break2_end_time ? Carbon::createFromFormat('H:i:s', $attendance->break2_end_time) : null;

        // 休憩時間（秒）
        $totalBreakSeconds = 0;
        if ($breakStart1 && $breakEnd1 && $breakEnd1->gt($breakStart1)) {
            $totalBreakSeconds += $breakEnd1->diffInSeconds($breakStart1);
        }
        if ($breakStart2 && $breakEnd2 && $breakEnd2->gt($breakStart2)) {
            $totalBreakSeconds += $breakEnd2->diffInSeconds($breakStart2);
        }

        // 勤務時間（秒）
        $totalWorkSeconds = $now->diffInSeconds($startTime);
        $workSeconds = max(0, $totalWorkSeconds - $totalBreakSeconds);

        // 保存
        $attendance->break_minutes = floor($totalBreakSeconds / 60);
        $attendance->total_minutes = floor($workSeconds / 60);
        $attendance->save();

        return redirect()->back()->with('success', '退勤しました。');
    }

    public function breakStart()
    {
        $now = Carbon::now();
        $user = Auth::user();

        $attendance = Attendance::where('employee_name', $user->name)
            ->whereDate('work_date', $now->toDateString())
            ->first();

        if (!$attendance) {
            return redirect()->back()->with('error', '出勤記録がありません。');
        }

        // 1回目の休憩が未記録なら
        if (!$attendance->break_start_time) {
            $attendance->break_start_time = $now->format('H:i:s');
            $attendance->save();
            return redirect()->back()->with('success', '休憩（1回目）を開始しました。');
        }

        // 1回目の休憩がまだ終了していない場合は拒否
        if ($attendance->break_start_time && !$attendance->break_end_time) {
            return redirect()->back()->with('error', '休憩（1回目）が終了していません。');
        }

        // 2回目の休憩が未記録なら
        if (!$attendance->break2_start_time) {
            $attendance->break2_start_time = $now->format('H:i:s');
            $attendance->save();
            return redirect()->back()->with('success', '休憩（2回目）を開始しました。');
        }

        // 2回目の休憩がまだ終了していない場合は拒否
        if ($attendance->break2_start_time && !$attendance->break2_end_time) {
            return redirect()->back()->with('error', '休憩（2回目）が終了していません。');
        }

        return redirect()->back()->with('error', '休憩は2回までです。');
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

        // 1回目の休憩が進行中なら終了
        if ($attendance->break_start_time && !$attendance->break_end_time) {
            $attendance->break_end_time = $now->format('H:i:s');
            $attendance->save();
            return redirect()->back()->with('success', '休憩（1回目）を終了しました。');
        }

        // 2回目の休憩が進行中なら終了
        if ($attendance->break2_start_time && !$attendance->break2_end_time) {
            $attendance->break2_end_time = $now->format('H:i:s');
            $attendance->save();
            return redirect()->back()->with('success', '休憩（2回目）を終了しました。');
        }

        return redirect()->back()->with('error', '現在、進行中の休憩はありません。');
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

    public function edit($id)
    {
        $attendance = Attendance::findOrFail($id);

        // 承認待ち申請があるかチェック
        $isPending = AttendanceCorrection::where('attendance_id', $attendance->id)
                    ->where('status', 'pending')
                    ->exists();

        return view('attendance.detail', compact('attendance', 'isPending'));
    }
         
    public function show($id)
    {
        $attendance = Attendance::findOrFail($id);

        $isAdmin = auth('admin')->check();
        $isUser = auth('web')->check();

        $isPending = AttendanceCorrection::where('attendance_id', $attendance->id)
            ->when($isUser, fn($q) => $q->where('user_id', auth()->id()))
            ->where('status', 'pending')
            ->exists();

        $layout = $isAdmin ? 'layouts.admin_app' : 'layouts.app';
        $canEdit = $isAdmin || ($isUser && !$isPending);

        return view('attendance.detail', compact('attendance', 'isPending', 'layout', 'canEdit'));
    }
    
    public function update(Request $request, $id)
    {
        $attendance = Attendance::findOrFail($id);

        // 修正がすでに申請中なら弾く
        $exists = AttendanceCorrection::where('attendance_id', $attendance->id)
                    ->where('status', 'pending')
                    ->exists();

        if ($exists) {
            return redirect()->back()->withErrors(['すでに修正申請が存在します。承認をお待ちください。']);
        }

        // 申請データの保存
        AttendanceCorrection::create([
            'attendance_id' => $attendance->id,
            'user_id' => auth('web')->id(),
            'start_time' => $request->input('start_time'),
            'end_time' => $request->input('end_time'),
            'break_start_time' => $request->input('break_start_time'),
            'break_end_time' => $request->input('break_end_time'),
            'break2_start_time' => $request->input('break2_start_time'),
            'break2_end_time' => $request->input('break2_end_time'),
            'note' => $request->input('note'),
            'status' => 'pending',
        ]);

        return redirect()->back()->with('success', '修正申請を送信しました。承認をお待ちください。');
    }

}
