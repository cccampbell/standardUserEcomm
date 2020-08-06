<?php

namespace App\Model;

use PDO;

$env = include(__DIR__ . '/../env.php');

class Database {
  protected static $_dbInstance = null; //static instance
  protected $_dbHandle;

  public static function getInstance() {
    
    $host = $env['DB_HOST'];
    $dbName = $env['DB_NAME'];
    $username = $env['DB_USERNAME'];
    $password = $env['DB_PASSWORD'];
    
  if(self::$_dbInstance === null) { //checked whether PDO exists
    //creates new single instance, if one already sends info
    self::$_dbInstance = new self($username, $password, $host, $dbName);
  }
    return self::$_dbInstance;

  }

  private function __construct($username, $password, $host, $dbName) {
    try {
      $this->_dbHandle = new PDO("mysql:host=$host;dbname=$dbName",  $username, $password); // creates the database handle with connection info
    } catch(PDOException $e) {//to catch any failure to connect to db
        echo $e->getMessage();
    }
  }

  public function getdbConnection() {
    return $this->_dbHandle; //returns so they can use it else where
  }

  public function __destruct() {
    $this->_dbHandle = null; //destorys it when not needed no more
  }

  

}
