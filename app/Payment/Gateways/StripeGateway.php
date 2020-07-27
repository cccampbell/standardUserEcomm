<?php
namespace App\Payment\Gateways;

use App\Model\Customer;
use App\Model\CustomerDataSet;
use App\Payment\PaymentGateway;
use Stripe\Customer as StripeCustomer;
use App\Payment\Gateways\StripeGatewayCustomer;

class StripeGateway implements PaymentGateway {

    protected $customer;

    public function withCustomer(Customer $customer) {

        $this->customer = $customer;

        
        return $this;

    }

    public function createCustomer() {

        // would work if user was logged in so you could ad multiple cards
        // if logged in check accound_id against customers accound_id column, if there get that payment_gateway_id
        if($this->customer->getPaymentGatewayId()) {

            return $this->getCustomer();

        }
        
        $stripeCustomer = new StripeGatewayCustomer(
            $this,
            $this->createStripeCustomer()
        );

        $creator = new CustomerDataSet();

        // would usually update customer in db by inputting payment_gateway_id
            // if user was logged in you would update the db record to add payment_gateway_id
        // if logged in skip part as all info accounted for 
        $this->customer = $this->customer->update('payment_gateway_id', $stripeCustomer->id());

        // update customer so it has payment_customer_id
            // $user->id - will get the payment_customer_id

        return $stripeCustomer;

    }
    
    // returns customer
    public function user() {
        return $this->customer;
    }

    protected function getCustomer() {

        return new StripeGatewayCustomer(
            $this,
            StripeCustomer::retrieve($this->user->payment_gateway_id)
        );

    }

    protected function createStripeCustomer() {

        return StripeCustomer::create([
            'email' =>$this->customer->getEmail()
        ]);
 
    }
    
}