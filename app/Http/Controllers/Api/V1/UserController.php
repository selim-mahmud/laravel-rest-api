<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\ApiRequest;
use App\Http\Requests\StoreUser;
use App\Http\Resources\V1\AnswerCollection;
use App\Http\Resources\V1\QuestionCollection;
use App\Http\Resources\V1\UserCollection;
use App\Http\Resources\V1\User as ResourceUser;
use App\Services\ApiColumnFilterHandler;
use App\Services\ApiColumnSortingHandler;
use App\Services\ApiRelationAdditionHandler;
use App\Services\ApiRelationFilterHandler;
use App\StatusMessage;
use App\Transformers\V1\UserTransformer;
use App\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator as ValidatorFacade;

class UserController extends ApiController
{
    /**@var User $user */
    protected $user;

    /**@var UserTransformer $userTransformer */
    protected $userTransformer;

    /**
     * UserController constructor.
     *
     * @param ApiRequest $request
     * @param User $user
     * @param UserTransformer $userTransformer
     * @param ApiColumnFilterHandler $columnFilterHandler
     * @param ApiRelationAdditionHandler $relationAdditionHandler
     * @param ApiRelationFilterHandler $relationFilterHandler
     * @param ApiColumnSortingHandler $columnSortingHandler
     */
    public function __construct(
        ApiRequest $request,
        User $user,
        UserTransformer $userTransformer,
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

        $this->user            = $user;
        $this->userTransformer = $userTransformer;
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
    public function show($reference): ResourceUser
    {
        $model = $this->user->findByReferenceOrFail($reference);
        return new ResourceUser($this->getSingleResource($model));
    }

    /**
     * @param $reference
     * @return QuestionCollection
     */
    public function getQuestions(string $reference): QuestionCollection
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
    public function getAnswers(string $reference): AnswerCollection
    {
        /** @var User $user */
        $user = $this->user->findByReferenceOrFail($reference);
        $user = $this->getRelatedResourceCollection($user, User::RELATION_ANSWERS);
        return new AnswerCollection($user->answers);
    }

    /**
     * @param StoreUser $request
     * @return JsonResponse
     */
    public function store(StoreUser $request): JsonResponse
    {
        $imputs                  = $this->userTransformer->transformInputs($request->all());
        $imputs[User::REFERENCE] = $this->user->generateUniqueReference();

        try {

            User::create($imputs);
            return $this->getSuccessResponse(StatusMessage::RESOURCE_CREATED, Response::HTTP_CREATED);

        } catch (Exception $exception) {

            return $this->getFailResponse(StatusMessage::COMMON_FAIL);
        }

    }

    /**
     * @param Request $request
     * @param $reference
     * @return JsonResponse
     */
    public function update(Request $request, $reference): JsonResponse
    {
        $jsonValidator = ValidatorFacade::make(
            $request->all(),
            $this->getValiadationRules()
        );
        $jsonValidator->validate();

        $user = $this->user->findByReferenceOrFail($reference);
        $data = $this->userTransformer->transformInputs($request->all());
        $user->fill($data);

        if (!$user->save()) {
            return $this->getFailResponse(StatusMessage::COMMON_FAIL);
        }

        return $this->getSuccessResponse(StatusMessage::RESOURCE_UPDATED);
    }

    /**
     * @return array
     */
    protected function getValiadationRules(): array
    {
        return [
            ResourceUser::NAME => 'string|max:255',
            ResourceUser::EMAIL => 'email',
            ResourceUser::ACTIVE => 'string|max:255',
            ResourceUser::ACTIVATION_TOKEN => 'string|max:255',
            ResourceUser::REMEMBER_TOKEN => 'string|max:255',
        ];
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
    protected function getSortableFields()
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
    protected function getRelationNames()
    {

        return [
            User::RELATION_QUESTIONS,
            User::RELATION_ANSWERS
        ];
    }
}
