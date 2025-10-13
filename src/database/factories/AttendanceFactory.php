<?php

namespace Database\Factories;

use App\Models\Attendance;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AttendanceFactory extends Factory
{
    protected $model = Attendance::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'employee_name' => $this->faker->name,
            'work_date' => now()->toDateString(),
            'start_time' => '09:00:00',
            'end_time' => '18:00:00',
        ];
    }
}
