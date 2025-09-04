<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateattendancesTable extends Migration
{
    public function up()
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();

            // 名前
            $table->string('employee_name');

            // 出勤・退勤
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();

            // 休憩時間（分単位を想定 → integer）
            $table->integer('break_minutes')->nullable();

            // 合計労働時間（分単位）
            $table->integer('total_minutes')->nullable();

            // 勤務日（検索用）
            $table->date('work_date');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('attendances');
    }
}