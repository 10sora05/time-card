<?php

namespace App\Models;

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [
        'employee_name',
        'start_time',
        'end_time',
        'break_minutes',
        'total_minutes',
        'work_date',
    ];

    // アクセサ：分を hh:mm 形式に変換（例）
    public function getFormattedTotalTimeAttribute()
    {
        if ($this->total_minutes === null) return null;
        $hours = floor($this->total_minutes / 60);
        $minutes = $this->total_minutes % 60;
        return sprintf('%02d:%02d', $hours, $minutes);
    }
}