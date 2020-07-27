<?php

namespace App\Support\Exceptions;

use Exception;
 
class DeliveryServicesNotAvailable extends Exception {

    protected $message = "Unfortunately Delivery is not available yet";

}