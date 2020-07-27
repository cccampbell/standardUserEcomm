<?php

namespace App\Support\Exceptions;

use Exception;
 
class NoTypeMatch extends Exception {

    protected $message = "The given type doesn't match any with in our shop";

}