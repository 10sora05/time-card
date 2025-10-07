@extends('layouts.admin_app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin.attendances.css') }}">
@endsection

@section('content')
<div class="content">
    <div class="attendance__content">
        <h2 class="attendance__title">スタッフ一覧</h2>

        <div class="attendance-table">
        <table class="attendance-table__inner">
            <tr class="attendance-table__row">
                <th class="attendance-table__header">名前</th>
                <th class="attendance-table__header">メールアドレス</th>
                <th class="attendance-table__header">詳細</th>
            </tr>
                @foreach ($users as $user)
            <tr class="attendance-table__row">
                <td class="attendance-table__td">{{ $user->name }}</td>
                <td class="attendance-table__td">{{ $user->email }}</td>
                <td class="attendance-table__td">
                    <a href="{{ route('admin.users.attendances.index', $user->id) }}" class="detail">詳細</a>
                </td>
            </tr>
                @endforeach
        </table>
    </div>
</div>
@endsection