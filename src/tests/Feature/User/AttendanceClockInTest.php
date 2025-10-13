<?php

namespace Tests\Feature\User;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class AttendanceClockInTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_clock_in_once_per_day()
    {
        $user = User::factory()->create();

        // 1回目の出勤（成功）
        $response1 = $this->actingAs($user)->post(route('attendance.clockin'));
        $response1->assertRedirect();

        $this->assertDatabaseHas('attendances', [
            'employee_name' => $user->name,
            'work_date' => Carbon::now()->toDateString(),
        ]);

        // 2回目の出勤（失敗）
        $response2 = $this->actingAs($user)->post(route('attendance.clockin'));
        $response2->assertRedirect();
        $response2->assertSessionHas('error', '本日はすでに出勤済みです。');

        // 勤怠一覧で出勤時刻が表示されるか
        $response3 = $this->actingAs($user)->get('/attendance/list?month=' . Carbon::now()->format('Y-m'));
        $response3->assertStatus(200);
        $response3->assertSee(Carbon::now()->format('H:i'));
    }
}
