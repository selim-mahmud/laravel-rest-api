<?php

namespace App;

class ApiQueryRelation
{
    /**
     * Relation name
     */
    const RELATION_NAME = 'rn';

    /**
     * Operator
     */
    const RELATION_OPERATOR = 'ro';

    /**
     * Value
     */
    const RELATION_VALUE = 'rv';

    /** @var string $relationName */
    var $relationName;

    /** @var string $relationOperator */
    var $relationOperator;

    /** @var string $relationValue */
    var $relationValue;

    /**
     * ApiQueryRelation constructor.
     *
     * @param array $relationArray
     */
    function __construct(array $relationArray)
    {
        $this->relationName = $relationArray[static::RELATION_NAME] ?? null;
        $this->relationOperator = $relationArray[static::RELATION_OPERATOR] ?? null;
        $this->relationValue = $relationArray[static::RELATION_VALUE] ?? null;
    }
}