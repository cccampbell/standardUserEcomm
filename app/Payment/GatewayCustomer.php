<?php

namespace App\Payment;

use App\Model\PaymentMethod;

interface GatewayCustomer {

    public function charge(PaymentMethod $card, $total);
    public function addCard($token);

}