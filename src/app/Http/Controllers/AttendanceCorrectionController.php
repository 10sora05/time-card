<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AttendanceCorrection;
use App\Http\Requests\UpdateAttendanceRequest;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\AttendanceCorrectionRequest;

class AttendanceCorrectionController extends Controller
{
    public function store(Request $request, $attendanceId)
    {
        $attendance = Attendance::findOrFail($attendanceId);

        // すでに申請中の場合、エラー返す（任意）
        if ($attendance->is_pending) {
            return back()->withErrors(['申請中のため修正できません。']);
        }

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

        // 勤怠データに申請中フラグを立てる（必要に応じて）
        $attendance->update(['is_pending' => true]);

        return redirect()->back()->with('message', '修正申請を送信しました。');
    }
}
