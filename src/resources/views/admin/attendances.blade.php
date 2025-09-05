@extends('layouts.admin_app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin.attendances.css') }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endsection

@section('content')
<div class="attendance__content">
  <div class="attendance__date">
    <span>
      <a href="{{ route('attendance.index', ['date' => $previousDate]) }}">← 前日</a>
    </span>

    <form method="GET" action="{{ route('attendance.index') }}" id="dateForm">
        <label for="fake-date">📅</label>

        <!-- 表示専用の span（ユーザーが見る部分） -->
        <span id="fake-date"
              style="cursor: pointer; padding: 6px 10px; display: inline-block;">
            {{ \Carbon\Carbon::parse($selectedDate)->format('Y/m/d') }}
        </span>

        <!-- 実際に送信される hidden input -->
        <input type="hidden" id="date" name="date" value="{{ $selectedDate }}">
    </form>

    <span>
      <a href="{{ route('attendance.index', ['date' => $nextDate]) }}">翌日 →</a>
    </span>
  </div>
  <div class="attendance-table">
    <table class="attendance-table__inner">
      <tr class="attendance-table__row">
        <th class="attendance-table__header">名前</th>
        <th class="attendance-table__header">出勤</th>
        <th class="attendance-table__header">退勤</th>
        <th class="attendance-table__header">休憩</th>
        <th class="attendance-table__header">合計</th>
        <th class="attendance-table__header">詳細</th>
      </tr>
      @foreach ($attendances as $attendance)
      <tr class="attendance-table__row">
          <td>{{ $attendance->employee_name }}</td>
          <td>{{ $attendance->start_time }}</td>
          <td>{{ $attendance->end_time }}</td>
          <td>{{ $attendance->break_minutes }}分</td>
          <td>{{ $attendance->formatted_total_time ?? '未計算' }}</td>
          <td><a href="#">詳細</a></td>
      </tr>
      @endforeach
    </table>
  </div>
</div>
@endsection
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/ja.js"></script>
<script>
  flatpickr("#fake-date", {
    dateFormat: "Y-m-d",   // hidden input用（送信用）
    locale: "ja",
    defaultDate: "{{ $selectedDate }}",
    clickOpens: true,
    allowInput: false,
    wrap: false,
    appendTo: document.body,
    onChange: function(selectedDates, dateStr, instance) {
      const selected = selectedDates[0];

      // 表示用フォーマット（例: 2025/09/01）
      const formattedDate = selected.toLocaleDateString('ja-JP');

      // 表示部分（span）を更新
      document.getElementById("fake-date").innerText = formattedDate;

      // 送信用 hidden input を更新
      document.getElementById("date").value = dateStr;

      // フォーム送信
      document.getElementById("dateForm").submit();
    }
  });
</script>
@endpush
