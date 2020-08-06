<?php
require_once('Model/ProductDataSet.php');
require_once('Model/ProductData.php');

$productDataSet = new ProductDataSet();

$view = new stdClass();

// echo $_GET['filter'];

if($_GET['filter']) {

    $ids = explode(',', $_GET['filter']);
    $view->products = $productDataSet->fetchFilters($ids);

} else {
    $view->products = $productDataSet->fetchAllProducts();
}





foreach($view->products as $product) {

    $stock = $productDataSet->getProductSizeQuantity($product->getProductId());

    // GETTING THE FIRST COLOUR OF PRODUCT (AS DEFAULT)
    $first = reset($stock);

    echo '<div class="cell small-6 large-3 product">
        <a href="/product.php?prodid='. $product->getProductId() .'&prodname='. $product->getProductName() .'&colour='. $first['id'] .'">
            <div class="product_thumbnail">
                <img src="/img/'.$product->getProductImg().'" class="prod_img">
            </div>
            <div class="prod_info">
                <div class="prod_card_info grid-x">
                    <div class="prod_info_left cell small-9">
                        <h3 class="prod_name">'.$product->getProductName().'</h3>
                        <div class="prod_colors">';
                        
                        foreach($stock as $color) {


                            echo '<div value="'.$color['id'].'" class="color_of_product" style="background-color:#'. $color['hex_code'] .'"></div>';

                        };
                            
                            echo '</div>
                        </div>

                        <h4 class="prod_price small-3">£'. $product->getProductPrice() .'</h4>
                    </div>



                </div>
            </a>
        </div>';
}