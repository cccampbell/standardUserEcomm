<?php
session_start();
require_once('Model/ProductDataSet.php');
require_once('Model/ProductData.php');
require_once('Model/ProductFunctions.php');


$productFunctions = new ProductFunctions();


if(isset($_POST['log_out_user'])) {
    header("Location:/index.php");
    session_destroy();
}

$view = new stdClass();

$productdata = new ProductDataSet();

$view->product = $productdata->FetchProduct($_GET['prodid']);

$selected_product = $view->product;

// PRODUCT QUANITITY TOTAL
$total_stock = $productdata->getProductQuantity($_GET['prodid']);

// COLOUR ARRAY
$colors = $selected_product->getProductColours($_GET['prodid']);
// $product_colors = array();

$stock = $productdata->getProductSizeQuantity($_GET['prodid']);

$currentProduct = $stock[$_GET['colour']];


// $firstKey = array_key_first($stock);
// reset($stock)

// var_dump($stock);

// var_dump($productFunctions->colorsToArray($colors));

// echo $productdata->getProductQuantity($_GET['prodid']);

$product_colors = $productdata->getProductColours($_GET['prodid']);

// var_dump($product_colors);
// session_destroy();

require_once('View/product.phtml');
