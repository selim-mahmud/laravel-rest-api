<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Resources\Json\Resource;
use App\Tag as modelTag;

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
            self::ID => $this->{modelTag::REFERENCE},
            self::NAME => $this->{modelTag::NAME},
            self::SLUG => $this->{modelTag::SLUG},
            self::ACTIVE => $this->{modelTag::ACTIVE},
        ];
    }
}
