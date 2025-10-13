<?php

namespace Tests\Feature;

use Tests\TestCase;
use Carbon\Carbon;

class DateTimeTest extends TestCase
{
    public function test_current_datetime_format()
    {
        Carbon::setTestNow('2025-10-13 17:25:30');

        $currentDatetime = now()->format('Y-m-d H:i:s');

        $this->assertEquals('2025-10-13 17:25:30', $currentDatetime);

        Carbon::setTestNow(); // モック解除
    }
}
