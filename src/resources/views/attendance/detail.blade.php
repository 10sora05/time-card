@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/detail.css') }}">
@endsection

@section('content')
<div class="container mt-4">
    <h2>勤怠詳細</h2>

        <form method="POST" action="{{ route('attendance.update', $attendance->id) }}">
            @csrf
            @method('PUT')

        {{-- 名前 --}}
        <div class="form-group">
            <label>名前</label>
            <p>{{ $attendance->employee_name }}</p>
        </div>

        {{-- 日付 --}}
        <div class="form-group">
            <label>日付</label>
            <p>{{ \Carbon\Carbon::parse($attendance->work_date)->format('Y年n月j日') }}</p>
        </div>

        {{-- 出勤・退勤 --}}
        <div class="form-group">
            <label>出勤・退勤</label>
            <div style="display: flex; gap: 10px;">
                <input type="time" name="start_time" value="{{ old('start_time', $attendance->start_time) }}" required>
                <span>～</span>
                <input type="time" name="end_time" value="{{ old('end_time', $attendance->end_time) }}" required>
            </div>
        </div>

        {{-- 休憩 --}}
        <div class="form-group">
            <label>休憩</label>
            <div style="display: flex; gap: 10px;">
                <input type="time" name="break_start_time" value="{{ old('break_start_time', $attendance->break_start_time) }}">
                <span>～</span>
                <input type="time" name="break_end_time" value="{{ old('break_end_time', $attendance->break_end_time) }}">
            </div>
        </div>

        {{-- 休憩2 --}}
        <div class="form-group">
            <label>休憩2</label>
            <div style="display: flex; gap: 10px;">
                <input type="time" name="break2_start_time" value="{{ old('break2_start_time', $attendance->break2_start_time) }}">
                <span>～</span>
                <input type="time" name="break2_end_time" value="{{ old('break2_end_time', $attendance->break2_end_time) }}">
            </div>
        </div>

        {{-- 備考 --}}
        <div class="form-group">
            <label>備考</label>
            <textarea name="note" class="form-control" rows="3">{{ old('note', $attendance->note) }}</textarea>
        </div>

        {{-- 修正ボタン --}}
        <button type="submit" class="btn btn-primary">修正</button>
    </form>
</div>
@endsection
