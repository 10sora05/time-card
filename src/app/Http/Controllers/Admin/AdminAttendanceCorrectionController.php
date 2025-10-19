<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AttendanceCorrection;

class AdminAttendanceCorrectionController extends Controller
{
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

        return view('admin.requests.index', compact('corrections', 'status'));
    }

    /**
     * 申請詳細(承認画面)
     */
    public function show($id)
    {
        $correction = AttendanceCorrection::with('attendance', 'user')->findOrFail($id);
        return view('admin.requests.detail', [
            'correction' => $correction,
        ]);
    }

    // 承認処理
    public function update(Request $request, $id)
    {
        $correction = AttendanceCorrection::findOrFail($id);
        $status = $request->input('status');

        if (!in_array($status, ['pending', 'approved', 'rejected'])) {
            return redirect()->back()->withErrors('無効なステータスです。');
        }

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

        $correction->status = $status;
        $correction->save();

        return redirect()->route('admin.attendance_corrections.index')->with('success', '申請の状態を更新しました。');
    }

    public function approve($id)
    {
        $correction = AttendanceCorrection::findOrFail($id);
        $correction->status = 'approved';
        $correction->save();

        return redirect()->route('admin.attendance_corrections.index', ['status' => 'pending']);
    }

}
