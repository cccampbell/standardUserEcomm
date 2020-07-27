<?php

namespace App\Support\Exceptions;

use Exception;
 
class QuantityExceededException extends Exception {

    protected $message = "you have added the maximum stock for this item";

}