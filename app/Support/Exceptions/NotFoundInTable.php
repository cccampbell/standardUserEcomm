<?php

namespace App\Support\Exceptions;

use Exception;
 
class NotFoundInTable extends Exception {

    protected $message = "Not been able to find in database";

}