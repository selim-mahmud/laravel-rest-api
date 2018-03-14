<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;

/**
 * @property int $id
 * @property string $reference
 * @property Collection $links
 */
abstract class ReferencedModel extends Model
{
    const FIELD_REFERENCE = 'reference';
    const REFERENCE_MUTATOR = self::REFERENCE_MUTATOR_UPPER;

    const REFERENCE_MUTATOR_NONE = 'none';
    const REFERENCE_MUTATOR_UPPER = 'upper';
    const REFERENCE_MUTATOR_LOWER = 'lower';

    protected $referenceLength = 16;

    function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

    /**
     * @return string
     */
    public function getReferenceName(): string
    {
        return self::FIELD_REFERENCE;
    }

    /**
     * @return int
     */
    public function getReferenceLength() {
        return $this->referenceLength;
    }

    /**
     * Set the reference
     *
     * @param $value
     */
    public function setReferenceAttribute(string $value)
    {
        $this->attributes[self::FIELD_REFERENCE] = strtoupper($value);
    }

    /**
     * @param string $reference
     * @return mixed
     */
    public function findByReference(string $reference)
    {
        return $this->where($this->getReferenceName(), $reference)->first();
    }

    /**
     * Find a model by reference or fail
     *
     * @param string $reference
     * @throws ModelNotFoundException
     * @return ReferencedModel
     */
    public function findByReferenceOrFail(string $reference): ReferencedModel
    {
        $model = $this->findByReference($reference);

        if (!$model) {
            throw new ModelNotFoundException('Could not find requested data', Response::HTTP_NOT_FOUND);
        }

        return $model;
    }

    /**
     * Get a model reference by id or fail
     *
     * @param int $id
     * @throws ModelNotFoundException if company could not be found
     * @return string
     */
    public function getReferenceByIdOrFail(int $id): string
    {

        return $this->findOrFail($id)->getAttribute($this->getReferenceName());
    }

    /**
     * @return string
     */
    public function generateUniqueReference()
    {
        $reference = str_random();
        while ($this->findByReference($reference) !== null) {
            $reference = str_random();
        }

        do {
            $reference = $this->mutateReference(str_random($this->getReferenceLength()));
        } while($this->newQuery()->where($this->getReferenceName(), $reference)->first());

        return $reference;
    }

    /**
     * @param string $reference
     * @return string
     */
    protected function mutateReference($reference) {
        switch (static::REFERENCE_MUTATOR) {
            case self::REFERENCE_MUTATOR_UPPER:
                return strtoupper($reference);
            case self::REFERENCE_MUTATOR_LOWER:
                return strtolower($reference);
            case self::REFERENCE_MUTATOR_NONE:
            default:
                return $reference;
        }
    }

}