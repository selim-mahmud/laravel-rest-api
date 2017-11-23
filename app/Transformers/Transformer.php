<?php

namespace App\Transformers;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

abstract class Transformer
{
    const TRANSFORMATION_TYPE_BASIC = 'basic';
    const TRANSFORMATION_TYPE_FULL = 'full';
    const TRANSFORMATION_TYPE_CUSTOM = 'custom';

    /** @var mixed $transformationType */
    protected $transformationType;

    /** @var array $customTransformationFields */
    protected $customTransformationFields;

    /**
     * Check if the transformation type can be set
     *
     * @return bool
     */
    protected function canSetTransformation() : bool {
        return !$this->transformationType ? true : false;
    }

    /**
     * Set the transformation type to full
     *
     * @return Transformer
     */
    public function setFullTransformation() : self {

        if($this->canSetTransformation()) {
            $this->transformationType = self::TRANSFORMATION_TYPE_FULL;
        }

        return $this;
    }

    /**
     * Set the field names to be transformed
     *
     * @param array $fields
     * @return Transformer
     */
    protected function setCustomTransformationFields(array $fields) : self {
        $this->customTransformationFields = $fields;

        return $this;
    }

    /**
     * Set the transformation type to custom
     *
     * @param array $fields
     * @return Transformer
     */
    public function setCustomTransformation(array $fields) : self {

        if($this->canSetTransformation()) {
            $this->transformationType = self::TRANSFORMATION_TYPE_CUSTOM;

            return $this->setCustomTransformationFields($fields);
        }

        return $this;
    }

    /**
     * Get the type of transformation
     *
     * @return mixed
     */
    protected function getTransformationType() {
        return $this->transformationType;
    }

    /**
     * Transform items
     *
     * @param Collection $collection
     * @return Collection
     */
    public function transformCollection(Collection $collection)
    {
        $transformedCollection = collect();

        /** @var Model $item */
        foreach ($collection as $item) {
            $transformedCollection->push(
                $this->transformModel($item)
            );
        }

        return $transformedCollection;
    }

    /**
     * Transform a the collection held inside a paginator
     *
     * @param LengthAwarePaginator $paginatedCollection
     * @return LengthAwarePaginator
     */
    public function transformPaginatedCollection(LengthAwarePaginator $paginatedCollection) : LengthAwarePaginator {

        $paginatedCollection->setCollection(
            $this->transformCollection($paginatedCollection->getCollection())
        );

        return $paginatedCollection;
    }

    /**
     * Get the map of all the field name/value pairs
     *
     * @param $item
     * @return array
     */
    abstract public function getTransformationMap($item) : array;

    /**
     * Get the fields to be returned from the transformation
     *
     * @return array
     * @throws \Exception
     */
    protected function getTransformationFields() : array {
        switch ($this->getTransformationType()) {
            case self::TRANSFORMATION_TYPE_FULL:
                return $this->getFullTransformationFields();
                break;
            case self::TRANSFORMATION_TYPE_CUSTOM:
                return $this->customTransformationFields;
                break;
            case self::TRANSFORMATION_TYPE_BASIC:
            default:
                return $this->getBasicTransformationFields();
                break;
        }
    }

    /**
     * Get the basic set of fields to be transformed
     *
     * @return array
     */
    abstract protected function getBasicTransformationFields() : array;

    /**
     * Get the full set of fields to be transformed
     *
     * @return array
     */
    abstract protected function getFullTransformationFields() : array;

    /**
     * Transform a model
     *
     * @param $item
     * @return mixed
     */
    public function transformModel($item) : array {
        return array_intersect_key(
            $this->getTransformationMap($item),
            array_flip($this->getTransformationFields())
        );
    }
}