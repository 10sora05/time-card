<?php

namespace Tests\Feature\User;

use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class AttendanceListTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    public function test_attendance_list_displays_users_own_records()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        // ログインユーザーの勤怠（今月）
        Attendance::create([
            'user_id' => $user->id,
            'employee_name' => $user->name,
            'work_date' => Carbon::now()->toDateString(),
            'start_time' => '09:00:00',
            'end_time' => '18:00:00',
        ]);

        // 他ユーザーの勤怠（表示されない）
        Attendance::create([
            'user_id' => $otherUser->id,
            'employee_name' => $otherUser->name,
            'work_date' => Carbon::now()->toDateString(),
            'start_time' => '10:00:00',
            'end_time' => '19:00:00',
        ]);

        $response = $this->actingAs($user)->get('/attendance/list');

        $response->assertStatus(200);
        $response->assertSee('09:00');
        $response->assertSee('18:00');
        $response->assertDontSee('10:00'); // 他人のデータは表示されない
    }

    public function test_current_month_is_displayed_when_no_month_selected()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/attendance/list');

        $currentMonth = Carbon::now()->format('Y/m');
        $response->assertStatus(200);
        $response->assertSee($currentMonth);
    }

    public function test_can_navigate_to_previous_and_next_month()
    {
        $user = User::factory()->create();

        $targetMonth = Carbon::now()->startOfMonth();

        // 前月データ
        $prevMonth = $targetMonth->copy()->subMonth();
        Attendance::create([
            'user_id' => $user->id,
            'employee_name' => $user->name,
            'work_date' => $prevMonth->copy()->day(10)->toDateString(),
            'start_time' => '08:00:00',
        ]);

        // 翌月データ
        $nextMonth = $targetMonth->copy()->addMonth();
        Attendance::create([
            'user_id' => $user->id,
            'employee_name' => $user->name,
            'work_date' => $nextMonth->copy()->day(10)->toDateString(),
            'start_time' => '10:00:00',
        ]);

        // 前月遷移
        $responsePrev = $this->actingAs($user)->get('/attendance/list?month=' . $prevMonth->format('Y-m'));
        $responsePrev->assertStatus(200);
        $responsePrev->assertSee('08:00');

        // 翌月遷移
        $responseNext = $this->actingAs($user)->get('/attendance/list?month=' . $nextMonth->format('Y-m'));
        $responseNext->assertStatus(200);
        $responseNext->assertSee('10:00');
    }

    public function test_can_navigate_to_detail_page()
    {
        $user = User::factory()->create();

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'employee_name' => $user->name,
            'work_date' => Carbon::now()->toDateString(),
            'start_time' => '09:30:00',
        ]);

        $response = $this->actingAs($user)->get('/attendance/list');

        // 「詳細」リンクが存在し、正しい URL に遷移する
        $response->assertSee(route('attendance.detail', $attendance->id));
    }
}
