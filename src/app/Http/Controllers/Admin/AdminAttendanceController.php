<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Attendance;
use App\Models\User;
use App\Models\AttendanceCorrection;
use App\Http\Requests\UpdateAttendanceRequest;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class AdminAttendanceController extends Controller
{

    protected function guard()
    {
        return Auth::guard('admin');
    }

    protected $redirectTo = '/admin/attendances';

    // 全ユーザーの勤怠一覧（日付単位）
    public function index(Request $request)
    {
        $selectedDate = $request->input('date', Carbon::today()->toDateString());

        $previousDate = Carbon::parse($selectedDate)->subDay()->toDateString();
        $nextDate = Carbon::parse($selectedDate)->addDay()->toDateString();

        $attendances = Attendance::whereDate('work_date', $selectedDate)->get();

        return view('admin.attendances.index', compact(
            'attendances',
            'selectedDate',
            'previousDate',
            'nextDate'
        ));
    }

    // 管理者が見る、特定ユーザーの月間勤怠一覧
    public function showUserAttendances(Request $request, User $user)
    {
        // 対象月取得
        $targetMonth = $request->input('month', Carbon::now()->format('Y-m'));

        $startOfMonth = Carbon::parse($targetMonth)->startOfMonth();
        $endOfMonth = Carbon::parse($targetMonth)->endOfMonth();

        $prevMonth = $startOfMonth->copy()->subMonth()->format('Y-m');
        $nextMonth = $startOfMonth->copy()->addMonth()->format('Y-m');

        $attendances = $user->attendances()
            ->whereBetween('work_date', [$startOfMonth->toDateString(), $endOfMonth->toDateString()])
            ->get()
            ->keyBy('work_date');

        $days = [];
        $date = $startOfMonth->copy();
        while ($date <= $endOfMonth) {
            $workDate = $date->toDateString();
            $days[] = [
                'date' => $workDate,
                'formatted' => $date->format('Y/m/d'),
                'weekday' => ['日', '月', '火', '水', '木', '金', '土'][$date->dayOfWeek],
                'attendance' => $attendances->get($workDate),
            ];
            $date->addDay();
        }

        return view('admin.users.attendances', compact(
            'user',
            'targetMonth',
            'prevMonth',
            'nextMonth',
            'days'
        ));
    }

    public function show($id)
    {
        $attendance = Attendance::findOrFail($id);

        $isPending = AttendanceCorrection::where('attendance_id', $attendance->id)
            ->where('status', 'pending')
            ->exists();

        $canEdit = true;

        return view('admin.attendances.detail', compact('attendance', 'isPending', 'canEdit'));
    }

    public function update(UpdateAttendanceRequest $request, $id)
    {

        \Log::debug('更新リクエスト', $request->all());

        $attendance = Attendance::findOrFail($id);

        // 管理者が勤怠情報を更新処理
        $attendance->update([
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'break_start_time' => $request->break_start_time,
            'break_end_time' => $request->break_end_time,
            'break2_start_time' => $request->break2_start_time,
            'break2_end_time' => $request->break2_end_time,
            'note' => $request->note,
        ]);

        return redirect()->route('admin.attendances.show', $attendance->id)
                        ->with('success', '勤怠情報が更新されました');
    }
}