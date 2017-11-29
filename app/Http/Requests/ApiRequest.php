<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApiRequest extends FormRequest
{
    const QUERY_PARAM_LIMIT = 'limit';
    const PAGINATED_RESULTS_LIMIT_MIN = 0;
    const PAGINATED_RESULTS_LIMIT_MAX = 1000;
    const PAGINATED_RESULTS_LIMIT_DEFAULT = 25;
    const PAGINATED_RESULTS_LIMIT_UNLIMITED = 0;

    const QUERY_PARAM_FIELDS = 'fields';
    const QUERY_PARAM_FIELDS_ALL = 'all';

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
                'between:' . self::PAGINATED_RESULTS_LIMIT_MIN . ',' . self::PAGINATED_RESULTS_LIMIT_MAX
            ],
            'page' => 'integer|min:1',
        ];
    }

    /**
     * Get the pagination limit
     *
     * @return int
     */
    public function getPaginationLimit(): int
    {
        return (int)$this->query(self::QUERY_PARAM_LIMIT, self::PAGINATED_RESULTS_LIMIT_DEFAULT);
    }

    /**
     * Checks if an unlimited result is requested
     *
     * @return bool
     */
    public function unlimitedPaginatedResultsRequested(): bool
    {
        return $this->getPaginationLimit() === self::PAGINATED_RESULTS_LIMIT_UNLIMITED ? true : false;
    }

    /**
     * @return bool
     */
    public function isAllFieldsRequested(): bool
    {

        if (
            $this->query(self::QUERY_PARAM_FIELDS) &&
            $this->query(self::QUERY_PARAM_FIELDS) === self::QUERY_PARAM_FIELDS_ALL
        ) {
            return true;
        }

        return false;
    }
}
