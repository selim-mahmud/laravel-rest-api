<?php

namespace App\Repositories;

use App\User;

class UserRepository extends ReferencedModelRepository
{
    /**
     * UserRepository constructor.
     *
     * @param User $user
     */
    function __construct(User $user) {
        $this->setModel($user);
    }
}