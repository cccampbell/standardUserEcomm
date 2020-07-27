<?php

namespace App\Validation\Exceptions;

use Exception;
 
class UnknownValidationRule extends Exception {

    protected $message = "Given rule not found within rules";

}