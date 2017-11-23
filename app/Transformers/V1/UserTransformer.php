<?php

namespace App\Transformers;

use App\Company;

class UserTransformer extends Transformer
{
    /**
     * @inheritdoc
     * @param Company $item
     */
    public function getTransformationMap($item) : array
    {
        return [
            'id'    => $item->getAttribute(Company::FIELD_REFERENCE),
            'name'  => $item->getAttribute(Company::FIELD_NAME),
            'type'  => $item->getAttribute(Company::FIELD_TYPE),
        ];
    }

    /**
     * @inheritdoc
     */
    protected function getBasicTransformationFields(): array
    {
        return [
            'id',
            'name',
        ];
    }

    /**
     * @inheritdoc
     */
    protected function getFullTransformationFields(): array
    {
        return [
            'id',
            'name',
            'type',
        ];
    }
}