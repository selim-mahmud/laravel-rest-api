<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Resources\V1\Question as ResourceQuestion;

class StoreQuestion extends FormRequest
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
            ResourceQuestion::USER_ID => 'required|integer',
            ResourceQuestion::TITLE => 'required|string|max:255',
            ResourceQuestion::DESCRIPTION => 'required|string|max:65535',
            ResourceQuestion::FEATURED => 'required|boolean',
            ResourceQuestion::STICKY => 'required|boolean',
            ResourceQuestion::TAGS => 'required|tags',
        ];
    }
}
