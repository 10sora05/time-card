<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;
use App\Models\Admin;
use App\Models\User;
use App\Models\Attendance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class UserAttendancesTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_user_attendances_and_navigate_months_and_access_detail()
    {
        // 管理者ユーザー作成
        $admin = Admin::factory()->create();

        // 一般ユーザー作成
        $user = User::factory()->create([
            'name' => '山田 太郎',
        ]);

        // 勤怠データを複数作成（特定月）
        $targetMonth = '2025-10';
        $workDate = Carbon::parse($targetMonth . '-15')->toDateString();

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'employee_name' => $user->name,
            'work_date' => $workDate,
            'start_time' => '09:00:00',
            'end_time' => '18:00:00',
            'break_minutes' => 60,
            'total_minutes' => 480,
        ]);

        // 1つ前の月と翌月の日付
        $prevMonth = Carbon::parse($targetMonth)->subMonth()->format('Y-m');
        $nextMonth = Carbon::parse($targetMonth)->addMonth()->format('Y-m');

        // 管理者としてログインして勤怠一覧ページへGETアクセス
        $response = $this->actingAs($admin, 'admin')
            ->get(route('admin.users.attendances.index', ['user' => $user->id, 'month' => $targetMonth]));

        $response->assertStatus(200);

        // ユーザーの氏名
        $response->assertSee($user->name);

        // 勤怠情報の表示確認（該当日付の開始時間・終了時間・休憩・合計）
        $response->assertSee('09:00');
        $response->assertSee('18:00');
        $response->assertSee('1:00');   // 休憩60分 = 1:00
        $response->assertSee('8:00');   // 合計480分 = 8:00

        // 前月・翌月リンクのURL確認
        $response->assertSee(route('admin.users.attendances.index', ['user' => $user->id, 'month' => $prevMonth]));
        $response->assertSee(route('admin.users.attendances.index', ['user' => $user->id, 'month' => $nextMonth]));

        // 詳細リンクが勤怠IDを使っているか確認
        $response->assertSee(route('admin.attendances.show', $attendance->id));
    }
}
