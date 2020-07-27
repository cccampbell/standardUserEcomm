<?php

use App\Model\ProductDataSet;

$productDS = new ProductDataSet();

$list = $productDS->fetchAllProductNames();

// print_r($list);

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
    print_r($suggestion . " : " . $value . " <br>");
    // echo '<div class="suggestion"><a href="/products/'.$prod->getSlug().'"><img src="/img/'.$prod->getProductImg().'"><p>'.$suggestion.'</p></a></div>';
  }
}

// var_dump($list);