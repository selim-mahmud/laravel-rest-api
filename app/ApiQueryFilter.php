<?php

namespace App;

class ApiQueryFilter
{
    /**
     * Column
     */
    const FILTER_COLUMN = 'c';

    /**
     * Operator
     */
    const FILTER_OPERATOR = 'o';

    /**
     * Value
     */
    const FILTER_VALUE = 'v';

    /** @var string $column */
    var $column;

    /** @var string $operator */
    var $operator;

    /** @var string $value */
    var $value;

    /**
     * ApiQueryFilter constructor.
     *
     * @param array $filterArray
     */
    function __construct(array $filterArray)
    {
        $this->column = $filterArray[static::FILTER_COLUMN] ?? null;
        $this->operator = $filterArray[static::FILTER_OPERATOR] ?? null;
        $this->value = $filterArray[static::FILTER_VALUE] ?? null;
    }
}