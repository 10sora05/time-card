<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\AttendanceCorrection;
use Illuminate\Support\Facades\Auth;


class AttendanceCorrectionController extends Controller
{
    public function store(Request $request, $id)
    {
        $attendance = Attendance::findOrFail($id);

        $exists = AttendanceCorrection::where('attendance_id', $attendance->id)
            ->where('status', 'pending')
            ->exists();

        if ($exists) {
            return redirect()->back()->withErrors(['すでに修正申請が存在します。']);
        }

        AttendanceCorrection::create([
            'attendance_id' => $attendance->id,
            'user_id' => auth('web')->id(),
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
    }

    public function list(Request $request)
    {
        $user = Auth::user();

        // ?status=pending or approved を取得（デフォルトは pending）
        $status = $request->input('status', 'pending');

        $query = AttendanceCorrection::with('attendance')
            ->where('user_id', $user->id);

        // 絞り込み（'pending' or 'approved' のみ許可）
        if (in_array($status, ['pending', 'approved'])) {
            $query->where('status', $status);
        }

        $corrections = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('stamp_correction_request.list', compact('corrections', 'status'));
    }

    public function show($id)
    {
        $correction = AttendanceCorrection::with('attendance')
            ->where('id', $id)
            ->where('user_id', Auth::id()) // セキュリティ: 他人のデータは弾く
            ->firstOrFail();

        return view('attendance.request_detail', compact('correction'));
    }
}
