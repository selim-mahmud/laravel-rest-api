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
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const ROLES = 'roles';
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            self::ID => encrypt($this->{ModelUser::ID}),
            self::NAME => $this->{ModelUser::NAME},
            self::EMAIL => $this->{ModelUser::EMAIL},
            $this->mergeWhen($request->query(ApiRequest::QUERY_PARAM_FIELDS) === ApiRequest::QUERY_PARAM_FIELDS_ALL,
                [
                    self::PASSWORD => $this->{ModelUser::PASSWORD},
                    self::ACTIVE => (boolean) $this->{ModelUser::ACTIVE},
                    self::ACTIVATION_TOKEN => $this->{ModelUser::ACTIVATION_TOKEN},
                    self::REMEMBER_TOKEN => $this->{ModelUser::REMEMBER_TOKEN},
                    self::CREATED_AT => $this->{ModelUser::CREATED_AT},
                    self::UPDATED_AT => $this->{ModelUser::UPDATED_AT},
                    self::ROLES => $this->getRoleNames(),
                ]),
            'questions' => Question::collection($this->whenLoaded(ModelUser::RELATION_QUESTIONS)),
            'answers' => Answer::collection($this->whenLoaded(ModelUser::RELATION_ANSWERS)),
        ];
    }
}
