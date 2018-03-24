<?php

namespace App\Transformers\V1;

use App\User;
use App\Http\Resources\V1\User as ResourceUser;

class UserTransformer
{
    /**
     * @return array
     */
    public function getTransformationMap() : array
    {
        return [
            ResourceUser::ID => User::REFERENCE,
            ResourceUser::NAME => User::NAME,
            ResourceUser::EMAIL => User::EMAIL,
            ResourceUser::PASSWORD => User::PASSWORD,
            ResourceUser::ACTIVE => User::ACTIVE,
            ResourceUser::ACTIVATION_TOKEN => User::ACTIVATION_TOKEN,
            ResourceUser::REMEMBER_TOKEN => User::REMEMBER_TOKEN,
            ResourceUser::CREATED_AT => User::CREATED_AT,
            ResourceUser::UPDATED_AT => User::UPDATED_AT,
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