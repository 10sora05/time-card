<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AttendanceCorrection;
use App\Http\Requests\UpdateAttendanceRequest;
use Illuminate\Support\Facades\Log;

class AttendanceCorrectionController extends Controller
{
    public function store(UpdateAttendanceRequest $request, $attendanceId)
    {
        Log::info('store method called for attendance id: ' . $attendanceId);
        // 必要なら追加ログ
        // Log::info('request data: ', $request->all());

        AttendanceCorrection::create([
            'attendance_id' => $attendanceId,
            'user_id' => auth()->id(),
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'break_start_time' => $request->break_start_time,
            'break_end_time' => $request->break_end_time,
            'break2_start_time' => $request->break2_start_time,
            'break2_end_time' => $request->break2_end_time,
            'note' => $request->note,
            'status' => 'pending',
        ]);

        return redirect()->back()->with('success', '修正申請を送信しました。');
    }
}
