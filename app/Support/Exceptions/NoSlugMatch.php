<?php

namespace App\Support\Exceptions;

use Exception;
 
class NoSlugMatch extends Exception {

    protected $message = "The given slug doesn't match any with in our shop";

}