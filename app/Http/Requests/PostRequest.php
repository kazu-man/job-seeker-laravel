<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Model\Job;

class PostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if($this->jobId){

            $job = Job::find($this->jobId);
            return $job && $this->user()->can('update', $job);
            
        }else{
            
            return $this->user()->can('create', Job::class);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'job_title' => 'required',
            'annual_salary' => 'required',
            'category' => 'required',
            'city' => 'required',
            'type' => 'required',
            'type' => [function($attribute, $value, $fail){
                if($value < 1 && $value > 3){
                    return $fail('エラーが発生しました。');
                }
            },
            'required',
            ]
        ];
    }
}
