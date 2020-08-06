<?php

class AccountData {
  private $_id,$_firstName,$_lastName,$_email,$_password;

  public function __construct($dbRow) {
    $this->_id = $dbRow['idAccount'];
    $this->_firstName = $dbRow['firstname'];
    $this->_lastName = $dbRow['lastname'];
    $this->_email = $dbRow['email'];
    $this->_password = $dbRow['password'];
  }

  public function getAccountId() {
    return $this->_id;
  }
  public function getFirstname() {
    return $this->_firstName;
  }
  public function getLastname() {
    return $this->_lastName;
    //echo $this->_lastName;
  }
  public function getEmail() {
    return $this->_email;
  }
  public function getPass() {
    return $this->_passoword;
  }


}
