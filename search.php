<?php
require_once('Model/ProductDataSet.php');

$productDS = new ProductDataSet();

$list = $productDS->fetchAllProductNames();

//gets the parameter from the URL for search typed by user
$q = $_REQUEST['q'];
$suggest = [];
//looks up all hints from array to check if similar to $q

if($q != "") {
  $q = strtolower($q);
  $len = strlen($q);
  foreach ($list as $name => $value) {
    if(stristr($q, substr($name, 0 ,$len))) {
      if($suggest === "") {
        $suggest[] = $name;
      } else {
        $suggest[$name] = $value;
      }
    }
  }
}

if(count($suggest) === 0) {
  echo 'no suggestion';
} else {
  foreach($suggest as $suggestion => $value) {
    $prod = $productDS->FetchProduct($value);
    $stock = $productDS->getProductSizeQuantity($prod->getProductId());
    $first = reset($stock)['id'];
    echo '<div class="suggestion"><a href="/product.php?prodid='. $prod->getProductId() .'&prodname='. $prod->getProductName(). '&colour=' . $first.'"><img src="/img/'.$prod->getProductImg().'"><p>'.$suggestion.'</p></a></div>';
  }
}

// var_dump($list);