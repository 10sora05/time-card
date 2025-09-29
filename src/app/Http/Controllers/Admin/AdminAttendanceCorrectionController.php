<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;  // Attendanceモデルをインポート
use App\Http\Requests\AttendanceCorrectionRequest;
use App\Models\AttendanceCorrection;

class AdminAttendanceCorrectionController extends Controller
{
    public function update(AttendanceCorrectionRequest $request, $id)
    {
        // 修正申請レコードを取得
        $correction = AttendanceCorrection::findOrFail($id);

        // ステータス（approved or rejected）をリクエストから受け取る想定
        $status = $request->input('status');

        if (!in_array($status, ['approved', 'rejected'])) {
            return redirect()->back()->withErrors('無効なステータスです。');
        }

        // 承認ならAttendanceも更新
        if ($status === 'approved') {
            $attendance = $correction->attendance;
            if ($attendance) {
                $attendance->update([
                    'start_time' => $correction->start_time,
                    'end_time' => $correction->end_time,
                    'break_start_time' => $correction->break_start_time,
                    'break_end_time' => $correction->break_end_time,
                    'break2_start_time' => $correction->break2_start_time,
                    'break2_end_time' => $correction->break2_end_time,
                    'note' => $correction->note,
                ]);
            }
        }

        // 修正申請の状態更新
        $correction->status = $status;
        $correction->reviewed_at = now(); // もし審査日時も管理したいなら
        $correction->save();

        return redirect()->back()->with('success', '修正申請の状態を更新しました。');
    }

    /**
     * 修正申請一覧（管理者用）
     */
    public function index(Request $request)
    {
        $status = $request->input('status', 'pending'); // デフォルト: 承認待ち

        $query = AttendanceCorrection::with(['user', 'attendance']);

        if (in_array($status, ['pending', 'approved'])) {
            $query->where('status', $status);
        }

        $corrections = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('admin.requests', compact('corrections', 'status'));
    }

    /**
     * 詳細画面（申請詳細）
     */
    public function show($id)
    {
        $correction = AttendanceCorrection::with(['user', 'attendance'])->findOrFail($id);

        return view('admin.request_detail', compact('correction'));
    }

}
