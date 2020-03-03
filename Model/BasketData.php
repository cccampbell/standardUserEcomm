<?php

require_once('ProductDataSet.php');
require_once('ProductData.php');

class BasketData {
    
    private $_id, $_size, $_color, $_quantity;
    private $_img, $_name, $_price;

    public function __construct($_id, $_color, $_size, $_quantity) {

        $this->_id = $_id;
        $this->_size = $_size;
        $this->_color = $_color;
        $this->_quantity = $_quantity;

        $productData = new ProductDataSet();
        $product_object = $productData->FetchProduct($_id);

        $this->_img = $product_object->getProductImg();
        $this->_name = $product_object->getProductName();
        $this->_price = $product_object->getProductPrice();

    }

    public function getName() {
        return $this->_name;
    }

    public function getId() {
        return $this->_id;
    }

    public function getSize() {
        return $this->_size;
    }

    public function getQuantity() {
        return $this->_quantity;
    }

    public function getImg() {
        return $this->_img;
    }

    public function getPrice() {
        return $this->_price;
    }

    public function getColor() {
        return $this->_color;
    }

    private function setQuantity($quantity) {
        $this->_quantity = $quantity;
    }

    private function addToQuantity() {
        $this->_quantity += 1;
    }

}
?>