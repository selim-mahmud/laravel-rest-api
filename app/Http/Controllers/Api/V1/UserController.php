<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\ApiRequest;
use App\Http\Resources\V1\AnswerCollection;
use App\Http\Resources\V1\QuestionCollection;
use App\Http\Resources\V1\UserCollection;
use App\Http\Resources\V1\User as ResourceUser;
use App\Services\ApiColumnFilterHandler;
use App\Services\ApiColumnSortingHandler;
use App\Services\ApiRelationAdditionHandler;
use App\Services\ApiRelationFilterHandler;
use App\User;

class UserController extends ApiController
{
    /**
     * @var User $user
     */
    protected $user;

    /**
     * UserController constructor.
     *
     * @param ApiRequest $request
     * @param User $user
     * @param ApiColumnFilterHandler $columnFilterHandler
     * @param ApiRelationAdditionHandler $relationAdditionHandler
     * @param ApiRelationFilterHandler $relationFilterHandler
     * @param ApiColumnSortingHandler $columnSortingHandler
     */
    public function __construct(
        ApiRequest $request,
        User $user,
        ApiColumnFilterHandler $columnFilterHandler,
        ApiRelationAdditionHandler $relationAdditionHandler,
        ApiRelationFilterHandler $relationFilterHandler,
        ApiColumnSortingHandler $columnSortingHandler
    )
    {
        parent::__construct(
            $request,
            $columnFilterHandler->setFilterableFields(
                $this->getFilterableFields()
            ),
            $relationAdditionHandler->setAddableRelations(
                $this->getRelationNames()
            ),
            $relationFilterHandler->setRelationNames(
                $this->getRelationNames()
            ),
            $columnSortingHandler->setSortableColumns(
                $this->getSortableFields()
            )
        );

        $this->user = $user;
    }

    /**
     * Display a listing of the resource.
     *
     * @return UserCollection
     */
    public function index(): UserCollection
    {
        $queryBuilder = $this->user->newQuery();
        return new UserCollection($this->getResourceCollection($queryBuilder));
    }

    /**
     * Display the specified resource.
     *
     * @param string $reference
     * @return ResourceUser
     */
    public function show($reference) : ResourceUser
    {
        $model = $this->user->findByReferenceOrFail($reference);
        return new ResourceUser($this->getSingleResource($model));
    }

    /**
     * @param $reference
     * @return QuestionCollection
     */
    public function getQuestions(string $reference) : QuestionCollection
    {
        /** @var User $user */
        $user = $this->user->findByReferenceOrFail($reference);
        $user = $this->getRelatedResourceCollection($user, User::RELATION_QUESTIONS);
        return new QuestionCollection($user->questions);
    }

    /**
     * @param $reference
     * @return AnswerCollection
     */
    public function getAnswers(string $reference) : AnswerCollection
    {
        /** @var User $user */
        $user = $this->user->findByReferenceOrFail($reference);
        $user = $this->getRelatedResourceCollection($user, User::RELATION_ANSWERS);
        return new AnswerCollection($user->answers);
    }

    /**
     * @return array
     */
    protected function getFilterableFields()
    {
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
    protected function getSortableFields() {
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
    protected function getRelationNames()
    {

        return [
            User::RELATION_QUESTIONS,
            User::RELATION_ANSWERS
        ];
    }
}
