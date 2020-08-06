<?php
session_start();
require_once("Model/Entry.php");
require_once("Model/AccountData.php");

// if(isset($_POST['log_in'])) {
if(isset($_POST['email']) && isset($_POST['password'])) {

  $name = $_POST['name'];
  $email = $_POST['email'];
  $pass = $_POST['password'];

  //echo $_POST['name']. " " . $_POST['email'];

  $entry = new Entry();

  $data = $entry->logIn($email, $pass);

  if($data === null) {
    echo 'null';
    // return 'error';
  } else {
    $user = new AccountData($data);

    $_SESSION['userID'] = $user->getAccountId();
    $_SESSION['firstname'] = $user->getFirstname();
    $_SESSION['lastname'] = $user->getLastname();
    $_SESSION['email'] = $user->getEmail();
    echo $user->getLastname();
    
  }

  

  // echo $user->getLastName();

  //echo $data[3];

  

}
//require_once("View/entry.phtml");
