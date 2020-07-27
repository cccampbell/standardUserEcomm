<?php
// session_start();
// require_once('Model/ProductDataSet.php');
// require_once('Model/ProductData.php');
// require_once('Model/ProductFunctions.php');

// $view = new stdClass();
// $productDataSet = new ProductDataSet();
// $productFunctions = new ProductFunctions();



// $sort_arr = ['high','low','new'];
// $checkedBoxes = $_POST['checkbox_filter'];

// if(isset($_POST['log_out_user'])) {
//     header("Location:/index.php");
//     session_destroy();
// }

// if(isset($_POST['submit_filter'])) {

//     header('?filter=' . implode(',', $checkedBoxes)); 
//     // echo 'products.php?filter=' . implode(',', $checkedBoxes);
// }

// if(isset($_POST['sort'])) {
//     echo $_POST['sort'];
// }

// if(isset($_GET['filter'])) {

//     if(isset($_GET['sort'])) {
//         $view->products = $productDataSet->sortAndFilterProducts(explode(',', $_GET['filter']), $_GET['sort']);
        
//     } else {
//         $view->products = $productDataSet->fetchFilters(explode(',', $_GET['filter']));
//     }
//     // ARRAY OF EACH FILTER ID
//     $filter_arr = explode(',', $_GET['filter']);


// }
//  else {
//     $view->products = $productDataSet->getInStockProducts();
//     // print_r($view->products);
// }


// require_once('View/template/header.phtml');
// require_once('View/products.phtml');


