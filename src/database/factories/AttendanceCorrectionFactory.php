<?php

namespace Database\Factories;

use App\Models\AttendanceCorrection;
use App\Models\User;
use App\Models\Attendance;
use Illuminate\Database\Eloquent\Factories\Factory;

class AttendanceCorrectionFactory extends Factory
{
    protected $model = AttendanceCorrection::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'attendance_id' => Attendance::factory(),
            'note' => 'テスト申請内容',
            'status' => 'pending',
        ];
    }
}
