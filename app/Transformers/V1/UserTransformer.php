<?php

namespace App\Transformers\V1;

use App\Transformers\Transformer;
use App\User;

class UserTransformer extends Transformer
{
    const ID = 'id';
    const NAME = 'name';
    const EMAIL = 'email';
    const ACTIVE = 'active';
    const ACTIVATION_TOKEN = 'activation_token';
    const REMEMBER_TOKEN = 'remember_token';

    /**
     * @inheritdoc
     * @param User $item
     */
    public function getTransformationMap($item) : array
    {
        return [
            self::ID    => $item->getAttribute(User::REFERENCE),
            self::NAME  => $item->getAttribute(User::NAME),
            self::EMAIL  => $item->getAttribute(User::EMAIL),
            self::ACTIVE  => $item->getAttribute(User::ACTIVE),
            self::ACTIVATION_TOKEN  => $item->getAttribute(User::ACTIVATION_TOKEN),
            self::REMEMBER_TOKEN  => $item->getAttribute(User::REMEMBER_TOKEN),
        ];
    }

    /**
     * @inheritdoc
     */
    protected function getBasicTransformationFields(): array
    {
        return [
            self::ID,
            self::NAME,
            self::EMAIL,
        ];
    }

    /**
     * @inheritdoc
     */
    protected function getFullTransformationFields(): array
    {
        return [
            self::ID,
            self::NAME,
            self::EMAIL,
            self::ACTIVE,
            self::ACTIVATION_TOKEN,
            self::REMEMBER_TOKEN,
        ];
    }
}