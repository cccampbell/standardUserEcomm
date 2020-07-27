<?php

namespace App\Model;

use PDO;
use App\Model\Model;


class Customer extends Model {

  protected $id,
          $firstname,
          $lastname,
          $email,
          $account_id,
          $payment_gateway_id;


  public function __construct(array $dbRow) {

    parent::__construct();

    if(isset($dbRow['id'])) {
      $this->id = $dbRow['id'];
    }

    $this->firstname = $dbRow['firstname'];
    $this->lastname = $dbRow['lastname'];
    $this->email = $dbRow['email'];
    $this->account_id = $dbRow['account_id'];
    $this->country_id = $dbRow['country_id'];

    if(isset($dbRow['payment_gateway_id'])) {
      $this->payment_gateway_id = $dbRow['payment_gateway_id'];
    }
    

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
  public function getAccountId() {
    return $this->account_id;
  }
  public function getCountryId() {
    return $this->country_id;
  }
  public function getPaymentGatewayId() {
    return $this->payment_gateway_id;
  }

  // updates customer and returns update customer
  public function update($column, $value) {

    // debug - update so takes in array of k => v for each updated column
    $query = 'UPDATE customers SET ' . $column . ' = :column WHERE id = :id';

    $statement = $this->_dbHandle->prepare($query);

    $statement->execute(array(
      'column' => $value,
      'id' => $this->getId()
    ));

    return $this->getCustomer();

  }

  protected function getCustomer() {

    $query = 'SELECT * FROM customers WHERE id = :id';

    $statement = $this->_dbHandle->prepare($query);

    $statement->execute(array(
      'id' => $this->getId()
    ));

    $customer = $statement->fetch(PDO::FETCH_ASSOC);

    return new Customer($customer);

  }





}
