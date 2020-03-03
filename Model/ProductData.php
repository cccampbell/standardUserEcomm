<?php

class ProductData {
  private $_id,$_prod_name,$_prod_img,$_prod_desc,$_prod_k_f,$_prod_care,$_prod_price,$_prod_colours,$_slug, $_stock, $_created_at, $_updated_at, $_category;

  public function __construct($dbRow) {
    $this->_id = $dbRow['ProductId'];
    $this->_prod_name = $dbRow['Prod_name'];
    $this->_prod_desc = $dbRow['desc'];
    $this->_prod_img = $dbRow['Prod_img'];
    $this->_prod_k_f = $dbRow['ke_features'];
    $this->_prod_care = $dbRow['care_label'];
    $this->_prod_price = $dbRow['prod_price'];
    $this->_prod_colours = $dbRow['Productcolours'];
    $this->_slug = $dbRow['slug'];
    $this->_stock = $dbRow['stock'];
    $this->_created_at = $dbRow['created_at'];
    $this->_updated_at = $dbRow['updated_at'];
    $this->_category = $dbRow['category'];
  }

  public function getProductId() {
    return $this->_id;
  }
  public function getProductName() {
    return $this->_prod_name;
  }
  public function getProductImg() {
    return $this->_prod_img;
  }
  public function getProductDesc() {
    return $this->_prod_desc;
  }
  public function getProductKeyFeatures() {
    return $this->_prod_k_f;
  }
  public function getProductCareLabel() {
    return $this->_prod_care;
  }
  public function getProductPrice() {
    return $this->_prod_price;
  }
  public function getProductColours() {
    return $this->_prod_colours;
  }
  public function getSlug() {
    return $this->_slug;
  }
  // GET STOCK FOR PRODUCT
  public function getStockQuantity() {
    return $this->_stock;
  }
  public function getCategory() {
    return $this->_category;
  }

  public function checkQuantity() {
    if($this->_stock == 0) {
      return "no stock left";
    } elseif ($this->_stock <= 10) {
      return "last few in stock";
    } else {
      return;
    }
  }
}
