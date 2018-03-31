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
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            self::ID => encrypt($this->{ModelTag::ID}),
            self::NAME => $this->{ModelTag::NAME},
            self::SLUG => $this->{ModelTag::SLUG},
            self::ACTIVE => (boolean) $this->{ModelTag::ACTIVE},
            self::CREATED_AT => $this->{ModelTag::CREATED_AT},
            self::UPDATED_AT => $this->{ModelTag::UPDATED_AT},
            'questions' => Question::collection($this->whenLoaded(ModelTag::RELATION_QUESTIONS)),
        ];
    }
}
