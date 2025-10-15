<?php

namespace Tests\Feature\User;

use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class ClockOutTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    public function test_user_can_clock_out_and_see_it_in_attendance_list()
    {
        $user = User::factory()->create();

        // 出勤済みの勤怠データを作成（退勤前の状態）
        $attendance = Attendance::create([
            'user_id' => $user->id,
            'employee_name' => $user->name,
            'work_date' => Carbon::now()->toDateString(),
            'start_time' => '09:00:00',
        ]);

        // 退勤処理（POST）
        $this->actingAs($user)->post(route('attendance.clockout'));

        // 退勤時刻が記録されたか確認
        $attendance->refresh();
        $this->assertNotNull($attendance->end_time);

        // 勤怠一覧画面に退勤時刻が表示されるか確認
        $response = $this->actingAs($user)->get(route('attendance.list', [
            'month' => Carbon::now()->format('Y-m')
        ]));

        $response->assertStatus(200);
        $response->assertSee(Carbon::parse($attendance->end_time)->format('H:i'));
    }
}
