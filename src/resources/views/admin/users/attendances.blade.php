@extends('layouts.admin_app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/list.css') }}">
@endsection

@section('content')
<div class="content">
    <div class="list__content">

        <h2 class="list__title">{{ $user->name }} さんの勤怠</h2>

        <div class="month-nav">
            <a href="{{ route('admin.users.attendances.index', ['user' => $user->id, 'month' => $prevMonth]) }}" class="page-turn">← 前月</a>
            
            <form method="GET" action="{{ route('admin.users.attendances.index', ['user' => $user->id]) }}" class="month-picker-form">
                <label for="month-picker" class="month-picker-label">
                    📅 {{ \Carbon\Carbon::parse($targetMonth)->format('Y/m') }}
                </label>
                <input 
                    type="month" 
                    id="month-picker" 
                    name="month" 
                    value="{{ $targetMonth }}" 
                    onchange="this.form.submit()" 
                    class="month-picker-input"
                >
            </form>

            <a href="{{ route('admin.users.attendances.index', ['user' => $user->id, 'month' => $nextMonth]) }}" class="page-turn">翌月 →</a>
        </div>
        
        <div class="list-table">
            <table class="list-table__inner">
                <tr class="list-table__row">
                    <th class="list-table__header">日付</th>
                    <th class="list-table__header">出勤</th>
                    <th class="list-table__header">退勤</th>
                    <th class="list-table__header">休憩</th>
                    <th class="list-table__header">合計</th>
                    <th class="list-table__header">詳細</th>
                </tr>
                @foreach ($days as $day)
                <tr class="list-table__row">

                    <td class="list-table__td">{{ $day['formatted'] }}（{{ $day['weekday'] }}）</td>

                    <td class="list-table__td">
                        @if (!empty($day['attendance']) && $day['attendance']->start_time)
                            {{ \Carbon\Carbon::createFromFormat('H:i:s', $day['attendance']->start_time)->format('H:i') }}
                        @else
                            　
                        @endif
                    </td>

                    <td class="list-table__td">
                        @if (!empty($day['attendance']) && $day['attendance']->end_time)
                            {{ \Carbon\Carbon::createFromFormat('H:i:s', $day['attendance']->end_time)->format('H:i') }}
                        @else
                            　
                        @endif
                    </td>

                    <td class="list-table__td">
                        @if (!empty($day['attendance']) && $day['attendance']->break_minutes !== null)
                            @php
                                $breakHours = floor($day['attendance']->break_minutes / 60);
                                $breakMinutes = str_pad($day['attendance']->break_minutes % 60, 2, '0', STR_PAD_LEFT);
                            @endphp
                            {{ $breakHours }}:{{ $breakMinutes }}
                        @else
                            　
                        @endif
                    </td>

                    <td class="list-table__td">
                        @if (!empty($day['attendance']) && $day['attendance']->total_minutes !== null)
                            @php
                                $totalHours = floor($day['attendance']->total_minutes / 60);
                                $totalMinutes = str_pad($day['attendance']->total_minutes % 60, 2, '0', STR_PAD_LEFT);
                            @endphp
                            {{ $totalHours }}:{{ $totalMinutes }}
                        @else
                            　
                        @endif
                    </td>

                    <td class="list-table__td">
                        @if (!empty($day['attendance']))
                            <a href="{{ route('admin.attendances.show', $day['attendance']->id) }}" class="detail-a">詳細</a>
                        @else
                            　
                        @endif
                    </td>
                </tr>
                @endforeach
            </table>
        </div>
        <div class="csv-export">
            <form method="GET" action="{{ route('admin.users.attendances.exportCsv', ['user' => $user->id]) }}">
                <input type="hidden" name="month" value="{{ $targetMonth }}">
                <button type="submit" class="csv-export-btn">CSV出力</button>
            </form>
        </div>
    </div>
</div>
@endsection
