<?php

require_once('Model/AccountData.php');
require_once('Model/Database.php');

class AccountDataSet {
  protected $_dbHandle, $_dbInstance;

  //dataset constructor
  public function __construct() {
        $this->_dbInstance = Database::getInstance();
        $this->_dbHandle = $this->_dbInstance->getdbConnection();
  }

  public function fetchAllAccounts() {

    $sqlQuery = 'SELECT * FROM Account';

    //echo $sqlQuery; //helpful for debugging
    $statement = $this->_dbHandle->prepare($sqlQuery); //prepare PDO $statement
    $statement->execute();

    $dataSet = [];
    while($row = $statement->fetch()) {
      $dataSet[] = new AccountData($row);
    }
    return $dataSet;

  }

  public function fetchFirst() {

    $sqlQuery = 'SELECT * FROM Account WHERE idAccount = 1';

    echo $sqlQuery; //helpful for debugging
    $statement = $this->_dbHandle->prepare($sqlQuery); //prepare PDO $statement
    $statement->execute();

    $dataSet = [];
    while($row = $statement->fetch()) {
      $dataSet[] = new AccountData($row);
    }
    return $dataSet;

  }




}
