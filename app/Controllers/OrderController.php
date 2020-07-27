<?php
namespace App\Controllers;

use Exception;
use App\Model\Order;

use Slim\Views\Twig;
use App\Model\Basket;
use App\Model\Orders;
use App\Model\Customer;
use Stripe\StripeClient;
use App\Model\PaymentMethod;
use App\Model\ProductDataSet;
use App\Validation\Validator;
use App\Controllers\Controller;
use App\Model\DeliveryServices;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class OrderController extends Controller {

    // basket
    // account if have one
    // address

    // public function __construct(Twig $view, Validator $validator , Basket $basket, Order $order) {

    //     $this->view = $view;
    //     $this->validator = $validator;
    //     $this->basket = $basket;
    //     $this->order = $order;
    // }
    
    // middleware for empty basket

    public function index(Request $request, Response $response) {

        // debug - if basket empty redirect to basket
        $logged_in = false;
        $delivery_services;

        // if user logged in get country id from user
        if($logged_in) {

            // hard code for user
            $user = 232;
            $delivery_services = DeliveryServices::getWithCountryID($user);

        }

        // if not logged in set as uk
        if(!$logged_in) {
            $delivery_services = DeliveryServices::getWithCountryID(231);
        }

        $countries = $this->container->get('app')->getAllCountries();

        return $this->container->get('view')->render($response, 'checkout.twig', [
            'countries' => $countries,
            'delivery_services' => $delivery_services
        ]);

    }

    public function confirmation(Request $request, Response $response) {

        // get passed through order number
        // call function to get all products from order

        $productDS = new ProductDataSet();

        $products = $productDS->collectProducts([3,4,5]);

        // die(var_dump($productDS->collectProducts(3,4,5)));

        return $this->container->get('view')->render($response, 'order_confirmation.twig', [
            'products' => $products
        ]);

    }
    
    public function postOrderDetails(Request $request, Response $response) {


        if( $this->container->get('basket')->isEmpty() ) {

            $_SESSION['flash']['failed'] = 'Unfortunely your basket is empty, you cannot proceed without products';

            return $response
                ->withHeader('Location', '/basket')
                ->withStatus(302);
            // return to basket page

        }

        // debug - caught errors return to checkout page with flash message
        // debug - clear basket once payment successful +
        // debug - add module on page to change payment if customer has more than one
        // debug - cancel payment (cancel btn to get rid of input in card form)

        // ajax post request - gets countries delivery service once chosen +
        //      freeze screen while collecting

        $inputs = $request->getParsedBody();

        // validate all inputs
        $validate = $this->container->get('validator')->validate($inputs, [
            // CLIENT INFO
            'firstname' => ['name', 'lengthMinShort'],
            'lastname' => ['name', 'lengthMinShort'],
            'email' => ['emailFormat'],
            // ADDRESS
            'firstline' => ['notEmpty'],
            'secondline' => [],
            'town' => ['lengthMinShort'],
            'city' => ['lengthMinShort'],
            'postcode' => ['postCodeFormat'],
        ]);

        // CANNOT VALIDATE
        if($validate->failed()) {

            return $response
                ->withHeader('Location', '/basket/checkout')
                ->withStatus(302);
        }

        // bad version of checking user logged in 
        // debug - correct this
        //   will have to go through whole setup of order to get it sorted
        $auth_account_id;

        if( $this->container->get('auth')->loggedIn() ) {

            $auth_account_id = $this->container->get('auth')->getUser()['id'];

        } else {

            $auth_account_id = 0;

        }

        // if logged in check accound_id against customers accound_id column, if there get that payment_gateway_id

        $temp_customer = $this->container->get('customerDS')->createCustomer(array(
            // billing info - start //
            'firstname' => $inputs['firstname'],
            'lastname' => $inputs['lastname'],
            'email' => $inputs['email'],
            // billing info - end //
            'account_id' => $auth_account_id,
            'country_id' => $inputs['country']
        ));

        // set up customer on stripe
        $customer = $this->container->get('paymentGateway')
                                    ->withCustomer($temp_customer)
                                    ->createCustomer();
                                                        
        // set up customer card on stripe
        $card = $customer->addCard($inputs['stripeToken']);
        // debug - return to payment page - use banner to show declined

        $order = $this->container->get('order');

        try {

            // set up order, get all info ready and return object state, for use
            $order = $order->setupOrder($this->container->get('basket'), $inputs, $auth_account_id, $card->getId());


        } catch( Exception $e) {

            $_SESSION['flash']['failed'] = $e->getMessage();

            return $response
                ->withHeader('Location', '/basket')
                ->withStatus(302);
            

        }

        // charge through on stripe
        $customer->charge($card, $order->getOrder()->getTotal());

        // once stripe is done process the order
        $order->processOrder();

        $order_success = $order->getOrder();

        // // debug - rework this
        // if($customer === false) {
        //     return $response
        //         ->withHeader('Location', '/basket/checkout')
        //         ->withStatus(302);
        // }

        // if payment successful create order on db
        // once completed return order_id and hash
        
        // put into session via a class
            // get order info -> pass through to payment stage (page)
                // post through order info
            
            // once payment successful -> remove items in basket
            // put through to order confirmation page - successful url
                // display all info on order
        
        die(json_encode([
            'id' => $order_success->getId(),
            'hash' => $order_success->getHash()
        ]));

        // return $response
        //         ->withHeader('Location', '/basket/payment/'. $order_success->getHash() .'_' . $order_success->getId())
        //         ->withStatus(302);

    }

    public function payment(Request $request, Response $response, $args) {

        $order = Orders::withID($args['id']);

        return $this->container->get('view')->render($response, 'payment.twig', [
            'order' => $order
        ]);

    }

    public function getPostageInfo(Request $request, Response $response, $args) {

        // debug - when get postage data add postage proce to containers basket

        $postage_id = $args['id'];
        $postage_price = $args['price'];

        die(json_encode(DeliveryServices::getPostageWithIDAndPrice($postage_id, $postage_price)));

    }

    public function getCountryDeliveryService(Request $request, Response $response, $args) {

        // call into db to get delivery services with matching country_id
        $country_id = $args['id'];

        $data = json_encode(DeliveryServices::getWithCountryID($country_id));

        die( $data );

    }


}