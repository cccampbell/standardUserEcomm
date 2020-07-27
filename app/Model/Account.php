<?php

namespace App\Model;

use App\Authentication\Contracts\JwtIdentifier;

class Account implements JwtIdentifier {

  protected $id,
          $firstname,
          $lastname,
          $email,
          $password,
          $dob;

  public function __construct(array $dbRow) {


    $this->id = $dbRow['id'];
    $this->firstname = $dbRow['firstname'];
    $this->lastname = $dbRow['lastname'];
    $this->email = $dbRow['email'];
    $this->password = $dbRow['password'];
    $this->dob = $dbRow['dob'];


  }

  public function JwtIdentifier() {

    return $this->id;

  }

  public function getId() {
    return $this->id;
  }
  public function getFirstname() {
    return $this->firstname;
  }
  public function getLastname() {
    return $this->lastname;
  }
  public function getEmail() {
    return $this->email;
  }
  public function getPassword() {
    return $this->password;
  }

  public function getDob() {
    return $this->dob;
  }



}
