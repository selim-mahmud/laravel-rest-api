<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ResourceApiController;
use App\Http\Requests\ApiRequest;
use App\Repositories\UserRepository;
use App\Services\ApiQueryFilterHandler;
use App\Services\ApiRelationFilterHandler;
use App\Transformers\V1\UserTransformer;
use App\User;

class UserController extends ResourceApiController
{
    /**
     * UserController constructor.
     *
     * @param ApiRequest $request
     * @param UserRepository $userRepository
     * @param UserTransformer $userTransformer
     * @param ApiQueryFilterHandler $queryFilterHandler
     * @param ApiRelationFilterHandler $relationFilterHandler
     */
    public function __construct(
        ApiRequest $request,
        UserRepository $userRepository,
        UserTransformer $userTransformer,
        ApiQueryFilterHandler $queryFilterHandler,
        ApiRelationFilterHandler $relationFilterHandler
    ) {
        parent::__construct(
            $request,
            $userRepository,
            $userTransformer,
            $queryFilterHandler->setFilterableFields(
                $this->getFilterableFields()
            ),
            $relationFilterHandler->setRelationNames(
                $this->getRelationNames()
            )
        );
    }

    /**
     * @return array
     */
    protected function getFilterableFields() {
        return [
            User::ID,
            User::REFERENCE,
            User::NAME,
            User::EMAIL,
            User::ACTIVATION_TOKEN,
            User::REMEMBER_TOKEN,
        ];
    }

    /**
     * @return array
     */
    protected function getRelationNames() {

        return [
            User::RELATION_QUESTIONS,
            User::RELATION_ANSWERS
        ];
    }
}
