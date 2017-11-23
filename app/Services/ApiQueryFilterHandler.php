<?php

namespace App\Services;

use App\ApiQueryFilter;
use App\Http\Requests\ApiRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator as ValidatorFacade;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Validator;

class ApiQueryFilterHandler
{
    const REQUEST_FIELD_FILTERS = 'filters';

    /** @var array $filterableFields */
    protected $filterableFields;

    /** @var Request $request */
    protected $request;

    /**
     * ApiQueryFilterParser constructor.
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
    public function setFilterableFields(array $fields) {
        $this->filterableFields = $fields;

        return $this;
    }

    /**
     * Get result query filters
     *
     * @return \Illuminate\Support\Collection|null
     * @throws ValidationException
     */
    public function getCollectionOfFilters() {
        if(!$this->hasFilters()) {
            return null;
        }

        // validate filters
        $this->validate();

        return $this->collectFilters();
    }



    /**
     * @return bool
     */
    protected function hasFilters() {
        return $this->getRawArrayFilters() ? true : false;
    }

    /**
     * @return bool
     * @throws ValidationException
     */
    protected function validate() {

        $filters = $this->getRawArrayFilters();

        // validate query value
        /** @var Validator $jsonValidator */
        $jsonValidator = ValidatorFacade::make([
            'filters' => $filters
        ],[
            'filters' => 'required|array'
        ]);
        $jsonValidator->validate();

        $filterableValuesRule = '';
        if($this->filterableFields) {
            $filterableValuesRule = '|in:'.implode(',', $this->filterableFields);
        }

        // validate each individual filter
        foreach($filters as $filter) {

            // check that the filter is an array
            /** @var Validator $validator */
            $validator = ValidatorFacade::make([
                'filter' => $filter
            ], [
                'filter' => 'required|array',
            ]);
            $validator->validate();

            // check that the structure of the filter is valid
            /** @var Validator $validator */
            $validator = ValidatorFacade::make($filter, [
                ApiQueryFilter::FILTER_COLUMN => 'required|alpha_dash|max:255'.$filterableValuesRule,
                ApiQueryFilter::FILTER_OPERATOR => 'required|in:<,<=,=,>=,>,like',
                ApiQueryFilter::FILTER_VALUE => 'required',
            ]);

            $validator->validate();
        }
    }

    /**
     * @return Collection
     */
    protected function collectFilters() {
        $collection = new Collection();

        $filters = $this->getRawArrayFilters();

        foreach($filters as $filter) {
            $collection->push(
                new ApiQueryFilter($filter)
            );
        }

        return $collection;
    }

    /**
     * Get the raw filters
     *
     * @return array
     */
    public function getRawArrayFilters() {
        return $this->request->query(static::REQUEST_FIELD_FILTERS);
    }
}