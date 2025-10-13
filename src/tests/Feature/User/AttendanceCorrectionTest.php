<?php

namespace Tests\Feature\User;

use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use App\Models\AttendanceCorrection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class AttendanceCorrectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_validation_errors_on_invalid_input()
    {
        $user = User::factory()->create();
        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'work_date' => now()->toDateString(),
            'start_time' => '09:00:00',
            'end_time' => '18:00:00',
        ]);

        $this->actingAs($user)
            ->put(route('attendance.update', $attendance->id), [
                'start_time' => '19:00', // 出勤 > 退勤
                'end_time' => '18:00',
                'break_start_time' => '19:30',
                'break_end_time' => '20:00',
                'note' => '', // 備考なし
            ])
            ->assertSessionHasErrors(['start_time', 'break_start_time', 'break_end_time', 'note']);
    }

    public function test_user_can_submit_correction_request()
    {
        $user = User::factory()->create();
        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'start_time' => '09:00:00',
            'end_time' => '18:00:00',
        ]);

        $response = $this->actingAs($user)
            ->put(route('attendance.update', $attendance->id), [
                'start_time' => '09:30',
                'end_time' => '18:00',
                'break_start_time' => '12:00',
                'break_end_time' => '12:30',
                'note' => '寝坊しました',
            ]);

        $response->assertRedirect(); // 成功してリダイレクト
        $this->assertDatabaseHas('attendance_corrections', [
            'user_id' => $user->id,
            'attendance_id' => $attendance->id,
            'note' => '寝坊しました',
            'status' => 'pending',
        ]);
    }

    public function test_pending_corrections_are_visible()
    {
        $user = User::factory()->create();
        $correction = AttendanceCorrection::factory()->create([
            'user_id' => $user->id,
            'status' => 'pending',
        ]);

        $this->actingAs($user)
            ->get(route('user.attendance_corrections.list', ['status' => 'pending']))
            ->assertStatus(200)
            ->assertSee('承認待ち')
            ->assertSee($correction->note);
    }

    public function test_approved_corrections_are_visible()
    {
        $user = User::factory()->create();
        $correction = AttendanceCorrection::factory()->create([
            'user_id' => $user->id,
            'status' => 'approved',
        ]);

        $this->actingAs($user)
            ->get(route('user.attendance_corrections.list', ['status' => 'approved']))
            ->assertStatus(200)
            ->assertSee('承認済み')
            ->assertSee($correction->note);
    }

    public function test_each_correction_has_detail_link()
    {
        $user = User::factory()->create();
        $attendance = Attendance::factory()->create(['user_id' => $user->id]);
        $correction = AttendanceCorrection::factory()->create([
            'user_id' => $user->id,
            'attendance_id' => $attendance->id,
            'note' => '打刻ミス',
        ]);

        $this->actingAs($user)
            ->get(route('user.attendance_corrections.list', ['status' => 'pending']))
            ->assertSee(route('attendance.detail', $attendance->id));
    }
}
