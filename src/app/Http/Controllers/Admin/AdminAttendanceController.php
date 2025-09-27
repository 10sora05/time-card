<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\Attendance;
use Carbon\Carbon;
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


    public function index(Request $request)
    {
        $user = \Auth::guard('admin')->user();

        $selectedDate = $request->input('date', Carbon::today()->toDateString());

        $previousDate = Carbon::parse($selectedDate)->subDay()->toDateString();
        $nextDate = Carbon::parse($selectedDate)->addDay()->toDateString();

        $attendances = Attendance::whereDate('work_date', $selectedDate)->get();

        return view('admin.attendances', compact(
            'attendances',
            'selectedDate',
            'previousDate',
            'nextDate'
        ));
    }

    public function show($id)
    {
        $attendance = Attendance::findOrFail($id);

        $isPending = AttendanceCorrection::where('attendance_id', $attendance->id)
            ->where('status', 'pending')
            ->exists();

        $layout = 'layouts.admin_app';

        // 管理者は常に管理者用のルートへ
        $formAction = route('admin.attendance.update', $attendance->id);

        return view('attendance.detail', compact('attendance', 'isPending', 'layout', 'formAction'));
    }

    public function update(UpdateAttendanceRequest $request, $id)
    {
        $attendance = Attendance::findOrFail($id);

        $input = $request->all();

        $removeSeconds = fn($time) => $time ? substr($time, 0, 5) : null;

        $input['break_start_time'] = $removeSeconds($input['break_start_time']);
        $input['break_end_time'] = $removeSeconds($input['break_end_time']);
        $input['break2_start_time'] = $removeSeconds($input['break2_start_time']);
        $input['break2_end_time'] = $removeSeconds($input['break2_end_time']);

        $attendance->update($input);

        AttendanceCorrection::create([
            'attendance_id' => $id,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'break_start_time' => $request->break_start_time,
            'break_end_time' => $request->break_end_time,
            'break2_start_time' => $request->break2_start_time,
            'break2_end_time' => $request->break2_end_time,
            'note' => $request->note,
            'status' => 'approved', // 管理者は即承認でも良い
        ]);

        return redirect()->back()->with('success', '勤怠を修正しました。');
    }

}