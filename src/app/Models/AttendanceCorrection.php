<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceCorrection extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'attendance_id',
        'user_id',
        'start_time',
        'end_time',
        'break_start_time',
        'break_end_time',
        'break2_start_time',
        'break2_end_time',
        'note',
        'status',
    ];

    // 申請者（ユーザー）
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 勤怠
    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }
}