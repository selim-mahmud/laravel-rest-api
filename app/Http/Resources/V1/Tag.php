<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Resources\Json\Resource;
use App\Tag as ModelTag;

class Tag extends Resource
{
    const ID = 'id';
    const NAME = 'name';
    const SLUG = 'slug';
    const ACTIVE = 'active';

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
                self::ID => $this->{ModelTag::REFERENCE},
                self::NAME => $this->{ModelTag::NAME},
                self::SLUG => $this->{ModelTag::SLUG},
                self::ACTIVE => $this->{ModelTag::ACTIVE},
                'questions' => Question::collection($this->whenLoaded(ModelTag::RELATION_QUESTIONS)),
            ]
        ];
    }
}
