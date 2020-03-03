<?php
session_start();
require_once("Model/Entry.php");
require_once("Model/AccountData.php");


if(isset($_POST['log_out_user'])) {
    header("Location:/index.php");
    session_destroy();
}


// if(isset($_POST['log_in'])) {
// // if(isset($_POST['email'] && $_POST['password'])) {
//
//
//   $name = $_POST['name'];
//   $email = $_POST['email'];
//   $pass = $_POST['password'];
//
//   //echo $_POST['name']. " " . $_POST['email'];
//
//   $entry = new Entry();
//
//   $data = $entry->logIn($email, $pass);
//
//   //echo $data[3];
//
//   $user = new AccountData($data[0]);
//
//   $_SESSION['userID'] = $user->getAccountId();
//   $_SESSION['firstname'] = $user->getFirstname();
//   $_SESSION['lastname'] = $user->getLastname();
//   $_SESSION['email'] = $user->getEmail();
//
//   // echo $data[0];
//   // echo $data[0][0];
//   // echo $data[0][1];
//   // echo $data[0][2];
//   // echo $data[0][3];
//
//
// }
require_once("View/entry.phtml");
echo $_SESSION['userID'];
