<?php

namespace App\Transformers\V1;

use App\Tag;
use App\Http\Resources\V1\Tag as ResourceTag;

class TagTransformer
{
    /**
     * @return array
     */
    public function getTransformationMap() : array
    {
        return [
            ResourceTag::ID => Tag::REFERENCE,
            ResourceTag::NAME => Tag::NAME,
            ResourceTag::SLUG => Tag::SLUG,
            ResourceTag::ACTIVE => Tag::ACTIVE,
            ResourceTag::CREATED_AT => Tag::CREATED_AT,
            ResourceTag::UPDATED_AT => Tag::UPDATED_AT,
        ];
    }

    /**
     * @param $inputs
     * @return array
     */
    public function transformInputs( array $inputs) : array
    {
        $arrayMap = $this->getTransformationMap();
        $transformInputs = array();
        foreach($inputs as $key => $value){

            if(isset($arrayMap[$key])){

                $transformInputs[$arrayMap[$key]] = $value;
            }
        }

        return $transformInputs;
    }
}