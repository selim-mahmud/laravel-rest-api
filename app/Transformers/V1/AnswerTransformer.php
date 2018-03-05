<?php

namespace App\Transformers\V1;

use App\Answer;
use App\Http\Resources\V1\Answer as ResourceAnswer;

class AnswerTransformer
{
    /**
     * @return array
     */
    public function getTransformationMap() : array
    {
        return [
            ResourceAnswer::ID => Answer::REFERENCE,
            ResourceAnswer::USER_ID => Answer::USER_ID,
            ResourceAnswer::USER_ID => Answer::USER_ID,
            ResourceAnswer::QUESTION_ID => Answer::QUESTION_ID,
            ResourceAnswer::DESCRIPTION => Answer::DESCRIPTION,
            ResourceAnswer::EXCEPTED => Answer::EXCEPTED,
            ResourceAnswer::UP_VOTE => Answer::UP_VOTE,
            ResourceAnswer::DOWN_VOTE => Answer::DOWN_VOTE,
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