<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApiRequest extends FormRequest
{
    const PAGINATED_RESULTS_LIMIT_MIN = 0;
    const PAGINATED_RESULTS_LIMIT_MAX = 1000;
    const PAGINATED_RESULTS_LIMIT_DEFAULT = 25;
    const PAGINATED_RESULTS_LIMIT_UNLIMITED = 0;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'limit' => [
                'integer',
                'between:'.static::PAGINATED_RESULTS_LIMIT_MIN.','.static::PAGINATED_RESULTS_LIMIT_MAX
            ],
            'page' => 'integer|min:1',
        ];
    }

    /**
     * Get the pagination limit
     *
     * @return int
     */
    public function getPaginationLimit() : int {
        return (int) $this->query('limit', static::PAGINATED_RESULTS_LIMIT_DEFAULT);
    }

    /**
     * Checks if an unlimited result is requested
     *
     * @return bool
     */
    public function unlimitedPaginatedResultsRequested() : bool {
        return $this->getPaginationLimit() === static::PAGINATED_RESULTS_LIMIT_UNLIMITED ? true : false;
    }

    /**
     * @return bool
     */
    protected function hasFields() : bool {

        if(
            $this->query('fields')
            && is_array($this->query('fields'))
        ) {
            return true;
        }

        return false;
    }

    /**
     * Get the requested data fields
     *
     * @return array|null
     */
    public function getFields() {

        return $this->hasFields() ? $this->query('fields') : null;
    }
}
