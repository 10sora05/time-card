@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/detail.css') }}">
@endsection

@section('content')
<div class="content">
    <div class="container">
        <h2>勤怠詳細</h2>

        <form method="POST" action="{{ route('attendance.update', $attendance->id) }}">
            @csrf
            @method('PUT')

            <div class="detail-table">
                <table class="detail-table__inner">
                    <tr class="detail-table__row">
                        <th class="detail-table__th"><label>名前</label></th>
                        <td class="detail-table__td">{{ $attendance->employee_name }}</td>
                    </tr>

                    <tr class="detail-table__row">
                        <th class="detail-table__th"><label>日付</label></th>
                        <td class="detail-table__td">{{ \Carbon\Carbon::parse($attendance->work_date)->format('Y年n月j日') }}</td>
                    </tr>

                    <tr class="detail-table__row">
                        <th class="detail-table__th"><label>出勤・退勤</label></th>
                        <td class="detail-table__td">
                            <input type="time" name="start_time"
                                step="60"
                                value="{{ old('start_time', \Carbon\Carbon::parse($attendance->start_time)->format('H:i')) }}"
                                required>
                            ～
                            <input type="time" name="end_time"
                                step="60"
                                value="{{ old('end_time', \Carbon\Carbon::parse($attendance->end_time)->format('H:i')) }}"
                                required>
                        </td>
                    </tr>

                    <tr class="detail-table__row">
                        <th class="detail-table__th"><label>休憩</label></th>
                        <td class="detail-table__td">
                            <input type="time" name="break_start_time" value="{{ old('break_start_time', $attendance->break_start_time) }}">
                            ～
                            <input type="time" name="break_end_time" value="{{ old('break_end_time', $attendance->break_end_time) }}">
                        </td>
                    </tr>

                    <tr class="detail-table__row">
                        <th class="detail-table__th"><label>休憩2</label></th>
                        <td class="detail-table__td">
                            <input type="time" name="break2_start_time" value="{{ old('break2_start_time', $attendance->break2_start_time) }}">
                            ～
                            <input type="time" name="break2_end_time" value="{{ old('break2_end_time', $attendance->break2_end_time) }}">
                        </td>
                    </tr>

                    <tr class="detail-table__row">
                        <th class="detail-table__th"><label>備考</label></th>
                        <td class="detail-table__td">
                            <textarea name="note" rows="3" style="width: 100%;">{{ old('note', $attendance->note) }}</textarea>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="detail-btn">
                <button type="submit" class="custom-button">修正</button>
            </div>
        </form>
    </div>
</div>
@endsection
