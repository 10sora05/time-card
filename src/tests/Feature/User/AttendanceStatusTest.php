<?php

namespace Tests\Feature;

use Tests\TestCase;

class AttendanceStatusTest extends TestCase
{
    /**
     * 勤怠ステータスが正しく表示されるか確認する
     *
     * @return void
     */
    public function test_status_out_of_work()
    {
        $response = $this->get('/attendance/status?status=out_of_work');
        $response->assertStatus(200);
        $response->assertSeeText('勤務外');
    }

    public function test_status_working()
    {
        $response = $this->get('/attendance/status?status=working');
        $response->assertStatus(200);
        $response->assertSeeText('出勤中');
    }

    public function test_status_on_break()
    {
        $response = $this->get('/attendance/status?status=on_break');
        $response->assertStatus(200);
        $response->assertSeeText('休憩中');
    }

    public function test_status_finished()
    {
        $response = $this->get('/attendance/status?status=finished');
        $response->assertStatus(200);
        $response->assertSeeText('退勤済');
    }
}
