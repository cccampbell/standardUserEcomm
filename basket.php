<?php
session_start();
require_once('Model/ProductData.php');
require_once('Model/ProductDataSet.php');
require_once('Model/BasketData.php');

$view = new stdClass();



$view->products = [];

foreach($_SESSION['basket'] as $basket_item ) {


  $basketData = new BasketData($basket_item['id'], $basket_item['color'], $basket_item['size'], $basket_item['quantity']);

  $view->products[] = $basketData;

};




session_destroy();


require_once('View/basket.phtml');
