<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InterviewRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'applyId' => 'required',
            'interviewDate' => 'date|required|after:yesterday',
            'interviewTime' => 'date_format:H:i|required',
            'interviewDuration' => 'numeric|required|min:1'
        ];
    }


    public function messages()
{
    return [
        'applyId.required' => '情報の取得に失敗しました。',
        'interviewDate.required'  => '面接日を入力してください。',
        'interviewDate.date'  => '面接日の日付の形式はYYYY-MM-ddで入力してください。',
        'interviewDate.after'  => '面接日には今日以降の日付を設定してください。',
        'interviewTime.required'  => '面接時間を入力してください。',
        'interviewTime.date_format'  => '面接時間はH:mm形式で入力してください。',
        'interviewDuration.required'  => '面接予定時間を入力してください。',
        'interviewDuration.numeric'  => '面接予定時間には数値を入力してください。',
        'interviewDuration.min'  => '面接予定時間には1以上の数値を入力してください。',
    ];
}
}
