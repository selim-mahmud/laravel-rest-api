<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Resources\V1\User as ResourceUser;

class StoreUser extends FormRequest
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
            ResourceUser::NAME => 'required|string|max:255',
            ResourceUser::EMAIL => 'required|email|unique:users,email',
            ResourceUser::PASSWORD => 'required|confirmed|string|Max:20|Min:5',
        ];
    }
}
