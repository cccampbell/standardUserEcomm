<?php

require_once("Database.php");

class Entry {


  protected $_dbHandle, $_dbInstance;

  // constructor
  public function __construct() {
        $this->_dbInstance = Database::getInstance();
        $this->_dbHandle = $this->_dbInstance->getdbConnection();
  }

  public function logIn($email, $pass) {

    $sqlQuery = "SELECT * from Account WHERE email = :email AND password = :password;" ;

    //echo $sqlQuery;

    $statement = $this->_dbHandle->prepare($sqlQuery);

    $statement->bindParam(':email', $email);
    $statement->bindParam(':password', $pass);

    $statement->execute();

    $dataSet = $statement->fetch(PDO::FETCH_ASSOC);

    // echo ! $dataSet;
    if(! $dataSet) {
      return;
    } else {
      return $dataSet;
    }
    
    // var_dump($dataSet);
    
    // echo $dataSet[0];


  }
}
