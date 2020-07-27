<?php
// session_start();
// require_once('Model/ProductDataSet.php');
// require_once('Model/ProductData.php');
// require_once('Model/ProductFunctions.php');


// $productFunctions = new ProductFunctions();

// $id = $_GET['prodid'];
// $colour = $_GET['colour'];


// if(isset($_POST['log_out_user'])) {
//     header("Location:/index.php");
//     session_destroy();
// }

// $view = new stdClass();

// $productdata = new ProductDataSet();

// echo ProductDataSet::class;

// // <-- PRODUCT OBJECT
// $view->product = $productdata->getProductDefault($id, $colour);

// $selected_product = $view->product;
// -->



// debug

// print_r($selected_product);
// echo $selected_product == $product2 ? 'work' : 'not';
// print_r($product2);
// print_r($selected_product);

// PRODUCT QUANITITY TOTAL
// $total_stock = $productdata->getProductQuantity($_GET['prodid']);

// // COLOUR ARRAY
// $colors = $productdata->getProductColours($id);
// // $product_colors = array();

// $stock = $productdata->getProductSizeQuantity($_GET['prodid']);

// // STOCK FOR CURRENTLY SELECTED COLOUR
// $currentProduct = $stock[$_GET['colour']];


// $product_colors = $productdata->getProductColours($_GET['prodid']);



// require_once('View/product.phtml');
