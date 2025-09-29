@extends('layouts.admin_app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin.attendances.css') }}">
@endsection

@section('content')
<div class="content">
    <div class="attendance__content">
        <h2 class="attendance__title">修正申請一覧</h2>

        @if ($corrections->isEmpty())
            <p>修正申請はありません。</p>
        @else

                @php
            $currentStatus = $status ?? 'pending'; // コントローラーから渡された変数
        @endphp


        <div class="mb-3">
            <a href="{{ route('admin.attendance_corrections.index', ['status' => 'pending']) }}"
            class="btn btn-sm {{ $status === 'pending' ? 'btn-warning text-dark' : 'btn-outline-warning' }}">
                承認待ち
            </a>

            <a href="{{ route('admin.attendance_corrections.index', ['status' => 'approved']) }}"
            class="btn btn-sm {{ $status === 'approved' ? 'btn-success' : 'btn-outline-success' }}">
                承認済み
            </a>
        </div>

        <div class="attendance-table">
            <table class="attendance-table__inner">
                <tr class="attendance-table__row">
                    <th class="attendance-table__header">状態（承認待ち）</th>
                    <th class="attendance-table__header">名前</th>
                    <th class="attendance-table__header">対象日時</th>
                    <th class="attendance-table__header">申請理由</th>
                    <th class="attendance-table__header">>申請日時</th>
                    <th class="attendance-table__header">詳細</th>
                </tr>
                @foreach ($corrections as $correction)
                <tr class="attendance-table__row">
                    <td class="attendance-table__td">
                        @switch($correction->status)
                            @case('pending')
                                <span class="badge bg-warning text-dark">承認待ち</span>
                                @break
                            @case('approved')
                                <span class="badge bg-success">承認済み</span>
                                @break
                            @case('rejected')
                                <span class="badge bg-danger">却下</span>
                                @break
                            @default
                                <span class="badge bg-secondary">不明</span>
                        @endswitch
                    </td>
                    <td class="attendance-table__td">{{ $correction->user->name ?? '不明' }}</td>
                    <td class="attendance-table__td">{{ optional($correction->attendance)->work_date ?? '不明' }}</td>
                    <td class="attendance-table__td">{{ $correction->note }}</td>
                    <td class="attendance-table__td">{{ $correction->created_at->format('Y-m-d H:i') }}</td>
                    <td class="attendance-table__td">
                        <a href="{{ route('admin.attendance_corrections.show', $correction->id) }}" class="detail">詳細</a>
                    </td>
                </tr>
                    @endforeach
            </table>

            {{ $corrections->links() }} {{-- ページネーション --}}
        @endif
        </div>
    </div>
</div>
@endsection
