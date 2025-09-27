<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAttendanceRequest extends FormRequest
{
    public function authorize()
    {
        return true; // 必要に応じて権限チェックを実装
    }

    public function rules()
    {
        return [
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',

            'break_start_time' => 'nullable|date_format:H:i',
            'break_end_time' => 'nullable|date_format:H:i|after:break_start_time|required_with:break_start_time',

            'break2_start_time' => 'nullable|date_format:H:i',
            'break2_end_time' => 'nullable|date_format:H:i|after:break2_start_time|required_with:break2_start_time',

            'note' => 'nullable|string|max:1000',
        ];
    }
}