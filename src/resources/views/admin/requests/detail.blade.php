@extends('layouts.admin_app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/detail.css') }}">
@endsection

@section('content')
<div class="content">
    <div class="container">
        <h2>| 勤怠詳細</h2>

        <div class="detail-table">
            <table class="detail-table__inner">
                <tr class="detail-table__row">
                    <th class="detail-table__th"><label>名前</label></th>
                    <td class="detail-table__td">{{ $correction->user->name }}</td>
                </tr>

                <tr class="detail-table__row">
                    <th class="detail-table__th"><label>日付</label></th>
                    <td class="detail-table__td">
                    @if ($correction->attendance)
                        <span>{{ \Carbon\Carbon::parse($correction->attendance->work_date)->format('Y年') }}</span>
                        <span class="detail-deta">{{ \Carbon\Carbon::parse($correction->attendance->work_date)->format('n月j日') }}</span>
                    @else
                        <span>日付情報なし</span>
                    @endif
                    </td>
                </tr>

                <tr class="detail-table__row">
                    <th class="detail-table__th"><label>出勤・退勤</label></th>
                    <td class="detail-table__td">
                        {{ \Carbon\Carbon::parse($correction->start_time)->format('H:i') }}
                        <span class="time-separator">～</span>
                        {{ \Carbon\Carbon::parse($correction->end_time)->format('H:i') }}
                    </td>
                </tr>

                <tr class="detail-table__row">
                    <th class="detail-table__th"><label>休憩</label></th>
                    <td class="detail-table__td">
                        @if ($correction->break_start_time && $correction->break_end_time)
                            {{ \Carbon\Carbon::parse($correction->break_start_time)->format('H:i') }}
                            <span class="time-separator">～</span>
                            {{ \Carbon\Carbon::parse($correction->break_end_time)->format('H:i') }}
                        @else
                            　
                        @endif
                    </td>
                </tr>

                <tr class="detail-table__row">
                    <th class="detail-table__th"><label>休憩2</label></th>
                    <td class="detail-table__td">
                        @if ($correction->break2_start_time && $correction->break2_end_time)
                            {{ \Carbon\Carbon::parse($correction->break2_start_time)->format('H:i') }}
                            <span class="time-separator">～</span>
                            {{ \Carbon\Carbon::parse($correction->break2_end_time)->format('H:i') }}
                        @else
                            　
                        @endif
                    </td>
                </tr>

                <tr class="detail-table__row">
                    <th class="detail-table__th"><label>備考</label></th>
                    <td class="detail-table__td">
                        {{ $correction->note }}
                    </td>
                </tr>
            </table>
        </div>

        <div class="detail-btn">
            @if ($correction->status === 'pending')
            <form method="POST" action="{{ route('admin.attendance_corrections.update', $correction->id) }}">
                @csrf
                @method('PUT')
                <input type="hidden" name="status" value="approved">
                <button type="submit" class="custom-button">承認</button>
            </form>
            @else
                <button type="button" class="custom-button_done" disabled>承認済み</button>
            @endif
        </div>
    </div>
</div>
@endsection
