<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Resources\V1\Answer as ResourceAnswer;

class StoreAnswer extends FormRequest
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
            ResourceAnswer::QUESTION_ID => 'required|string',
            ResourceAnswer::USER_ID => 'required|string',
            ResourceAnswer::DESCRIPTION => 'required|string|max:65535',
        ];
    }
}
