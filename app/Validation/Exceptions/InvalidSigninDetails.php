<?php
namespace App\Validation\Exceptions;

use Exception;


class InvalidSigninDetails extends Exception {

    protected $message = 'Email and / or password are invalid';

}