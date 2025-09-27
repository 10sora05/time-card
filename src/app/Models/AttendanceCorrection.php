<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceCorrection extends Model
{
    protected $fillable = [
        'attendance_id',
        'start_time',
        'end_time',
        'break_start_time',
        'break_end_time',
        'break2_start_time',
        'break2_end_time',
        'note',
        'status',
    ];

    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }
}