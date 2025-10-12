<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAttendanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i'],
            'break_start_time' => ['nullable', 'date_format:H:i'],
            'break_end_time' => ['nullable', 'date_format:H:i'],
            'break2_start_time' => ['nullable', 'date_format:H:i'],
            'break2_end_time' => ['nullable', 'date_format:H:i'],
            'note' => ['required', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'start_time.required' => '出勤時間は必須です。',
            'start_time.date_format' => '出勤時間の形式が正しくありません。',
            'end_time.required' => '退勤時間は必須です。',
            'end_time.date_format' => '退勤時間の形式が正しくありません。',
            'break_start_time.date_format' => '休憩開始時間の形式が正しくありません。',
            'break_end_time.date_format' => '休憩終了時間の形式が正しくありません。',
            'break2_start_time.date_format' => '休憩2開始時間の形式が正しくありません。',
            'break2_end_time.date_format' => '休憩2終了時間の形式が正しくありません。',
            'note.required' => '備考を記入してください。',
            'note.max' => '備考は500文字以内で入力してください。',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $start = $this->input('start_time');
            $end = $this->input('end_time');
            $breakStart = $this->input('break_start_time');
            $breakEnd = $this->input('break_end_time');
            $break2Start = $this->input('break2_start_time');
            $break2End = $this->input('break2_end_time');

            // 出勤時間 > 退勤時間
            if ($start && $end && $start > $end) {
                $validator->errors()->add('start_time', '出勤時間が不適切な値です');
            }

            // 休憩開始 > 退勤時間
            if ($breakStart && $end && $breakStart > $end) {
                $validator->errors()->add('break_start_time', '休憩時間が不適切な値です');
            }

            // 休憩終了 > 退勤時間
            if ($breakEnd && $end && $breakEnd > $end) {
                $validator->errors()->add('break_end_time', '休憩時間もしくは退勤時間が不適切な値です');
            }

            // 任意: 休憩2のバリデーション
            if ($break2Start && $end && $break2Start > $end) {
                $validator->errors()->add('break2_start_time', '休憩2時間が不適切な値です');
            }

            if ($break2End && $end && $break2End > $end) {
                $validator->errors()->add('break2_end_time', '休憩2時間もしくは退勤時間が不適切な値です');
            }
        });
    }
}
