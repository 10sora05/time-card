<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AttendanceCorrection;
use App\Http\Requests\UpdateAttendanceRequest;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\AttendanceCorrectionRequest;

class AttendanceCorrectionController extends Controller
{
    public function store(AttendanceCorrectionRequest $request, $attendanceId)
    {
        try {
            AttendanceCorrection::create([
                'attendance_id' => $attendanceId,
                'user_id' => auth()->id(),
                'start_time' => $request->input('start_time'),
                'end_time' => $request->input('end_time'),
                'break_start_time' => $request->input('break_start_time'),
                'break_end_time' => $request->input('break_end_time'),
                'break2_start_time' => $request->input('break2_start_time'),
                'break2_end_time' => $request->input('break2_end_time'),
                'note' => $request->input('note'),
                'status' => 'pending',
            ]);

            return redirect()->back()->with('success', '修正申請を送信しました。');
        } catch (\Exception $e) {
            Log::error('勤怠修正申請の保存に失敗: ' . $e->getMessage());
            return redirect()->back()->with('error', '修正申請の送信に失敗しました。もう一度お試しください。');
        }
    }
}
