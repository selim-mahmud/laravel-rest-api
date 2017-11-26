<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ResourceWithParentApiController;
use App\Http\Requests\ApiRequest;
use App\Question;
use App\Repositories\UserRepository;
use App\Repositories\QuestionRepository;
use App\Services\ApiColumnFilterHandler;
use App\Services\ApiRelationFilterHandler;
use App\Transformers\V1\QuestionTransformer;

class QuestionController extends ResourceWithParentApiController
{
    /**
     * UserController constructor.
     *
     * @param ApiRequest $request
     * @param QuestionRepository $questionRepository
     * @param QuestionTransformer $questionTransformer
     * @param UserRepository $userRepository
     * @param ApiColumnFilterHandler $apiColumnFilterHandler
     * @param ApiRelationFilterHandler $apiRelationFilterHandler
     */
    public function __construct(
        ApiRequest $request,
        QuestionRepository $questionRepository,
        QuestionTransformer $questionTransformer,
        UserRepository $userRepository,
        ApiColumnFilterHandler $apiColumnFilterHandler,
        ApiRelationFilterHandler $apiRelationFilterHandler
    ) {
        parent::__construct(
            $request,
            $questionRepository,
            $questionTransformer,
            $userRepository,
            $apiColumnFilterHandler->setFilterableFields(
                $this->getFilterableFields()
            ),
            $apiRelationFilterHandler->setRelationNames(
                $this->getRelationNames()
            )
        );
    }

    /**
     * @return array
     */
    protected function getFilterableFields() {
        return [
            Question::ID,
            Question::REFERENCE,
            Question::USER_ID,
            Question::TITLE,
            Question::SLUG,
            Question::FEATURED,
            Question::STICKY,
            Question::SOLVED,
            Question::UP_VOTE,
            Question::DOWN_VOTE,
        ];
    }

    /**
     * @return array
     */
    protected function getRelationNames() {

        return [
            Question::RELATION_USER,
            Question::RELATION_TAGS
        ];
    }
}
