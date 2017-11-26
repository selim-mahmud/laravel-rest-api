<?php

namespace App\Services;

use App\ApiQueryRelation;
use App\Http\Requests\ApiRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator as ValidatorFacade;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Validator;

class ApiRelationFilterHandler
{
    const QUERY_PARAM_RELATIONS = 'relations';

    /** @var array $relationNames */
    protected $relationNames;

    /** @var Request $request */
    protected $request;

    /**
     * ApiRelationFilterHandler constructor.
     *
     * @param ApiRequest $request
     */
    function __construct(ApiRequest $request)
    {
        $this->request = $request;
    }

    /**
     * @param array $names
     * @return $this
     */
    public function setRelationNames(array $names)
    {
        $this->relationNames = $names;

        return $this;
    }

    /**
     * Get result query relation
     *
     * @return \Illuminate\Support\Collection|null
     * @throws ValidationException
     */
    public function getCollectionOfRelations()
    {
        if (!$this->hasRelations()) {
            return null;
        }

        // validate filters
        $this->validate();

        return $this->collectRelations();
    }

    /**
     * @return bool
     */
    protected function hasRelations()
    {
        return $this->getRawArrayRelations() ? true : false;
    }

    /**
     * @return bool
     * @throws ValidationException
     */
    protected function validate()
    {

        $relations = $this->getRawArrayRelations();

        // validate query value
        /** @var Validator $jsonValidator */
        $jsonValidator = ValidatorFacade::make([
            'relations' => $relations
        ], [
            'relations' => 'required|array'
        ]);
        $jsonValidator->validate();

        $relationValuesRule = '';
        if ($this->relationNames) {
            $relationValuesRule = '|in:' . implode(',', $this->relationNames);
        }

        // validate each individual relation
        foreach ($relations as $relation) {

            // check that the relation is an array
            /** @var Validator $validator */
            $validator = ValidatorFacade::make([
                'relation' => $relation
            ], [
                'relation' => 'required|array',
            ]);
            $validator->validate();

            // check that the structure of the relation is valid
            /** @var Validator $validator */
            $validator = ValidatorFacade::make($relation, [
                ApiQueryRelation::RELATION_NAME => 'required|alpha|max:255' . $relationValuesRule,
                ApiQueryRelation::RELATION_OPERATOR => 'required|in:<,<=,=,>=,>',
                ApiQueryRelation::RELATION_VALUE => 'required|integer',
            ]);

            $validator->validate();
        }

    }

    /**
     * @return Collection
     */
    protected function collectRelations()
    {

        $collection = new Collection();
        $relations = $this->getRawArrayRelations();

        foreach ($relations as $relation) {
            $collection->push(
                new ApiQueryRelation($relation)
            );
        }

        return $collection;
    }

    /**
     * Get the raw relation
     *
     * @return array
     */
    public function getRawArrayRelations()
    {
        return $this->request->query(static::QUERY_PARAM_RELATIONS);
    }
}