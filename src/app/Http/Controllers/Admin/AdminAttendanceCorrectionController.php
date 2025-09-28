<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\AdminAttendanceCorrectionController;
use App\Http\Requests\AttendanceCorrectionRequest;


public function update(AttendanceCorrectionRequest $request, $id)
{
    $attendance = Attendance::findOrFail($id);

    $attendance->update([
        'start_time' => $request->start_time,
        'end_time' => $request->end_time,
        'break_start_time' => $request->break_start_time,
        'break_end_time' => $request->break_end_time,
        'break2_start_time' => $request->break2_start_time,
        'break2_end_time' => $request->break2_end_time,
        'note' => $request->note,
    ]);

    return redirect()->back()->with('success', '勤怠情報を更新しました。');
}
