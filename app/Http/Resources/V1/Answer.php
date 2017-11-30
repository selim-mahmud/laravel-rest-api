<?php

namespace App\Http\Resources\V1;

use App\Http\Requests\ApiRequest;
use Illuminate\Http\Resources\Json\Resource;
use App\Answer as modelAnswer;

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
            self::ID => $this->{modelAnswer::REFERENCE},
            self::USER_ID => $this->{modelAnswer::USER_ID},
            self::USER_ID => $this->{modelAnswer::USER_ID},
            self::QUESTION_ID => $this->{modelAnswer::QUESTION_ID},
            self::DESCRIPTION => $this->{modelAnswer::DESCRIPTION},
            $this->mergeWhen($request->query(ApiRequest::QUERY_PARAM_FIELDS) === ApiRequest::QUERY_PARAM_FIELDS_ALL, [
                self::EXCEPTED => $this->{modelAnswer::EXCEPTED},
                self::UP_VOTE => $this->{modelAnswer::UP_VOTE},
                self::DOWN_VOTE => $this->{modelAnswer::DOWN_VOTE},
            ]),
            'user' => Answer::collection($this->whenLoaded(modelAnswer::RELATION_USER)),
            'question' => Answer::collection($this->whenLoaded(modelAnswer::RELATION_QUESTION)),
        ];
    }
}
