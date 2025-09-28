<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;  // Attendanceモデルをインポート
use App\Http\Requests\AttendanceCorrectionRequest;

class AdminAttendanceCorrectionController extends Controller
{
    public function update(AttendanceCorrectionRequest $request, $id)
    {
        // 勤怠情報の取得
        $attendance = Attendance::findOrFail($id);

        // 勤怠修正申請に対する承更新処理
        $attendance->update([
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'break_start_time' => $request->break_start_time,
            'break_end_time' => $request->break_end_time,
            'break2_start_time' => $request->break2_start_time,
            'break2_end_time' => $request->break2_end_time,
            'note' => $request->note,
        ]);

        // 更新後にメッセージを表示してリダイレクト
        return redirect()->back()->with('success', '修正申請が更新されました。');
    }
}
