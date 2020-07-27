<?php

namespace App\Validation\Exceptions;

use Exception;
 
class UnknownValidationMethod extends Exception {

    protected $message = "Rule given is not a valid Validation function";

}