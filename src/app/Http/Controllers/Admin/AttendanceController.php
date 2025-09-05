<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Illuminate\Http\Request;
use Carbon\Carbon;


class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $selectedDate = $request->input('date') ?? now()->toDateString();
        $previousDate = Carbon::parse($selectedDate)->subDay()->toDateString();
        $nextDate = Carbon::parse($selectedDate)->addDay()->toDateString();

        $attendances = Attendance::where('work_date', $selectedDate)->get();

        return view('admin.attendance', [
            'selectedDate' => $selectedDate,
            'previousDate' => $previousDate,
            'nextDate' => $nextDate,
            'attendances' => $attendances,
        ]);
    }
}