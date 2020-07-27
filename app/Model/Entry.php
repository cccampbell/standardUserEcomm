<?php

namespace App\Model;

use App\Model\Database;
use App\Validation\Exceptions\InvalidSigninDetails;
use App\Validation\Exceptions\EmailUnavailableException;



class Entry {


  protected $_dbHandle, $_dbInstance;

  // constructor
  public function __construct() {
        $this->_dbInstance = Database::getInstance();
        $this->_dbHandle = $this->_dbInstance->getdbConnection();
  }

  public function logIn($email, $pass) {

    $sqlQuery = "SELECT idAccount from Account WHERE email = :email AND password = :password;" ;

    //echo $sqlQuery;

    $statement = $this->_dbHandle->prepare($sqlQuery);

    $statement->bindParam(':email', $email);
    $statement->bindParam(':password', $pass);

    $statement->execute();

    $dataSet = $statement->fetch();

    // echo ! $dataSet;
    if(! $dataSet) {
      return;
    } else {
      return $dataSet[0];
    }


  }

  public function signup($data) {
    
    $query = 'INSERT INTO accounts (firstname, lastname, email, `password`)
              VALUES 
              (:fname, :lname, :email, :pass)';

    $statement = $this->_dbHandle->prepare($query);

    $statement->execute(array(
      'fname' => $data['firstname'],
      'lname' => $data['lastname'],
      'email' => $data['email'],
      'pass' => password_hash($data['password'], PASSWORD_DEFAULT)
    ));

  }

  public function isEmailAvailable($email) {
    
    $query = 'SELECT * FROM accounts WHERE email = :email';

    $statement = $this->_dbHandle->prepare($query);

    $statement->execute(array('email' => $email));

    // if email match is returned
    if($statement->rowCount() !== 0) {
      return false;

    } else {
      return true;
    }

  }

  public function check($email, $pass) {

    // check data against db
    $query = 'SELECT * FROM accounts WHERE email = :email';

    $statement = $this->_dbHandle->prepare($query);

    $statement->execute(array(
      'email' => $email
    ));

    $check = $statement->fetch();

    // if !user returns false
    if(!$check) {
      
      throw new InvalidSigninDetails();
      return false;
      
    }

    // verify password retreived from db against input
    if( password_verify($pass, $check['password']) ) {

      //set into session
      $_SESSION['user'] = [
        'id' => $check['id'],
        'firstname' => $check['firstname'],
        'lastname' => $check['lastname'],
        'email' => $check['email']
      ];
      
      return true;

    } else {
      
      throw new InvalidSigninDetails();
      return false;
    }
    
    
    
  }

  public function signout() {

    unset($_SESSION['user']);

  }

}
