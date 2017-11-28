<?php

namespace App\Services;

use App\Http\Requests\ApiRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator as ValidatorFacade;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Validator;

class ApiRelationAdditionHandler
{
    const QUERY_PARAM_RELATIONS = 'add_relations';

    /** @var array $addableRelations */
    protected $addableRelations;

    /** @var Request $request */
    protected $request;

    /**
     * ApiRelationAdditionHandler constructor.
     *
     * @param ApiRequest $request
     */
    function __construct(ApiRequest $request)
    {
        $this->request = $request;
    }

    /**
     * @param array $fields
     * @return $this
     */
    public function setAddableRelations(array $fields) {
        $this->addableRelations = $fields;

        return $this;
    }

    /**
     * Get result query relation
     *
     * @return array|null
     * @throws ValidationException
     */
    public function getArrayOfRelations() {
        if(!$this->hasRelations()) {
            return null;
        }

        // validate filters
        $this->validate();

        return $this->getRelations();
    }



    /**
     * @return bool
     */
    protected function hasRelations() {
        return $this->getRelations() ? true : false;
    }

    /**
     * @return bool
     * @throws ValidationException
     */
    protected function validate() {

        $relations = $this->getRelations();

        //dd($relations);

        // validate query value
        /** @var Validator $jsonValidator */
        $jsonValidator = ValidatorFacade::make([
            'relations' => $relations
        ],[
            'relations' => 'required|array'
        ]);
        $jsonValidator->validate();

        $addableValuesRule = '';
        if($this->addableRelations) {
            $addableValuesRule = '|in:'.implode(',', $this->addableRelations);
        }

        // validate each individual relation
        foreach($relations as $relation) {

            // check that the name of the relation is valid
            /** @var Validator $validator */
            $validator = ValidatorFacade::make(
                ['relation' => $relation],
                ['relation' => 'required|alpha_dash|max:255'.$addableValuesRule,]);

            $validator->validate();
        }
    }

    /**
     * @return array
     */
    protected function getRelations() {
        return $this->request->query(static::QUERY_PARAM_RELATIONS);
    }
}