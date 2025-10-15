@extends('layouts.admin_app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin.attendances.css') }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endsection

@section('content')
<div class="content">
  <div class="attendance__content">
    <h2 class="attendance__title">{{ \Carbon\Carbon::parse($selectedDate)->format('Y年m月d日') }}の勤怠</h2>
    <div class="attendance__date">
      <span>
        <a href="{{ route('admin.attendances.index', ['date' => $previousDate]) }}" class="page-turn">← 前日</a>
      </span>

      <form method="GET" action="{{ route('admin.attendances.index') }}" id="dateForm">
        <label for="fake-date">📅</label>

        <!-- 表示専用の span（ユーザーが見る部分） -->
        <span id="fake-date" class="attendance__date-title">
            {{ \Carbon\Carbon::parse($selectedDate)->format('Y/m/d') }}
        </span>

        <!-- 実際に送信される hidden input -->
        <input type="hidden" id="date" name="date" value="{{ $selectedDate }}">
      </form>

      <span>
        <a href="{{ route('admin.attendances.index', ['date' => $nextDate]) }}" class="page-turn">翌日 →</a>      </span>
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
          <td class="attendance-table__td">{{ $attendance->employee_name }}</td>
          <td class="attendance-table__td">{{ $attendance->start_time }}</td>
          <td class="attendance-table__td">{{ $attendance->end_time }}</td>
          <td class="attendance-table__td">{{ $attendance->break_minutes }}分</td>
          <td class="attendance-table__td">{{ $attendance->formatted_total_time ?? '未計算' }}</td>
          <td class="attendance-table__td">
            <a href="{{ route('admin.attendances.show', ['id' => $attendance->id]) }}" class="detail">詳細</a>
          </td>
        </tr>
        @endforeach
      </table>
    </div>
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
