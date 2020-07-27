<?php

namespace App\Support\Exceptions;

use Exception;
 
class ProductQuantityNotAvailable extends Exception {

    protected $message = "The quantity of that product is currently unavailable, please check back at basket before proceeding";

}