<?php 
namespace App\Payment;

use App\Model\Customer;

interface PaymentGateway {

    // function returns customer to be used
    public function withCustomer(Customer $customer);

    // creates customer on payment api side
    public function createCustomer();

}