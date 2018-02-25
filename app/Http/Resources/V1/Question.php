<?php

namespace App\Http\Resources\V1;

use App\Http\Requests\ApiRequest;
use Illuminate\Http\Resources\Json\Resource;
use App\Question as modelQuestion;

class Question extends Resource
{
    const ID = 'id';
    const USER_ID = 'user_id';
    const TITLE = 'title';
    const SLUG = 'slug';
    const DESCRIPTION = 'description';
    const FEATURED = 'featured';
    const STICKY = 'sticky';
    const SOLVED = 'solved';
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
            self::ID => $this->{modelQuestion::REFERENCE},
            self::USER_ID => $this->{modelQuestion::USER_ID},
            self::TITLE => $this->{modelQuestion::TITLE},
            self::SLUG => $this->{modelQuestion::SLUG},
            self::DESCRIPTION => $this->{modelQuestion::DESCRIPTION},
            $this->mergeWhen($request->query(ApiRequest::QUERY_PARAM_FIELDS) === ApiRequest::QUERY_PARAM_FIELDS_ALL, [
                self::FEATURED => $this->{modelQuestion::FEATURED},
                self::STICKY => $this->{modelQuestion::STICKY},
                self::SOLVED => $this->{modelQuestion::SOLVED},
                self::UP_VOTE => $this->{modelQuestion::UP_VOTE},
                self::DOWN_VOTE => $this->{modelQuestion::DOWN_VOTE},
            ]),
            'answers' => Answer::collection($this->whenLoaded(modelQuestion::RELATION_ANSWERS)),
            'user' => new User($this->whenLoaded(modelQuestion::RELATION_USER)),
            'tags' => Tag::collection($this->whenLoaded(modelQuestion::RELATION_TAGS)),
        ];
    }
}
