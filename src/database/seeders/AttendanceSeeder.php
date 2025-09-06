<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Attendance;
use Carbon\Carbon;

class AttendanceSeeder extends Seeder
{
    public function run(): void
    {
        $employees = ['山田 太郎', '西 伶奈', '増田 一世', '山本 敬吉', '秋田 朋美', '中西 教夫'];

        for ($i = 1; $i <= 5; $i++) {
            foreach ($employees as $employee) {
                Attendance::create([
                    'employee_name' => "{$employee}{$i}",
                    'start_time' => '09:00:00',
                    'end_time' => '18:00:00',
                    'break_start_time' => '12:00:00',
                    'break_end_time' => '13:00:00',
                    'break_minutes' => 60,
                    'total_minutes' => 480,
                    'work_date' => Carbon::today()->subDays($i)->toDateString(),
                ]);
            }
        }
    }
}
