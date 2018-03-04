<?php

namespace App\Transformers\V1;

use App\Question;
use App\Http\Resources\V1\Question as ResourceQuestion;

class QuestionTransformer
{
    /**
     * @return array
     */
    public function getTransformationMap() : array
    {
        return [
            ResourceQuestion::ID => Question::REFERENCE,
            ResourceQuestion::USER_ID => Question::USER_ID,
            ResourceQuestion::TITLE => Question::TITLE,
            ResourceQuestion::SLUG => Question::SLUG,
            ResourceQuestion::DESCRIPTION => Question::DESCRIPTION,
            ResourceQuestion::FEATURED => Question::FEATURED,
            ResourceQuestion::STICKY => Question::STICKY,
            ResourceQuestion::SOLVED => Question::SOLVED,
            ResourceQuestion::UP_VOTE => Question::UP_VOTE,
            ResourceQuestion::DOWN_VOTE => Question::DOWN_VOTE,
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