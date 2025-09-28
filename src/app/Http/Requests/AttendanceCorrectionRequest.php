<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AttendanceCorrectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        // ログインユーザーだけ許可（adminでもuserでもOK）
        return auth()->check() || auth('admin')->check();
    }

    public function rules(): array
    {
        return [
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'break_start_time' => ['nullable', 'date_format:H:i'],
            'break_end_time' => ['nullable', 'date_format:H:i', 'after:break_start_time'],
            'break2_start_time' => ['nullable', 'date_format:H:i'],
            'break2_end_time' => ['nullable', 'date_format:H:i', 'after:break2_start_time'],
            'note' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'start_time.required' => '出勤時間は必須です。',
            'end_time.required' => '退勤時間は必須です。',
            'end_time.after' => '退勤時間は出勤時間より後である必要があります。',
            'break_end_time.after' => '休憩終了時間は休憩開始時間より後である必要があります。',
            'break2_end_time.after' => '休憩2終了時間は休憩2開始時間より後である必要があります。',
        ];
    }
}
