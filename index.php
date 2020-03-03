<?php
session_start();
require_once("Model/AccountDataSet.php");
require_once("Model/Entry.php");

$view = new stdClass();
$view->pageTitle = "standard clothing";

if(isset($_POST['log_out_user'])) {
    header("Location:/index.php");
    session_destroy();
}

if (isset($_POST['search_btn'])) {
  $searched_term = $_POST['nav_search'];
  //methodToSearchDb($searched_term);
}
// $accountDataSet = new AccountDataSet();
//
// $view->accountData = $accountDataSet->fetchAllAccounts();

// if(isset($_POST['log_in_user'])) {
//
//   $name = $_POST['name'];
//   $email = $_POST['email'];
//   $pass = $_POST['password'];
//
//   //echo $name. " " . $email . " " . $pass;
//
//   $entry = new Entry();
//
//   $data = $entry->logIn($email, $pass);
//
//   //echo $data[3];
//
//   if($data[0][3] === $email) {
//     $_SESSION['email'] = $email;
// }
// }

// print_r(array_chunk($view->accountData, 1));

//echo count($view->accountData);

// $con = new mysqli("127.0.0.1", "root", "QZaJCe$4Nxd", "user_Db");
// $message = $con->query("SELECT * FROM Account")->fetch_object()->message;
// $con->close();
// echo "$message <br/>";
// echo "Hello From Sites Folder!";
// phpinfo();


//echo $view->accountData;

require_once("View/index.phtml");
