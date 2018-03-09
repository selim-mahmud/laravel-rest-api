<?php

namespace App\Http\Resources\V1;

use App\Http\Requests\ApiRequest;
use App\User as ModelUser;
use Illuminate\Http\Resources\Json\Resource;

class User extends Resource
{
    const ID = 'id';
    const NAME = 'name';
    const EMAIL = 'email';
    const PASSWORD = 'password';
    const PASSWORD_CONFIRMATION = 'password_confirmation';
    const ACTIVE = 'active';
    const ACTIVATION_TOKEN = 'activation_token';
    const REMEMBER_TOKEN = 'remember_token';
    const ROLE = 'role';

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'status' => 'success',
            'successMessage' => 'Resource has been retrieved successfully.',
            'result' => [
                self::ID => $this->{ModelUser::REFERENCE},
                self::NAME => $this->{ModelUser::NAME},
                self::EMAIL => $this->{ModelUser::EMAIL},
                $this->mergeWhen($request->query(ApiRequest::QUERY_PARAM_FIELDS) === ApiRequest::QUERY_PARAM_FIELDS_ALL,
                    [
                        self::PASSWORD => $this->{ModelUser::PASSWORD},
                        self::ACTIVE => $this->{ModelUser::ACTIVE},
                        self::ACTIVATION_TOKEN => $this->{ModelUser::ACTIVATION_TOKEN},
                        self::REMEMBER_TOKEN => $this->{ModelUser::REMEMBER_TOKEN},
                        self::ROLE => $this->{ModelUser::REMEMBER_TOKEN},
                    ]),
                'questions' => Question::collection($this->whenLoaded(ModelUser::RELATION_QUESTIONS)),
                'answers' => Answer::collection($this->whenLoaded(ModelUser::RELATION_ANSWERS)),
            ]
        ];
    }
}
