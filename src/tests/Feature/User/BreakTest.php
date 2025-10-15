<?php

namespace Tests\Feature\User;

use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class BreakTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    public function test_user_can_start_and_end_multiple_breaks()
    {
        $user = User::factory()->create();

        // 出勤済みの勤怠データを事前に作成
        $attendance = Attendance::create([
            'user_id' => $user->id,
            'employee_name' => $user->name,
            'work_date' => Carbon::now()->toDateString(),
            'start_time' => Carbon::now()->subHours(2)->format('H:i:s'),
        ]);

        // 1回目の休憩開始
        $this->actingAs($user)->post(route('attendance.break_start'));
        $attendance->refresh();
        $this->assertNotNull($attendance->break_start_time);

        // 1回目の休憩終了
        $this->actingAs($user)->post(route('attendance.break_end'));
        $attendance->refresh();
        $this->assertNotNull($attendance->break_end_time);

        // 2回目の休憩開始
        $this->actingAs($user)->post(route('attendance.break_start'));
        $attendance->refresh();
        $this->assertNotNull($attendance->break2_start_time);

        // 2回目の休憩終了
        $this->actingAs($user)->post(route('attendance.break_end'));
        $attendance->refresh();
        $this->assertNotNull($attendance->break2_end_time);
    }

    public function test_break_times_appear_in_attendance_detail()
    {
        $user = User::factory()->create();

        // 勤怠データを作成（休憩開始・終了時刻あり）
        $attendance = Attendance::create([
            'user_id' => $user->id,
            'employee_name' => $user->name,
            'work_date' => Carbon::now()->toDateString(),
            'start_time' => '09:00:00',
            'break_start_time' => '12:00:00',
            'break_end_time' => '12:30:00',
            // 2回目の休憩も追加可能
            'break2_start_time' => '15:00:00',
            'break2_end_time' => '15:15:00',
        ]);

        // 詳細画面にアクセス
        $response = $this->actingAs($user)->get(route('attendance.detail', $attendance->id));

        $response->assertStatus(200);

        // 休憩開始・終了時刻が詳細画面に表示されているかチェック
        $response->assertSee('12:00');
        $response->assertSee('12:30');
        $response->assertSee('15:00');
        $response->assertSee('15:15');
    }
}
