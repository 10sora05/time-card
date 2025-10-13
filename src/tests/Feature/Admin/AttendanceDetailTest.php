<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;
use App\Models\Admin;
use App\Models\Attendance;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AttendanceDetailTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_attendance_detail()
    {
        $admin = Admin::factory()->create();

        $attendance = Attendance::factory()->create([
            'employee_name' => '山田 太郎',
            'work_date' => now()->format('Y-m-d'),
            'start_time' => '09:00:00',
            'end_time' => '18:00:00',
            'break_start_time' => '12:00:00',
            'break_end_time' => '13:00:00',
            'break_minutes' => 60,
            'note' => '特になし',
        ]);

        $response = $this->actingAs($admin, 'admin')->get(route('admin.attendances.show', ['id' => $attendance->id]));

        $response->assertStatus(200);
        $response->assertSee('山田 太郎');
        $response->assertSee('09:00');
        $response->assertSee('18:00');
        $response->assertSee('12:00');
        $response->assertSee('13:00');
        $response->assertSee('特になし');
    }

    public function test_validation_errors_when_updating_attendance_with_invalid_times_or_missing_remarks()
    {
        $admin = Admin::factory()->create();

        $attendance = Attendance::factory()->create();

        $invalidData = [
            'start_time' => '19:00:00',           // 出勤時間が退勤時間より後
            'end_time' => '18:00:00',
            'break_start_time' => '19:30:00',     // 休憩開始が退勤後
            'break_end_time' => '20:00:00',       // 休憩終了が退勤後
            'note' => '',                         // 備考未入力
        ];

        $response = $this->actingAs($admin, 'admin')
            ->from(route('admin.attendances.show', ['id' => $attendance->id]))
            ->put(route('admin.attendances.update', ['id' => $attendance->id]), $invalidData);

        $response->assertRedirect(route('admin.attendances.show', ['id' => $attendance->id]));
        $response->assertSessionHasErrors([
            'start_time',
            'break_start_time',
            'break_end_time',
            'note',
        ]);
    }
}
