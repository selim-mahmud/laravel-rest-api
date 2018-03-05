<?php

namespace App\Http\Resources\V1;

use App\Http\Requests\ApiRequest;
use Illuminate\Http\Resources\Json\Resource;
use App\Answer as ModelAnswer;

class Answer extends Resource
{
    const ID = 'id';
    const QUESTION_ID = 'question_id';
    const USER_ID = 'user_id';
    const DESCRIPTION = 'description';
    const EXCEPTED = 'excepted';
    const UP_VOTE = 'up_vote';
    const DOWN_VOTE = 'down_vote';

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
                self::ID => $this->{ModelAnswer::REFERENCE},
                self::USER_ID => $this->{ModelAnswer::USER_ID},
                self::USER_ID => $this->{ModelAnswer::USER_ID},
                self::QUESTION_ID => $this->{ModelAnswer::QUESTION_ID},
                self::DESCRIPTION => $this->{ModelAnswer::DESCRIPTION},
                $this->mergeWhen($request->query(ApiRequest::QUERY_PARAM_FIELDS) === ApiRequest::QUERY_PARAM_FIELDS_ALL,
                    [
                        self::EXCEPTED => $this->{ModelAnswer::EXCEPTED},
                        self::UP_VOTE => $this->{ModelAnswer::UP_VOTE},
                        self::DOWN_VOTE => $this->{ModelAnswer::DOWN_VOTE},
                    ]),
                'user' => new User($this->whenLoaded(ModelAnswer::RELATION_USER)),
                'question' => new Question($this->whenLoaded(ModelAnswer::RELATION_QUESTION)),
            ]
        ];
    }
}
