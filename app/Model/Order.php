<?php

namespace App\Model;

use PDO;
use Exception;
use App\Model\Orders;
use App\Model\Database;
use App\Model\DeliveryServices;
use App\Support\Exceptions\NotFoundInTable;
use App\Validation\Exceptions\InvalidSigninDetails;
use App\Support\Exceptions\ProductQuantityNotAvailable;
use App\Validation\Exceptions\EmailUnavailableException;



class Order {


    protected $_dbHandle, $_dbInstance;

    protected   $basket_total,
                $hash_id,
                $total,
                $address_id,
                $customer_id,
                $payment_method_id,
                $basket,
                $delivery_service_id;

    protected $order;


    // constructor
    public function __construct() {
        $this->_dbInstance = Database::getInstance();
        $this->_dbHandle = $this->_dbInstance->getdbConnection();
    }

    //   set up order to process later - returns object state
    public function setupOrder($basket, $data, $account_id, $card_id) {

        // check items still available 

        try {

            $basket->refresh();

        }
        catch(Exception $e) {

            throw $e;
            // flash message $e->getMessage();
            // go back to basket
            $_SESSION['flash']['failed'] = $e->getMessage();
            return;

        }

        $this->order = $this->setOrder($basket, $data, $account_id, $card_id);

        $this->basket = $basket;

        return $this;

    }

    private function createStockOrder($basket, $order_id) {

        // setting query up
        $query = "INSERT INTO order_products (order_id, stock_id, quantity) VALUES ";
        // array for order info 
        $values = [];

        foreach($basket->all() as $product) {

            $prod = $product->getId();

            $quantity = $product->quantity;

            $values[] = "(" .$order_id. ", ". $prod .", ". $quantity ."),";
        }

        // add values to query
        $query = $query . implode('', $values);

        // removes comma from end of string
        $query = substr($query, 0, -1);

        $statement = $this->_dbHandle->prepare($query);

        $statement->execute();

    }

    //   sets up order object
    private function setOrder($basket, $data, $account_id, $card_id) {

        // debug - put into method - tidy it up - cleaner

        // debug - check quantity of product from db -> take product from db

        // all input data text - transformed to lowercase
        $firstline = strtolower($data['firstline']);
        $secondline;
        // check if secondline empty
        if($data['secondline'] === '') {

            $secondline = '';

        } else {

            $secondline = strtolower($data['secondline']);
        }

        // debug - loop through data to strlower

        $town = strtolower($data['town']);
        $city = strtolower($data['city']);

        $firstname = strtolower($data['firstname']);
        $lastname = strtolower($data['lastname']);
        $email = strtolower($data['email']);
        $country_id = (int) $data['country'];

        $postcode = strtoupper($data['postcode']);

        // hash_id for order
        $hash_id = bin2hex(random_bytes(32));

        // var for customer id 
        $customer_id;

        $shipping = DeliveryServices::withID($data['postage']);

        // basket total
        $total = number_format($basket->getTotal() + $shipping->getPrice(), 2, '.', ',');

        // debug - used address in db

        // check address if there get id. if not return 0
        $address_id = $this->firstOrCreateAddress(array(
            'firstline' => $firstline,
            'secondline' => $secondline,
            'town' =>  $town,
            'city' => $city,
            'country_id' => $country_id,
            'postcode' => $postcode
        ));

        // debug - when logged in 

        $customer_id = $this->firstOrCreateCustomer(array(
                'firstname' => $firstname,
                'lastname' =>  $lastname,
                'email' => $email,
                'account_id' => $account_id,
                'country_id' => $country_id
        ));

        $this->fill(array(
            'basket_total' => $total,
            'hash_id' => $hash_id,
            'address_id' => $address_id,
            'customer_id' => $customer_id,
            'payment_method_id' => $card_id,
            'delivery_service_id' => $shipping->getId()
        ));

        // set up order on db
        return $this->createPendingOrder();

    }

    protected function updateStockQuantities($basket) {

        $query = 'UPDATE stock';

        // foreach over each item
        foreach($basket->all() as $product) {

            // id
            $query .= '';
            // quantity

        }

    }

