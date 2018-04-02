<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\ApiRequest;
use App\Http\Requests\StoreUser;
use App\Http\Resources\V1\AnswerCollection;
use App\Http\Resources\V1\QuestionCollection;
use App\Http\Resources\V1\UserCollection;
use App\Http\Resources\V1\User as ResourceUser;
use App\Question;
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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator as ValidatorFacade;

class UserController extends ApiController
{
    /**@var User $user */
    protected $user;

    /**@var Question $question */
    protected $question;

    /**@var UserTransformer $userTransformer */
    protected $userTransformer;

    /**
     * UserController constructor.
     *
     * @param ApiRequest $request
     * @param User $user
     * @param Question $question
     * @param UserTransformer $userTransformer
     * @param ApiColumnFilterHandler $columnFilterHandler
     * @param ApiRelationAdditionHandler $relationAdditionHandler
     * @param ApiRelationFilterHandler $relationFilterHandler
     * @param ApiColumnSortingHandler $columnSortingHandler
     */
    public function __construct(
        ApiRequest $request,
        User $user,
        Question $question,
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
        $this->question        = $question;
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
     * @param string $id
     * @return ResourceUser
     */
    public function show(string $id): ResourceUser
    {
        $model = $this->user->findOrFail(decrypt($id));
        return new ResourceUser($this->getSingleResource($model));
    }

    /**
     * @param string $id
     * @return QuestionCollection
     */
    public function getQuestions(string $id): QuestionCollection
    {
        $queryBuilder = $this->question->newQuery()->where(User::ID, decrypt($id));
        return new QuestionCollection($this->getResourceCollection($queryBuilder));
    }

    /**
     * @param string $id
     * @return AnswerCollection
     */
    public function getAnswers(string $id): AnswerCollection
    {
        /** @var User $user */
        $user = $this->user->findOrFail(decrypt($id));
        $user = $this->getRelatedResourceCollection($user, User::RELATION_ANSWERS);
        return new AnswerCollection($user->answers);
    }

    /**
     * @param StoreUser $request
     * @return JsonResponse
     */
    public function store(StoreUser $request): JsonResponse
    {
        $request->merge([ResourceUser::PASSWORD => app('hash')->make($request->{ResourceUser::PASSWORD})]);
        $inputs = $this->userTransformer->transformInputs($request->all());

        try {

            $user = User::create($inputs);
            $user->assignRole('member');
            return $this->getSuccessResponse(StatusMessage::RESOURCE_CREATED, Response::HTTP_CREATED);

        } catch (Exception $exception) {
            return $this->getFailResponse(StatusMessage::COMMON_FAIL);
        }

    }

    /**
     * @param Request $request
     * @param string $id
     * @return JsonResponse
     */
    public function update(Request $request, string $id)
    {
        $jsonValidator = ValidatorFacade::make(
            $request->all(),
            $this->getValiadationRules()
        );
        $jsonValidator->validate();

        $user = $this->user->findOrFail(decrypt($id));

        if ($request->has(ResourceUser::PASSWORD)) {
            $request->merge([ResourceUser::PASSWORD => app('hash')->make($request->{ResourceUser::PASSWORD})]);
        }

        $data = $this->userTransformer->transformInputs($request->all());
        $user->fill($data);

        if (!$user->save()) {
            return $this->getFailResponse(StatusMessage::COMMON_FAIL);
        }

        return $this->getSuccessResponse(StatusMessage::RESOURCE_UPDATED);
    }


    /**
     * @param Request $request
     * @return ResourceUser|JsonResponse
     */
    public function login(Request $request)
    {
        $jsonValidator = ValidatorFacade::make(
            $request->all(),
            [
                ResourceUser::EMAIL => 'required|email',
                ResourceUser::PASSWORD => 'required|string|Max:20|Min:5',
            ]
        );
        $jsonValidator->validate();

        $user = $this->loginAttempt($request->get(ResourceUser::EMAIL), $request->get(ResourceUser::PASSWORD));

        if ($user) {
            return new ResourceUser($user);
        }

        return $this->getSuccessResponse(StatusMessage::LOGIN_FAIL);
    }

    /**
     * @param $email
     * @param $password
     * @return bool|\Illuminate\Database\Eloquent\Model|null|static
     */
    protected function loginAttempt($email, $password)
    {
        $user = $this->user->where(User::EMAIL, $email)->first();

        if ($user) {
            if (password_verify($password, $user->password)) {
                return $user;
            }
        }

        return false;
    }

    /**
     * @param string $id
     * @return JsonResponse
     */
    public function destroy(string $id): JsonResponse
    {
        $user = $this->user->findOrFail(decrypt($id));

        if (!$user->delete()) {
            return $this->getFailResponse(StatusMessage::COMMON_FAIL);
        }

        return $this->getSuccessResponse(StatusMessage::RESOURCE_DELETED);
    }

    /**
     * @return array
     */
    protected function getValiadationRules(): array
    {
        return [
            ResourceUser::NAME => 'string|max:255',
            ResourceUser::EMAIL => 'email|unique:users,email',
            ResourceUser::ACTIVE => 'string|max:255',
            ResourceUser::PASSWORD => 'string|confirmed|Max:20|Min:5',
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
            User::RELATION_ANSWERS,
            Question::RELATION_USER,
            Question::RELATION_ANSWERS,
            Question::RELATION_TAGS
        ];
    }
}
