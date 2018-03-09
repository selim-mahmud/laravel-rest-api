<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * Class ApiUser
 *
 * @package App
 * @property int $id
 * @property string $name
 * @property string $user_name
 * @property string $password
 * @property bool $active
 * @property string $activation_token
 */
class ApiUser extends Authenticatable
{
    const ID = 'id';
    const NAME = 'name';
    const USER_NAME = 'user_name';
    const PASSWORD = 'password';
    const ACTIVE = 'active';
    const ACTIVATION_TOKEN = 'activation_token';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        self::NAME,
        self::USER_NAME,
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password'
    ];
}
