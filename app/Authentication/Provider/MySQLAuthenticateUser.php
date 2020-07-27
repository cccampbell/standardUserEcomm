<?php

namespace App\Authentication\Provider;

use PDO;
use Exception;
use App\Model\Account;
use App\Model\Database;
use App\Validation\Exceptions\InvalidSigninDetails;
use App\Authentication\Provider\Contracts\AuthenticateUser;



class MySQLAuthenticateUser implements AuthenticateUser {
    
    protected $_dbHandle, $_dbInstance;

    public function __construct() {
        $this->_dbInstance = Database::getInstance();
        $this->_dbHandle = $this->_dbInstance->getdbConnection();
        
    }
    

    public function checkUser($email, $password) { 

        // check data against db
        $query = 'SELECT * FROM accounts WHERE email = :email';

        $statement = $this->_dbHandle->prepare($query);

        $statement->execute(array(
        'email' => $email
        ));

        $check = $statement->fetch(PDO::FETCH_ASSOC);

        // if !user returns false
        if(!$check) {
        
            throw new InvalidSigninDetails();
            return false;
        
        }

        // verify password retreived from db against input
        if( password_verify($password, $check['password']) ) {

            return new Account($check);
            

        } else {
        
            throw new InvalidSigninDetails();
            return false;
        }

    }

    public function byId($id) {
        
        $query = 'SELECT * FROM accounts WHERE id = :id';

        $statement = $this->_dbHandle->prepare($query);

        $statement->execute(array(
            'id' => (int) $id
        ));

        $check = $statement->fetch(PDO::FETCH_ASSOC);

        if(!$check) {
        
            throw new Exception('ID is not in db');
            return false;
        
        }

        return $check;

    }


}