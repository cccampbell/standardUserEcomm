<?php

namespace App\Validation\Exceptions;

use Exception;
 
class EmailUnavailableException extends Exception {

    protected $message = "Email already in use";

}