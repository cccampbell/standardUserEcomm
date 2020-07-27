<?php

namespace App\Model;

use App\Model\Database;

require_once('Database.php');

class ProductData {

  private $_id,
          $_prod_id,
          $_name,
          $_img,
          $_desc,
          $_price,
          $_colour,
          $_hex_colour,
          $_colour_id,
          $_slug,
          $_total_stock,
          $_stock,
          $_size,
          $_garm_id;



  public function __construct($dbRow) {

    $this->_dbInstance = Database::getInstance();
    $this->_dbHandle = $this->_dbInstance->getdbConnection();
    
    // garment info
    $this->_garm_id = $dbRow['garm_id'];
    $this->_name = $dbRow['name'];
    $this->_slug = $dbRow['slug'];
    $this->_desc = $dbRow['desc'];
    // product info
    $this->_prod_id = $dbRow['prod_id'];
    $this->_img = $dbRow['img'];
    $this->_price = $dbRow['price'];
    isset($dbRow['colour']) ? $this->_colour = $dbRow['colour'] : $this->_colour = '';
    $this->_hex_colour = $dbRow['hex'];
    $this->_colour_id = $dbRow['colour_id'];
    
    isset($dbRow['stock_id']) ? $this->_id = $dbRow['stock_id'] : $this->_id = '';
    isset($dbRow['size']) ? $this->_size = $dbRow['size'] : $this->size = '';
    //stock for specific product, with colour, and size
    isset($dbRow['stock']) ? $this->_stock = $dbRow['stock'] : $this->_stock = '';
    // stock for all size range
    isset($dbRow['total_stock']) ? $this->_total_stock = $dbRow['total_stock'] : $this->_total_stock = '';

  }

  public function getProductId() {
    return $this->_prod_id;
  }
  public function getId() {
    return $this->_id;
  }
  public function getGarmId() {
    return $this->_garm_id;
  }
  public function getName() {
    return $this->_name;
  }
  public function getImg() {
    return $this->_img;
  }
  public function getDesc() {
    return $this->_desc;
  }
  public function getSize() {
    return $this->_size;
  }
  public function getPrice() {
    return $this->_price;
  }
  public function getColour() {
    return $this->_colour;
  }
  public function getColourId() {
    return $this->_colour_id;
  }
  public function getColourHex() {
    return $this->_hex_colour;
  }
  public function getSlug() {
    return $this->_slug;
  }

  public function getStock() {
    return $this->_stock;
  }



  // GET STOCK FOR PRODUCT
  public function getStockTotal() {
    return $this->_total_stock;
  }
  // GET QUANTITY OF EACH SIZE
  public function getProductSizeQuantity($id, $colour) {

    $query = 'SELECT Stock.colour, Size.size, Stock.stock FROM Stock INNER JOIN Size ON Stock.size = Size.id AND Stock.product = :id';
    $statement = $this->_dbHandle->prepare($query);

    $statement->bindParam(':id',$id);
    $statement->execute();

    $colourArr = $this->getProductColours($id);

    while($row = $statement->fetch()) {

      // MATCH COLOUR ID IN ARRAY TO COLOUR ID IN TABLE
      if($colourArr[$row['colour']]) {

        $colourArr[$row['colour']]['stock'][$row['size']] = $row['stock'];
      }
    }

    $this->_colour_stock_arr = $colourArr;
    return $colourArr;

  }

  public function getColourStock() {

    // query for db
    $query = 'SELECT Stock.colour, Size.size, Stock.stock 
              FROM Stock 
              INNER JOIN Size ON Stock.size = Size.id 
              AND Stock.product = '. $this->_id .'
              AND colour = ' .$this->_colour;

    $statement = $this->_dbHandle->prepare($query);
    $statement->execute();

    // CREATE ARRAY FOR SIZE QUANTITYS
    $arr = array();

    while($row = $statement->fetch()) {
      $arr[ $row['size'] ] = $row['stock'];
    }

    return $arr;

  }

  private function getColours($id) {

    $query = 'SELECT distinct colour FROM Stock WHERE product = :id';

    $statement = $this->_dbHandle->prepare($query);
    $statement->bindParam(':id',$id);
    $statement->execute();

    $colourIds = [];

    while($row = $statement->fetch()) {
      // var_dump($row);
      array_push($colourIds, $row[0]);
    }
    // array to string (v,a,l,u,e)
    $idString = implode(',',$colourIds);


    $newQuery = 'SELECT * FROM Colour WHERE id IN ('.$idString.')';

    $newStatement = $this->_dbHandle->prepare($newQuery);
    $newStatement->execute();

    $dataSet = array();
    
    $arr = [];

    while($colourRow = $newStatement->fetch()) {

      array_push($arr, $colourRow['id']);

      $dataSet[$colourRow['id']] = array(
        'id' => $colourRow['id'],
        'colour_name' => $colourRow['colour_name'],
        'hex_code' => $colourRow['hex_colour'],
        'stock' => array(),
      );

    }

    return $dataSet;
  }

  public function checkTotalStock() {

    if((int)$this->_total_stock == 0) {
      return "no stock";
    } elseif ((int)$this->_total_stock <= 40) {
      return "last few";
    } else {
      return 'in stock';
    }

  }

  public function checkQuantity($size) {
    if($this->_stock[$size] == 0) {
      return "no stock left";
    } elseif ($this->_stock[$size] <= 20) {
      return "last few in stock";
    } else {
      return;
    }
  }

  public function getProductColours($id) {

    $query = 'SELECT distinct colour FROM Stock WHERE product = :id';

    $statement = $this->_dbHandle->prepare($query);
    $statement->bindParam(':id',$id);
    $statement->execute();

    $colourIds = [];

    while($row = $statement->fetch()) {
      // var_dump($row);
      array_push($colourIds, $row[0]);
    }
    // array to string (v,a,l,u,e)
    $idString = implode(',',$colourIds);


    $newQuery = 'SELECT * FROM Colour WHERE id IN ('.$idString.')';

    $newStatement = $this->_dbHandle->prepare($newQuery);
    $newStatement->execute();

    $dataSet = array();
    
    $arr = [];

    while($colourRow = $newStatement->fetch()) {

      array_push($arr, $colourRow['id']);

      $dataSet[$colourRow['id']] = array(
        'id' => $colourRow['id'],
        'colour_name' => $colourRow['colour_name'],
        'hex_code' => $colourRow['hex_colour'],
        'stock' => array(),
      );

    }

    return $dataSet;
  }

  public function hasStock($quantity) {

    $query = 'SELECT quantity
		          FROM product_order_stock_view
              WHERE id = '. $this->_id ;


    $statement = $this->_dbHandle->prepare($query);
    $statement->execute();

    $stock = (int) $statement->fetch()[0];

    return $stock >= (int) $quantity ? true : false;

  }

  // unique id for prod with correct colour and size
  // private function getFullID() {

  //   $query = 'SELECT id FROM Stock
  //             WHERE product = '.$this->_id.'
  //             AND colour = '.$this->_colour.'
  //             AND size = (SELECT id FROM Size WHERE size = "'. $this->_size .'")';
    
  //   $statement = $this->_dbHandle->prepare($query);
  //   $statement->execute();

  //   return (int) $statement->fetch()[0];

  // }

}
