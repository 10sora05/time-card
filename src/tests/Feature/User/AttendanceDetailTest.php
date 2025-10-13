<?php

namespace Tests\Feature\User;

use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class AttendanceDetailTest extends TestCase
{
    use RefreshDatabase;

    public function test_attendance_detail_displays_correct_information()
    {
        $user = User::factory()->create();

        // 勤怠データを作成
        $attendance = Attendance::create([
            'user_id' => $user->id,
            'employee_name' => $user->name,
            'work_date' => '2025-10-01',
            'start_time' => '09:00:00',
            'end_time' => '18:00:00',
            'break_start_time' => '12:00:00',
            'break_end_time' => '12:30:00',
        ]);

        // 詳細画面にアクセス
        $response = $this->actingAs($user)->get(route('attendance.detail', $attendance->id));

        // ステータス確認
        $response->assertStatus(200);

        // 氏名確認
        $response->assertSee($user->name);

        // 日付確認（「2025年」や「10月1日」などに分かれているため両方検査）
        $response->assertSee('2025年');
        $response->assertSee('10月1日');

        // 出勤・退勤時間
        $response->assertSee('09:00');
        $response->assertSee('18:00');

        // 休憩時間
        $response->assertSee('12:00');
        $response->assertSee('12:30');
    }
}
