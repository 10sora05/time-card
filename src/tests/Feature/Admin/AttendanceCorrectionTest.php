<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;
use App\Models\Admin;
use App\Models\User;
use App\Models\Attendance;
use App\Models\AttendanceCorrection;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AttendanceCorrectionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    public function test_admin_can_view_pending_corrections()
    {
        $admin = Admin::factory()->create();
        $user = User::factory()->create();

        $attendance = Attendance::factory()->create(['user_id' => $user->id]);
        $correction = AttendanceCorrection::factory()->create([
            'attendance_id' => $attendance->id,
            'user_id' => $user->id,
            'status' => 'pending',
            'note' => '時間間違い修正',
        ]);

        $response = $this->actingAs($admin, 'admin')->get(route('admin.attendance_corrections.index', ['status' => 'pending']));

        $response->assertStatus(200);
        $response->assertSee('承認待ち');
        $response->assertSee($user->name);
        $response->assertSee('時間間違い修正');
    }

    public function test_admin_can_view_approved_corrections()
    {
        $admin = Admin::factory()->create();
        $user = User::factory()->create();

        $attendance = Attendance::factory()->create(['user_id' => $user->id]);
        $correction = AttendanceCorrection::factory()->create([
            'attendance_id' => $attendance->id,
            'user_id' => $user->id,
            'status' => 'approved',
            'note' => '退勤時間修正',
        ]);

        $response = $this->actingAs($admin, 'admin')->get(route('admin.attendance_corrections.index', ['status' => 'approved']));

        $response->assertStatus(200);
        $response->assertSee('承認済み');
        $response->assertSee('退勤時間修正');
    }

    public function test_admin_can_view_correction_detail()
    {
        $admin = Admin::factory()->create();
        $user = User::factory()->create();

        $attendance = Attendance::factory()->create(['user_id' => $user->id]);
        $correction = AttendanceCorrection::factory()->create([
            'attendance_id' => $attendance->id,
            'user_id' => $user->id,
            'status' => 'pending',
            'note' => '詳細確認テスト',
        ]);

        $response = $this->actingAs($admin, 'admin')->get('/admin/requests/' . $correction->id);

        $response->assertStatus(200);
        $response->assertSee('詳細確認テスト');
        $response->assertSee($user->name);
    }

    public function test_admin_can_approve_correction_request()
    {
        $admin = Admin::factory()->create();
        $user = User::factory()->create();

        $attendance = Attendance::factory()->create(['user_id' => $user->id]);
        $correction = AttendanceCorrection::factory()->create([
            'attendance_id' => $attendance->id,
            'user_id' => $user->id,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($admin, 'admin')->post('/admin/requests/' . $correction->id . '/approve');

        $response->assertRedirect('/admin/requests?status=pending');
        $this->assertDatabaseHas('attendance_corrections', [
            'id' => $correction->id,
            'status' => 'approved',
        ]);
    }
}
