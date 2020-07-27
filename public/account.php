<?php 
session_start();
require_once("Model/AccountData.php");
require_once("Model/AccountDataSet.php");

$accountDS = new AccountDataSet();

$user = $accountDS->fetchAccount($_SESSION['userID']);

// print_r($user);

// array for personal
$personal = array(
    'firstname' => $user->getFirstname(),
    'lastname' => $user->getLastname(),
    'email' => $user->getEmail(),
    'password' => $user->getPass(),
    'dob' => $user->getDob()
);

$address_template = array(
    'first line' =>'',
    'second line' =>'',
    'town' =>'',
    'city' =>'',
    'postcode' =>'',
);

// 'id' = [
// // all address info as k => v
// ]

$addresses = array();

$encoded = json_encode($personal);

// array for address
$adr = $user->getAllAddresses();

foreach($adr as $address) {

    $new_arr = $address_template;
    $address[$address['id']] = $new_arr;
    foreach($address as $k => $v) {
        if(isset($new_arr[$k])) {
            $new_arr[$k] = $v;
        }
    }
    
    // print_r($new_arr);
    array_push($addresses, $new_arr);
}


print_r($addresses);

// print_r($adr);

$view = new stdClass();

$view->pageTitle = "Account Page";




require_once("View/account.phtml");