    protected function fill(array $data) {

        $this->basket_total = $data['basket_total'];
        $this->hash_id = $data['hash_id'];
        $this->address_id = $data['address_id'];
        $this->customer_id = $data['customer_id'];
        $this->payment_method_id = $data['payment_method_id'];
        $this->delivery_service_id = $data['delivery_service_id'];

    }
    
    // returns newly created order instance
    protected function createPendingOrder() {

        $data = array(

            'hash_id' => $this->hash_id,
            'total' => $this->basket_total,
            'address_id' => $this->address_id,
            'paid' => 'PENDING',
            'customer_id' => $this->customer_id,
            'payment_method_id' => $this->payment_method_id,
            'delivery_service_id' => $this->delivery_service_id

        );

        $order = new Orders();

        // will return instance of newly created orders tuple on db
        return $order->create($data);

    }

    public function processOrder() {

        try {
            $this->basket->refresh();
        }
        catch(Exception $e) {

            throw new ProductQuantityNotAvailable();
            // flash message $e->getMessage();
            // go back to basket
            $_SESSION['flash']['failed'] = $e->getMessage();
            return;

        }

        $this->order = $this->order->update('paid', 'SUCCESS');

        // use order_id to create product_order to db
        // debug - try catch - if work
        //      stock might not be there for some reason
        try {

            $this->createStockOrder($this->basket, $this->order->getID());



        } catch(Exception $e) {

            // return to basket page
            // flash message - error processing order
            // wouldnt work if info wasnt right
            //      manipulated data
            //      old product not in stock 
            throw Exception;
        }
        

        // debug - maybe return order and stock_order
        //      counter to debug - just return order as basket has details of products
        //          counter to counter  - basket will be cleared once payment successfull
        
        // clear basket
        // $this->basket->clear();

        // return [
        //     'order' => $this->order,
        //     'stock_order' => $stock_order
        // ];
        
    }

    public function getOrder() {

        return $this->order;

    }

    protected function createOrder($payment_method_id) {

        // add order to db
        $query = "INSERT INTO orders (`hash_id`, total, address_id, paid, customer_id, payment_method_id)
                VALUES (:hash_id_id, :total, :address_id, TRUE, :customer_id, :payment_method_id)";

        $statement = $this->_dbHandle->prepare($query);

        $statement->execute(array(

            'hash_id_id' => $this->hash_id,
            'total' => $this->basket_total,
            'address_id' => $this->address_id,
            'customer_id' => $this->customer_id,
            'payment_method_id' => $payment_method_id

        ));

        // debug - return orders object
        return array(
            'hash_id' => $this->hash_id,
            'total' => $this->basket_total,
            'address_id' => $this->address_id,
            'customer_id' => $this->customer_id,
        );

    }

    public function getOrderTotal() {

        return $this->basket_total;

    }

    private function firstOrCreateAddress($data) {

        // RETURN ID OR 0 IF NOT FOUND
        $check = $this->checkifAddress($data['firstline'], $data['postcode']);

        // var_dump('check address: ' . $check . '<br>');


        if($check === 0) {

            $this->createAddress($data);

            try{

                return $this->getAddressId($data['firstline'], $data['postcode']);

            } catch(NotFoundInTable $e) {

                $e->getMessage();

            }
            

        } else {

            return (int) $check;

        }



    }

    private function firstOrCreateCustomer($data) {

        $check = $this->checkIfCustomer($data);

        // debug - not in customer db

        if($check === 0) {

            $this->createCustomer($data);

            try{

                return (int) $this->getCustomerId($data);

            } catch(NotFoundInTable $e) {

                $e->getMessage();
                return 0;

            }
            

        } else {

            return (int) $check;

        }

    }

    //   returns int - 0 if not there, other # if found
    private function checkIfAddress($firstline, $postcode) {

        $query = "SELECT * FROM addresses WHERE firstline = :firstline AND postcode = :postcode";

        $statement = $this->_dbHandle->prepare($query);

        $statement->execute(array(
            'firstline' => $firstline,
            'postcode' => $postcode
        ));

        $check = $statement->fetch();

        if(!$check) {

            return 0;

        } else {
            return (int) $check['id'];
        }

    }

