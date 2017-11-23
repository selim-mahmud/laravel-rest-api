<?php

namespace App\Repositories;

use App\ReferencedModel;

abstract class ReferencedModelRepository
{
    /**
     * @var ReferencedModel $model
     */
    private $model;

    /**
     * @return ReferencedModel
     */
    public function getModel() : ReferencedModel {
        return $this->model;
    }

    /**
     * @param ReferencedModel $model
     * @return self
     */
    protected function setModel(ReferencedModel $model) : self {
        $this->model = $model;

        return $this;
    }

    /**
     * @param string $name
     * @param $value
     * @return ReferencedModel
     */
    protected function findByField(string $name, $value) : ReferencedModel {
        return $this->getModel()->where($name, $value);
    }
}