<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;
use Carbon\Carbon;

class AdminAttendanceController extends Controller
{
    // 管理者用勤怠一覧表示
    public function index(Request $request)
    {
        $date = $request->input('date', Carbon::now()->toDateString());

        $attendances = Attendance::where('work_date', $date)->get();

        return view('admin.attendance.index', compact('attendances', 'date'));
    }
}
