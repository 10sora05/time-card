@extends('layouts.admin_app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/list.css') }}">
@endsection

@section('content')
<div class="content">
    <div class="list__content">
        <h2 class="list__title">| 申請一覧</h2>

        @php
            $currentStatus = $status ?? 'pending';
        @endphp

        <div class="link-btn" role="group">
            <a href="{{ route('admin.attendance_corrections.index', ['status' => 'pending']) }}"
               class="btn btn-sm link-btn_a {{ $currentStatus === 'pending' ? 'active' : '' }}">
               承認待ち
            </a>

            <a href="{{ route('admin.attendance_corrections.index', ['status' => 'approved']) }}"
               class="btn btn-sm link-btn_a {{ $currentStatus === 'approved' ? 'active' : '' }}">
               承認済み
            </a>
        </div>

        @if ($corrections->isEmpty())
            <p>修正申請はありません。</p>
        @else
        <div class="list-table">
            <table class="list-table__inner">
                <tr class="list-table__row">
                        <th class="list-table__header">状態</th>
                        <th class="list-table__header">名前</th>
                        <th class="list-table__header">対象日時</th>
                        <th class="list-table__header">申請理由</th>
                        <th class="list-table__header">申請日時</th>
                        <th class="list-table__header">詳細</th>
                    </tr>
                    @foreach ($corrections as $correction)
                <tr class="list-table__row">
                    <td class="list-table__td">
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
                    <td class="list-table__td">{{ $correction->user->name ?? '不明' }}</td>
                    <td class="list-table__td">{{ optional($correction->attendance)->work_date ?? '不明' }}</td>
                    <td class="list-table__td">{{ $correction->note }}</td>
                    <td class="list-table__td">{{ $correction->created_at->format('Y-m-d H:i') }}</td>
                    <td class="list-table__td">
                        <a href="{{ url('/admin/requests/' . $correction->id) }}" class="detail-a">詳細</a>
                    </td>
                </tr>
                @endforeach
            </table>
        </div>
        @endif
    </div>
</div>
@endsection
