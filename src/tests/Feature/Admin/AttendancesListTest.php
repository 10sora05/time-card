<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;
use App\Models\Admin;
use App\Models\Attendance;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AttendancesListTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_attendance_list_with_correct_date_and_navigation_links()
    {
        // 管理者ユーザー作成
        $admin = Admin::factory()->create();
        
        // テスト用の日付と前日・翌日を設定
        $selectedDate = now()->format('Y-m-d');
        $previousDate = now()->subDay()->format('Y-m-d');
        $nextDate = now()->addDay()->format('Y-m-d');

        // 勤怠データを作成（ユーザー1）
        $attendance1 = Attendance::factory()->create([
            'employee_name' => '山田 太郎',
            'work_date' => $selectedDate,
            'start_time' => '09:00:00',
            'end_time' => '18:00:00',
            'break_minutes' => 60,
        ]);

        // 勤怠データを作成（ユーザー2）
        $attendance2 = Attendance::factory()->create([
            'employee_name' => '佐藤 花子',
            'work_date' => $selectedDate,
            'start_time' => '10:00:00',
            'end_time' => '19:00:00',
            'break_minutes' => 45,
        ]);

        // 管理者としてログインしてページへアクセス
        $response = $this->actingAs($admin, 'admin')->get(route('admin.attendances.index', ['date' => $selectedDate]));

        $response->assertStatus(200);

        // 日付タイトルの表示確認
        $response->assertSee(\Carbon\Carbon::parse($selectedDate)->format('Y年m月d日') . 'の勤怠');

        // 勤怠情報が表示されているか
        $response->assertSee('山田 太郎');
        $response->assertSee('09:00:00');
        $response->assertSee('18:00:00');
        $response->assertSee('60分');

        $response->assertSee('佐藤 花子');
        $response->assertSee('10:00:00');
        $response->assertSee('19:00:00');
        $response->assertSee('45分');

        // 「前日」「翌日」リンクが正しく生成されているか
        $response->assertSee(route('admin.attendances.index', ['date' => $previousDate]));
        $response->assertSee(route('admin.attendances.index', ['date' => $nextDate]));
    }
}
