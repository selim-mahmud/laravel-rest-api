<?php

namespace App\Services;

use App\Http\Requests\ApiRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator as ValidatorFacade;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Validator;

class ApiColumnSortingHandler
{
    const QUERY_PARAM_SORTINGS = 'sortings';

    /** @var array $sortableColumns */
    protected $sortableColumns;

    /** @var Request $request */
    protected $request;

    /**
     * ApiColumnSortingHandler constructor.
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
    public function setSortableColumns(array $fields) {
        $this->sortableColumns = $fields;
        return $this;
    }

    /**
     * Get result query relation
     *
     * @return array|null
     * @throws ValidationException
     */
    public function getArrayOfSortingColumns() {

        if(!$this->hasSortingColumns()) {
            return null;
        }

        // validate filters
        $this->validate();

        return $this->getSortingColumns();
    }



    /**
     * @return bool
     */
    protected function hasSortingColumns() {
        return $this->getSortingColumns() ? true : false;
    }

    /**
     * @return bool
     * @throws ValidationException
     */
    protected function validate() {

        $sortingColumns = $this->getSortingColumns();

        // validate query value
        /** @var Validator $jsonValidator */
        $jsonValidator = ValidatorFacade::make([
            'sortingColumns' => $sortingColumns
        ],[
            'sortingColumns' => 'required|array'
        ]);
        $jsonValidator->validate();

        $addableValuesRule = '';
        if($this->sortableColumns) {
            $addableValuesRule = '|in:'.implode(',', $this->sortableColumns);
        }

        // validate each individual relation
        foreach($sortingColumns as $sortingColumn=>$value) {

            // check that the name of the relation is valid
            /** @var Validator $validator */
            $validator = ValidatorFacade::make(
                ['sortingColumn' => $sortingColumn],
                ['sortingColumn' => 'required|alpha_dash|max:255'.$addableValuesRule,]);

            $validator->validate();
        }
    }

    /**
     * @return array
     */
    protected function getSortingColumns() {
        return $this->request->query(static::QUERY_PARAM_SORTINGS);
    }
}