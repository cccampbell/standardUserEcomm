<?php
namespace App\Model;

use PDO;
use Exception;
use App\Model\Customer;
use App\Model\Database;
use App\Support\Exceptions\NotFoundInTable;
use App\Validation\Exceptions\EmailUnavailableException;


require_once('Customer.php');
require_once('Database.php');

class CustomerDataSet {

    protected $_dbHandle, $_dbInstance;

    //dataset constructor
    public function __construct() {

            $this->_dbInstance = Database::getInstance();
            $this->_dbHandle = $this->_dbInstance->getdbConnection();
    }

    public function getUser($id) {

        $query = 'SELECT * FROM customers WHERE id = :id';

        $statement = $this->_dbHandle->prepare($query);

        $statement->execute(array(
            'id' => $id
        ));

        return new Customer( $statement->fetch(PDO::FETCH_ASSOC) );


    }

    public function firstOrCreate($data) {

        $customer;

        $check = $this->checkIfCustomer($data);

        // debug - not in customer db

        if($check === 0) {

            $customer = $this->createCustomer($data);

            try{

                $customer = $this->getCustomer($data);

            } catch(NotFoundInTable $e) {

                $e->getMessage();
                return 0;

            }
            

        } else {

            $customer = $check;

        }

        return new Customer($customer);

    }

    public function createCustomer($data) {

        $query =   'INSERT into customers (firstname, lastname, email, account_id, country_id)
                    VALUES (:firstname, :lastname, :email, :account_id, :country_id)';

        $statement = $this->_dbHandle->prepare($query);

        if($statement->execute(array(
            'firstname' => $data['firstname'],
            'lastname' => $data['lastname'],
            'email' => $data['email'],
            'account_id' =>  $data['account_id'],
            'country_id' => $data['country_id']

        ))) {

            try {

                return new Customer($this->getCustomer($data));

            } catch(Exception $e) {

                var_dump($e);
                return false;

            }

        } else {

            return false;

        }

    }

    public function createTempCustomer(array $array) {
        return new Customer($array);
    }

    public function createFullCustomer(Array $data) {

        $query =   'INSERT into customers (firstname, lastname, email, account_id, payment_gateway_id)
                    VALUES (:firstname, :lastname, :email, :account_id, :payment_gateway_id)';

        $statement = $this->_dbHandle->prepare($query);

        if($statement->execute(array(
            'firstname' => $data['firstname'],
            'lastname' => $data['lastname'],
            'email' => $data['email'],
            'account_id' =>  $data['account_id'],
            'payment_gateway_id' =>  $data['payment_gateway_id']

        ))) {

            try {

                return new Customer($this->getCustomerViaGatewayId($data['payment_gateway_id']));

            } catch(Exception $e) {

                var_dump($e);
                return false;

            }

        } else {

            return false;

        }

    }

    private function createFirstStageCustomer($data) {
        // 
    }

    private function checkIfCustomer($data) {
    
        // check if customer in db
        $data['firstname'] = strtolower($data['firstname']);
        $data['lastname'] = strtolower($data['lastname']);

        $query = "SELECT id FROM customers WHERE firstname = :firstname AND lastname = :lastname AND email = :email AND account_id = :account_id";

        $statement = $this->_dbHandle->prepare($query);

        $statement->execute(array(
            'firstname' => $data['firstname'],
            'lastname' => $data['lastname'],
            'email' => $data['email'],
            'account_id' => $data['account_id'],
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

    private function getCustomerViaGatewayId($id) {

        $query = 'SELECT * FROM customers WHERE payment_gateway_id = :id';

        $statement = $this->_dbHandle->prepare($query);

        $statement->execute(array(
            'id' => $id,
        ));


        $check = $statement->fetch(PDO::FETCH_ASSOC);

        if(!$check) {

            return NULL;
            throw new NotFoundInTable();

        } else {
            
            return $check;

        }

    }

    private function getCustomer($data) {

        $query = "SELECT * from customers WHERE email = :email AND firstname = :firstname AND lastname = :lastname AND account_id = :account_id AND payment_gateway_id IS :gateway_id";

        $statement = $this->_dbHandle->prepare($query);

        $statement->execute(array(
            'email' => $data['email'],
            'firstname' => $data['firstname'],
            'lastname' => $data['lastname'],
            'account_id' => $data['account_id'],
            'gateway_id' => NULL,
        ));


        $check = $statement->fetch(PDO::FETCH_ASSOC);

        if(!$check) {

            return NULL;
            throw new NotFoundInTable();

        } else {

            return $check;

        }

    }



}
