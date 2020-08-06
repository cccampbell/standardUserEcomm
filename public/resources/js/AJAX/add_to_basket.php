<?php 
require_once('../Model/ProductDataSet.php');
require_once('../Model/ProductData.php'); 

print_r($user_basket);

echo isset($user_basket) ? '<br>' . 'true' .'<br>': '<br>' .  'false'.'<br>';

$id = $_GET['id'];
$color = $_GET['color'];
$size = $_GET['size'];

$PDS = new ProductDataSet();

$product = $PDS->getSelectProduct($id, $color, $size);

// print_r($product);

// echo "id = " . $id . ". colour = " . $color . ". size =  " . $size;

if($size == 'size selection' || $size === ''){
    return;
} else {

        echo '<div class="basket_item" value="'. $i .'">
            <img src="/img/'. $product->getProductImg() .'"/>

            <div class="bitem_info">

            <div class="bitem_name">
              <h4 class="item_name">'. $product->getProductName() .'</h4>
            </div>

            <div class="bitem_selected">
              <h6 class="item_size">size : '. $product->getSize() .'</h6>
              <h6 class="item_color">color : '. $product->getColour() .'</h6>
            </div>

          </div>
          </div>';

          $user_basket->add($product, 1);

}








?>

