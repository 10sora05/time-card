@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/list.css') }}">
@endsection

@section('content')
<div class="content">
    <div class="list__content">
        <h2 class="list__title">申請一覧</h2>
        @php
            $currentStatus = $status ?? 'pending'; // コントローラーから渡された変数
        @endphp

        <div class="btn-group mb-3" role="group">
            <a href="{{ route('admin.attendance_corrections.index', ['status' => 'pending']) }}"
            class="btn btn-sm {{ $status === 'pending' ? 'btn-primary' : 'btn-outline-primary' }}">
                承認待ち
            </a>
            <a href="{{ route('admin.attendance_corrections.index', ['status' => 'approved']) }}"
            class="btn btn-sm {{ $status === 'approved' ? 'btn-success' : 'btn-outline-success' }}">
                承認済み
            </a>
        </div>

        {{-- 申請リストが空かどうかで表示を切り替え --}}
        @if ($corrections->isEmpty())
            <p>該当する申請はありません。</p>
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
                <tr>
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
                        <a href="{{ route('user.attendance_corrections.show', $correction->id) }}" class="btn btn-sm btn-primary">詳細</a>
                    </td>
                </tr>
                @endforeach
            </table>

            {{-- ページネーション --}}
            {{ $corrections->links() }}
        </div>
    @endif
    </div>
</div>
@endsection
