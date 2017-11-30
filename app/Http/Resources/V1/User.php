<?php

namespace App\Http\Resources\V1;

use App\Http\Requests\ApiRequest;
use App\User as modelUser;
use Illuminate\Http\Resources\Json\Resource;

class User extends Resource
{
    const ID = 'id';
    const NAME = 'name';
    const EMAIL = 'email';
    const ACTIVE = 'active';
    const ACTIVATION_TOKEN = 'activation_token';
    const REMEMBER_TOKEN = 'remember_token';

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            self::ID    => $this->{modelUser::REFERENCE},
            self::NAME  => $this->{modelUser::NAME},
            self::EMAIL  => $this->{modelUser::EMAIL},
            $this->mergeWhen($request->query(ApiRequest::QUERY_PARAM_FIELDS) === ApiRequest::QUERY_PARAM_FIELDS_ALL, [
                self::ACTIVE  => $this->{modelUser::ACTIVE},
                self::ACTIVATION_TOKEN  => $this->{modelUser::ACTIVATION_TOKEN},
                self::REMEMBER_TOKEN  => $this->{modelUser::REMEMBER_TOKEN},
            ]),
            'questions' => Question::collection($this->whenLoaded(modelUser::RELATION_QUESTIONS)),
            'answers' => Answer::collection($this->whenLoaded(modelUser::RELATION_ANSWERS)),
        ];
    }
}
