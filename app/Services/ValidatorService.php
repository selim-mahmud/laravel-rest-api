<?php

namespace App\Services;

use App\Tag;
use Illuminate\Validation\Validator;

class ValidatorService
{

    /**
     * ValidatorService constructor.
     *
     */
    public function __construct()
    {

    }

    /**
     * @param string $attribute
     * @param array $value
     * @param array $parameters
     * @param Validator $validator
     *
     * @return bool
     */
    public function validateTags(string $attribute, array $value, array $parameters, Validator $validator): bool
    {
        array_map(function($el) { return decrypt($el); }, $value);

        $tagsCount = Tag::find($value)->count();

        if ($tagsCount != count($value)){
            return false;
        }

        return true;
    }

}