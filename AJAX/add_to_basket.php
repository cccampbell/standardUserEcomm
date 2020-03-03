<?php 

session_start();
require_once('../Model/ProductDataSet.php');
require_once('../Model/ProductData.php'); 

$id = $_GET['id'];
$color = $_GET['color'];
$size = $_GET['size'];

// 
// CHECK TO SEE IF CAN STREAMLINE SO YOU DONT NEED TO INITIALIZE
// CLASS TO GET PRODUCT INFORMATION
$product = [
    'id' => $id,
    'color' => $color,
    'size' => $size,
    'quantity' => 1,
];

if($color == 'size selection' || $size === ''){
    return;
} else {

    // CHECK TO SEE IF THERE IS A BASKET
if(!isset($_SESSION['basket'])) {
    $_SESSION['basket'] = [];
}

// CHECK IF BASKET EMPTY
if(empty($_SESSION['basket'])) {
    $aProductDataSet = new ProductDataSet();

        $basketItem = $aProductDataSet->FetchProduct($product[id]);

        $this_item = $basketItem;

        echo '<div class="basket_item" value="'. $i .'">
            <img src="/img/'. $this_item->getProductImg() .'"/>

            <div class="bitem_info">

            <div class="bitem_name">
              <h4 class="item_name">'. $this_item->getProductName() .'</h4>
            </div>

            <div class="bitem_selected">
              <h6 class="item_size">size : '. $product[size] .'</h6>
              <h6 class="item_color">color : '. $product[color] .'</h6>
            </div>

          </div>
          </div>';

    array_push($_SESSION['basket'], $product);
    
} 
else {
    
    //VAR TO HOLD BOOL TO CHECK IF IN BASKET
    $in_basket = false;    

    //GO THROUGH EACH ITEM IN BASKET TO SEE IF EXACT ITEM ALREADY THERE
    foreach($_SESSION['basket'] as &$basket_product) {
        // CHECKS AGAINST EACH ID COLOR SIZE IF TRUE
        if($product['id'] === $basket_product['id'] && $product['size'] === $basket_product['size'] && $product['color'] === $basket_product['color']) {
            
            $in_basket = true;
            $basket_product['quantity'] += 1;
        }
    }

    if($in_basket) {
        return;

    } else {
        $aProductDataSet = new ProductDataSet();

        $basketItem = $aProductDataSet->FetchProduct($product[id]);

        $this_item = $basketItem;

        echo '<div class="basket_item" value="'. $i .'">
            <img src="/img/'. $this_item->getProductImg() .'"/>

            <div class="bitem_info">

            <div class="bitem_name">
              <h4 class="item_name">'. $this_item->getProductName() .'</h4>
            </div>

            <div class="bitem_selected">
              <h6 class="item_size">size : '. $product[size] .'</h6>
              <h6 class="item_color">color : '. $product[color] .'</h6>
            </div>

          </div>
          </div>';
        array_push($_SESSION['basket'], $product);
    }


}

}







?>