    private function checkIfCustomer($data) {

        // check if customer in db
        $data['firstname'] = strtolower($data['firstname']);
        $data['lastname'] = strtolower($data['lastname']);

        $query = "SELECT id FROM customers WHERE firstname = :firstname AND lastname = :lastname AND email = :email AND account_id = :account_id AND country_id = :country_id";

        $statement = $this->_dbHandle->prepare($query);

        $statement->execute(array(
            'firstname' => $data['firstname'],
            'lastname' => $data['lastname'],
            'email' => $data['email'],
            'account_id' => $data['account_id'],
            'country_id' => $data['country_id'],
        ));


        $check = $statement->fetch(PDO::FETCH_ASSOC);

        if(!$check) {
            return 0;

        } else {
            return (int) $check['id'];
        }   


        // check customers against data 
        // if true return id #. false return 0
    }

    private function getAddressId($firstline, $postcode) {

        // var_dump($firstline . " " . $postcode);

        $query = 'SELECT `id` from addresses WHERE firstline = :firstline AND postcode = :postcode';

        $statement = $this->_dbHandle->prepare($query);

        $statement->execute(array(
                'firstline' => $firstline,
                'postcode' => $postcode
        ));


        $check = $statement->fetch();
        // var_dump('check ' . $check['id'] . '<br>');

        if(!$check) {

            return 0;
            throw new NotFoundInTable();

        } else {

            return $check['id'];

        }

    }

    private function getOrderId($data) {

        $query = "SELECT id from orders WHERE `hash_id` = :hash_id_id AND customer_id = :customer_id AND address_id = :address_id";

        $statement = $this->_dbHandle->prepare($query);

        $statement->execute(array(
                'hash_id_id' => $data['hash_id'],
                'customer_id' => (int) $data['customer_id'],
                'address_id' => (int) $data['address_id'],
        ));


        $check = $statement->fetch();

        if(!$check) {

            return 0;
            throw new NotFoundInTable();

        } else {

            return $check['id'];

        }

    }

    private function getCustomerId($data) {

        $query = "SELECT id from customers WHERE email = :email AND firstname = :firstname AND lastname = :lastname AND account_id = :account_id AND country_id = :country_id";

        $statement = $this->_dbHandle->prepare($query);

        $statement->execute(array(
                'email' => $data['email'],
                'firstname' => $data['firstname'],
                'lastname' => $data['lastname'],
                'account_id' => $data['id'],
                'country_id' => $data['country_id'],
                
        ));


        $check = $statement->fetch();

        if(!$check) {

            return 0;
            throw new NotFoundInTable();

        } else {

            return $check['id'];

        }

    }

    private function createAddress($data) {

        $query = "INSERT INTO addresses (firstline, secondline, town, city, country_id, postcode)
                VALUES (:firstline, :secondline, :town, :city, :country_id, :postcode)";

        $statement = $this->_dbHandle->prepare($query);

        if($data['secondline'] === '') {

            $statement->execute(array(
            'firstline' => strtolower($data['firstline']),
            'secondline' => $data['secondline'],
            'town' => strtolower($data['town']),
            'city' => strtolower($data['city']),
            'country_id' => strtolower($data['country_id']),
            'postcode' => strtolower($data['postcode'])
            ));

        } else {

            $statement->execute(array(
                'firstline' => strtolower($data['firstline']),
                'secondline' => strtolower($data['secondline']),
                'town' => strtolower($data['town']),
                'city' => strtolower($data['city']),
                'country_id' => strtolower($data['country_id']),
                'postcode' => strtolower($data['postcode'])
            ));
        }



    }

    private function createCustomer($data) {

        $query = "INSERT INTO customers (firstname, lastname, email, account_id, country_id)
                VALUES (:firstname, :lastname, :email, :account_id, :country_id)";


        $statement = $this->_dbHandle->prepare($query);

        $statement->execute(array(
            'firstname' => strtolower($data['firstname']),
            'lastname' => strtolower($data['lastname']),
            'email' => $data['email'],
            'account_id' => (int) $data['account_id'],
            'country_id' => (int) $data['country_id']
            ));
        }

    }