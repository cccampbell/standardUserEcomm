<?php

namespace App\Authentication\Exceptions;

use Exception;

class UnknownClaim extends Exception {

    protected $message = 'Claim given could not be found';

}