<?php

class Database {
  protected static $_dbInstance = null; //static instance
  protected $_dbHandle;

  public static function getInstance() {
    $host = "127.0.0.1";
    $dbName = "user_Db";
    $username = "root";
    $password = "QZaJCe$4Nxd*";

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


  // $dbHandle = new
  // $sqlQuery = 'SELECT * FROM Account';
  // echo $sqlQuery; //helpful for debugging
  //
  // $statement = $dbhandle->prepare($sqlQuery); //prepare PDO $statement
  // $statement->execute();
  //
  // echo "<table border='1'>";
  // while($row = $statement->fetch()) {
  //   echo "<tr><td>".$row[0]."</td><td>".$row[1]."</td><td>".$row[2]."</td><td>".$row[3];
  // }
  // echo "</table>";
  //
  // $dbHandle = null;

}
