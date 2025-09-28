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

        return view('attendance.detail', [
            'attendance' => $attendance,
            'isPending' => $isPending,
            'layout' => 'layouts.admin_app',
        ]);
    }

    public function update(Request $request, $id)
    {
        $attendance = Attendance::findOrFail($id);

        $attendance->update([
            'start_time' => $request->input('start_time'),
            'end_time' => $request->input('end_time'),
            'break_start_time' => $request->input('break_start_time'),
            'break_end_time' => $request->input('break_end_time'),
            'break2_start_time' => $request->input('break2_start_time'),
            'break2_end_time' => $request->input('break2_end_time'),
            'note' => $request->input('note'),
        ]);

        return redirect()->back()->with('success', '勤怠情報を更新しました。');
    }

}