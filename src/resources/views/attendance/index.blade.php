@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endsection

@section('content')
<div class="content">
    <div class="container">

        @php
            use Carbon\Carbon;

            $startTime = isset($attendance->start_time)
                ? Carbon::createFromFormat('H:i:s', $attendance->start_time)
                : null;

            $endTime = isset($attendance->end_time)
                ? Carbon::createFromFormat('H:i:s', $attendance->end_time)
                : null;

            $breakStart = isset($attendance->break_start_time)
                ? Carbon::createFromFormat('H:i:s', $attendance->break_start_time)
                : null;

            $breakEnd = isset($attendance->break_end_time)
                ? Carbon::createFromFormat('H:i:s', $attendance->break_end_time)
                : null;
        @endphp

        @php
            $breakStart1 = isset($attendance->break_start_time) ? Carbon::createFromFormat('H:i:s', $attendance->break_start_time) : null;
            $breakEnd1 = isset($attendance->break_end_time) ? Carbon::createFromFormat('H:i:s', $attendance->break_end_time) : null;

            $breakStart2 = isset($attendance->break2_start_time) ? Carbon::createFromFormat('H:i:s', $attendance->break2_start_time) : null;
            $breakEnd2 = isset($attendance->break2_end_time) ? Carbon::createFromFormat('H:i:s', $attendance->break2_end_time) : null;

            // 休憩中判定（どちらかの休憩開始時間はあり、終了時間がまだなら休憩中）
            $isOnBreak = 
                ($breakStart1 && !$breakEnd1) || 
                ($breakStart2 && !$breakEnd2);
        @endphp

        {{-- ステータス表示（画面上部） --}}
        @if (!$attendance)
            @if ($isOutsideWorkHours)
                <div class="note">勤務外</div>
            @endif

        @elseif ($attendance && !$endTime)
            @if ($isOnBreak)
                <div class="note">休憩中</div>
            @else
                <div class="note">出勤中</div>
            @endif

        @elseif ($attendance && $endTime)
            <div class="note">退勤済</div>
        @endif

        {{-- 日付と曜日 --}}
        <p class="data">{{ $now->format('Y年m月d日') }}（{{ $weekday }}）</p>
        <p class="data_time">{{ $now->format('H:i') }}</p>

        {{-- 勤務時間と休憩時間の表示 --}}
        @if ($workDuration)
            <p class="data">勤務時間：{{ $workDuration->format('%H時間%i分') }}</p>
        @endif

        @if ($breakDuration)
            <p class="data">休憩時間：{{ $breakDuration->format('%H時間%i分') }}</p>
        @endif

        {{-- フラッシュメッセージ --}}
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @elseif(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif


        {{-- フッターに固定表示されるボタン or メッセージ --}}
        <div class="fixed-buttons">
            @if (!$attendance)
                {{-- 出勤前 --}}
                <form method="POST" action="{{ route('attendance.clockin') }}">
                    @csrf
                    <button type="submit" class="btn-custom-black">出勤</button>
                </form>

            @elseif ($attendance && !$endTime)
                {{-- 出勤中 --}}
            @if ($isOnBreak)
                {{-- 休憩中 --}}
                <form method="POST" action="{{ route('attendance.break_end') }}">
                    @csrf
                    <button type="submit" class="btn-custom-black">休憩戻</button>
                </form>
            @else
                <form method="POST" action="{{ route('attendance.clockout') }}" style="display:inline-block;">
                    @csrf
                    <button type="submit" class="btn-custom-black">退勤</button>
                </form>

                <form method="POST" action="{{ route('attendance.break_start') }}" style="display:inline-block;">
                    @csrf
                    <button type="submit" class="btn-custom-white">休憩入</button>
                </form>
            @endif

            @else
                {{-- 退勤済み --}}
                <div class="status-note">お疲れさまでした。</div>
            @endif
        </div>

    </div>
</div>
@endsection