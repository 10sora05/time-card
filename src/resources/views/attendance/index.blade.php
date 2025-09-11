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

        {{-- ステータス表示（画面上部） --}}
        @if (!$attendance)
            @if ($isOutsideWorkHours)
                <div class="note">勤務外</div>
            @endif

        @elseif ($attendance && !$endTime)
            @if ($breakStart && !$breakEnd)
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
                @if ($breakStart && !$breakEnd)
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