<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Permission\Traits\HasRoles;

/**
 * Class User
 *
 * @package App
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property bool $active
 * @property string $activation_token
 * @property string $remember_token
 *
 * @property Collection questions
 * @property Collection answers
 */
class User extends Authenticatable
{
    use HasRoles;

    const ID = 'id';
    const NAME = 'name';
    const EMAIL = 'email';
    const PASSWORD = 'password';
    const ACTIVE = 'active';
    const ACTIVATION_TOKEN = 'activation_token';
    const REMEMBER_TOKEN = 'remember_token';

    const RELATION_QUESTIONS = 'questions';
    const RELATION_ANSWERS = 'answers';

    /**
     * The attributes that are not mass assignable.
     *
     * @var array
     */
    protected $guarded = [
        self::ID,
        self::PASSWORD,
    ];

    /**
     * The questions that this user has.
     *
     * @return HasMany
     */
    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    /**
     * The answers that this user has.
     *
     * @return HasMany
     */
    public function answers()
    {
        return $this->hasMany(Answer::class);
    }
}
