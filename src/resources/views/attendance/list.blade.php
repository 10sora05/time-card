@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/list.css') }}">
@endsection

@section('content')
<div class="content">
    <div class="list__content">
        <h2 class="attendance-list__title">勤怠一覧</h2>
        <div class="month-nav">
            <a href="{{ route('attendance.list', ['month' => $prevMonth]) }}" class="page-turn">← 前月</a>

            {{-- 📅 月選択（中央） --}}
            <form method="GET" action="{{ route('attendance.list') }}" style="position: relative;">
                {{-- ラベル表示（クリックでinputが開く） --}}
                <label for="month-picker" style="cursor: pointer;">
                    📅 {{ \Carbon\Carbon::parse($targetMonth)->format('Y/m') }}
                </label>

                {{-- 実際の月ピッカー（見えないけどクリック可能） --}}
                <input type="month" id="month-picker" name="month"
                    value="{{ $targetMonth }}"
                    onchange="this.form.submit()"
                    style="position: absolute; left: 0; top: 0; width: 100%; height: 100%; opacity: 0; cursor: pointer;">
            </form>

            <a href="{{ route('attendance.list', ['month' => $nextMonth]) }}" class="page-turn">翌月 →</a>
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
                <tr>
                    {{-- 日付＋曜日 --}}
                    <td>{{ $day['formatted'] }}（{{ $day['weekday'] }}）</td>

                    <td class="list-table__td">
                        @if (!empty($day['attendance']) && $day['attendance']->start_time)
                            {{ \Carbon\Carbon::createFromFormat('H:i:s', $day['attendance']->start_time)->format('H:i') }}
                        @else
                            -
                        @endif
                    </td>

                    <td class="list-table__td">
                        @if (!empty($day['attendance']) && $day['attendance']->end_time)
                            {{ \Carbon\Carbon::createFromFormat('H:i:s', $day['attendance']->end_time)->format('H:i') }}
                        @else
                            -
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
                            -
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
                            -
                        @endif
                    </td class="list-table__td">

                    <td class="list-table__td"><a href="#" class="detail">詳細</a></td>
                </tr>
                @endforeach
            </table>
        </div>
    </div>
</div>
@endsection
