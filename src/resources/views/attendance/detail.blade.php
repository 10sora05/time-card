@extends($layout)

@php
    $canEdit = auth('admin')->check() || (!$isPending && auth()->check());
@endphp

@section('css')
<link rel="stylesheet" href="{{ asset('css/detail.css') }}">
@endsection

@section('content')
<div class="content">
    <div class="container">
        <h2>勤怠詳細</h2>

        <form method="POST" action="@auth('admin')
            {{ route('admin.attendance.update', $attendance->id) }}
        @else
            {{ route('user.attendance.correction.store', $attendance->id) }}
        @endauth">
            @csrf

            @auth('admin')
                @method('PUT')
            @endauth
                <div class="detail-table">
                <table class="detail-table__inner">
                    <tr class="detail-table__row">
                        <th class="detail-table__th"><label>名前</label></th>
                        <td class="detail-table__td">{{ $attendance->employee_name }}</td>
                    </tr>

                    <tr class="detail-table__row">
                        <th class="detail-table__th"><label>日付</label></th>
                            <td class="detail-table__td">
                                <span>　{{ \Carbon\Carbon::parse($attendance->work_date)->format('Y年') }}</span><span class="detail-deta">　{{ \Carbon\Carbon::parse($attendance->work_date)->format('n月j日') }}</span>
                            </td>
                        </tr>

                    <tr class="detail-table__row">
                        <th class="detail-table__th"><label>出勤・退勤</label></th>
                        <td class="detail-table__td">
                            <input type="time" class="detail-input__left" name="start_time"
                                step="60"
                                value="{{ old('start_time', \Carbon\Carbon::parse($attendance->start_time)->format('H:i')) }}"
                                required
                                @unless($canEdit) disabled @endunless>
                            ～
                            <input type="time" class="detail-input__right" name="end_time"
                                step="60"
                                value="{{ old('end_time', \Carbon\Carbon::parse($attendance->end_time)->format('H:i')) }}"
                                required
                                @unless($canEdit) disabled @endunless>
                        </td>
                    </tr>

                    <tr class="detail-table__row">
                        <th class="detail-table__th"><label>休憩</label></th>
                        <td class="detail-table__td">
                            <input type="time" class="detail-input__left" name="break_start_time" 
                                value="{{ old('break_start_time', $attendance->break_start_time ? \Carbon\Carbon::parse($attendance->break_start_time)->format('H:i') : '') }}"
                                @unless($canEdit) disabled @endunless>
                            ～
                            <input type="time" class="detail-input__right" name="break_end_time" 
                                value="{{ old('break_end_time', $attendance->break_end_time ? \Carbon\Carbon::parse($attendance->break_end_time)->format('H:i') : '') }}"
                                @unless($canEdit) disabled @endunless>
                        </td>
                    </tr>

                    <tr class="detail-table__row">
                        <th class="detail-table__th"><label>休憩2</label></th>
                        <td class="detail-table__td">
                            <input type="time" class="detail-input__left" name="break2_start_time" 
                                value="{{ old('break2_start_time', $attendance->break2_start_time ? \Carbon\Carbon::parse($attendance->break2_start_time)->format('H:i') : '') }}"
                                @unless($canEdit) disabled @endunless>
                            ～
                            <input type="time" class="detail-input__right" name="break2_end_time" 
                                value="{{ old('break2_end_time', $attendance->break2_end_time ? \Carbon\Carbon::parse($attendance->break2_end_time)->format('H:i') : '') }}"
                                @unless($canEdit) disabled @endunless>
                        </td>
                    </tr>

                    <tr class="detail-table__row">
                        <th class="detail-table__th"><label>備考</label></th>
                        <td class="detail-table__td">
                            <textarea name="note" rows="3" class="note-textarea" @unless($canEdit) disabled @endunless>{{ old('note', $attendance->note) }}</textarea>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="detail-btn">
                @if ($canEdit)
                    <button type="submit" class="custom-button">修正</button>
                @else
                    <p class="comment">※ 承認待ちのため修正はできません。</p>
                @endif
            </div>
        </form>
    </div>
    @if ($errors->any())
    <div style="color: red;">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
</div>
@endsection