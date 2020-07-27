<?php

namespace App\Payment\Gateways;

use Exception;
use App\Model\PaymentMethod;
use App\Payment\PaymentGateway;
use App\Payment\GatewayCustomer;
use Stripe\Charge as StripeCharge;
use Stripe\Customer as StripeCustomer;


class StripeGatewayCustomer implements GatewayCustomer {

    protected   $gateway,
                $user;

    public function __construct(PaymentGateway $gateway, StripeCustomer $user) {

        $this->gateway = $gateway;
        $this->user = $user;

    }

    public function charge(PaymentMethod $card, $total) {

        try {

            StripeCharge::create([
                'currency' => 'gbp',
                'amount' => (int) $total * 100,
                'customer' => $this->user->id,
                'source' => $card->getProviderID()
            ]);

        } catch(Exception $e) {

            die(var_dump($e->getMessage()));

        }

        

    }


    public function addCard($token) {

        try {

            $card = $this->user->sources->create([
                'source' => $token
            ]);
            
        } catch(Exception $e) {

            die(var_dump($e->getMessage()));

        }

        // sets this card as default in stripe
        $this->user->default_source = $card->id;
        $this->user->save();

        $payment_obj = new PaymentMethod();

        $payment_card = $payment_obj->create(array(
            'customer_id' => $this->gateway->user()->getId(),
            'card_type' => $card->brand,
            'last_four' => $card->last4,
            'default_card' => TRUE,
            'provider_id' => $card->id
        ));
        // $this->gateway->user()->create(array(
        //     'customer_id' => $this->user->getId(),
        //     'card_type' => 'VISA',
        //     'last_four' => '4242',
        //     'default_card' => 'TRUE',
        //     'provider_id' => '12345abcd'
        // ));
        // create payment method in db to link with card and user

        return $payment_card;
        
        
    }

    public function id() {

        return $this->user->id;
    }

}