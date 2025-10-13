<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Attendance extends Model
{
    use HasFactory;
    
    protected $fillable = [
    'user_id',
    'employee_name',
    'start_time',
    'end_time',
    'break_start_time',
    'break_end_time',
    'break2_start_time',
    'break2_end_time',
    'break_minutes',
    'total_minutes',
    'work_date',
    'note',
    ];

    // アクセサ：分を hh:mm 形式に変換（例）
    public function getFormattedTotalTimeAttribute()
    {
        if ($this->total_minutes === null) return null;
        $hours = floor($this->total_minutes / 60);
        $minutes = $this->total_minutes % 60;
        return sprintf('%02d:%02d', $hours, $minutes);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}