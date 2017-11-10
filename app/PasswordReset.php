<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class PasswordReset
 *
 * @package App
 * @property string $email
 * @property string $token
 */
class PasswordReset extends Model
{
    const EMAIL = 'email';
    const TOKEN = 'token';
}
