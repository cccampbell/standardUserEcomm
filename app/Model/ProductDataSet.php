<?php

namespace App\Model;

use PDO;
use App\Model\Database;
use App\Support\Exceptions\NoSlugMatch;
use App\Support\Exceptions\NoTypeMatch;

require_once('ProductData.php');
require_once('Database.php');

class ProductDataSet {
  
  protected $_dbHandle, $_dbInstance;

  //dataset constructor
  public function __construct() {
        $this->_dbInstance = Database::getInstance();
        
        $this->_dbHandle = $this->_dbInstance->getdbConnection();
        
  }

  // get products that have stock
  public function getInStockProducts() {

    $query = 'SELECT products.id as id, garments.name, garments.id as garm_id, garments.desc, products.img, products.price, products.slug, products.colour_id, colours.name as colour, colours.hex, SUM(product_order_stock_view.quantity) AS total_stock
              FROM products
              INNER JOIN
              stock ON products.id = stock.product
              INNER JOIN garments
              ON products.garment_id = garments.id
              INNER JOIN colours
              ON products.colour_id = colours.id
              INNER JOIN product_order_stock_view
              ON stock.id = product_order_stock_view.id
              GROUP BY products.colour_id, products.id';

    $statement = $this->_dbHandle->prepare($query);
    $statement->execute();
    $data = [];

    while($row = $statement->fetch()) {
      $data[] = new ProductData($row);
    }
    return $data;

  }
  // params: $type (category || collection) :String, $slug :String 
  public function getTypeProducts($type, $slug) {

    $query = "";

    if($type === 'collection') {

      $query = "SELECT product_id FROM collection_products WHERE collection_id = 
              (SELECT id FROM collections WHERE slug = :slug)";

    } elseif ($type === 'category') {
      $query = "SELECT product_id FROM product_category WHERE category_id = 
              (SELECT id FROM categories WHERE slug = :slug)";
    } else {
      throw new NoTypeMatch;
    }

    $statement = $this->_dbHandle->prepare($query);
    // $statement->bindParam();
    $statement->execute(array('slug' => $slug));

    $product_ids = [];

    while($row = $statement->fetch(PDO::FETCH_ASSOC)) {

      $product_ids[] = $row['product_id'];
    }
    
    $dataSet[] = $this->getProducts($product_ids);
    // print_r($dataSet);


    return $dataSet;


  }

  public function getProductWithStockID($id) {

    $query = 'SELECT stock.id AS stock_id, products.id as prod_id, garments.id AS garm_id, garments.name, garments.desc, products.img, products.price, products.slug,colours.id AS colour_id, colours.name AS colour, colours.hex, Sizes.size, product_order_stock_view.quantity as stock
              FROM stock
              INNER JOIN Products
              ON stock.product = products.id
              INNER JOIN garments
              ON products.garment_id = garments.id
              INNER JOIN Colours
              ON products.colour_id = colours.id
              INNER JOIN Sizes
              ON stock.size = Sizes.id
              INNER JOIN product_order_stock_view
              ON stock.id = product_order_stock_view.id
              WHERE stock.id = :id';
    
    $statement = $this->_dbHandle->prepare($query);
    $statement->bindParam(':id', $id);
    $statement->execute();

    return new ProductData($statement->fetch());

  }

  public function fetchProduct($id) {

    $query = "SELECT products.id as id, garments.name, garments.id as garm_id,garments.desc, products.img, products.price, products.slug, products.colour_id, colours.name as colour, colours.hex, SUM(Stock.stock) AS total_stock
              FROM products
              INNER JOIN
              stock ON products.id = stock.product
              INNER JOIN garments
              ON products.garment_id = garments.id
              INNER JOIN colours
              ON products.colour_id = colours.id
              GROUP BY products.colour_id, products.id
              WHERE products.id = :id ";

    $statement = $this->_dbHandle->prepare($query);
    $statement->execute(array(':id' => $id));
    $data = [];

    while($row = $statement->fetch(PDO::FETCH_ASSOC)) {
      $data[] = new ProductData($row);
    }
    return $data;

  }
  
  // with given param [$ids]:int returns array of class Products
  public function collectProducts($ids) {

    // var_dump($ids);
    $products = [];

    // SET NUMBER OF PARAMS TO BE SET FOR EACH PRODUCT_ID
    $param  = str_repeat('?,', count($ids) - 1) . '?';

    $query = "SELECT stock.id as stock_id, products.id as prod_id, garments.name, garments.id as garm_id,garments.desc, Sizes.size, products.img, products.price, products.slug, products.colour_id, colours.name as colour, colours.hex, product_order_stock_view.quantity as stock
              FROM products
              INNER JOIN
              stock ON products.id = stock.product
              INNER JOIN garments
              ON products.garment_id = garments.id
              INNER JOIN colours
              ON products.colour_id = colours.id
              INNER JOIN Sizes
              ON stock.size = Sizes.id
              INNER JOIN product_order_stock_view
              ON stock.id = product_order_stock_view.id
              WHERE stock.id IN ($param)";

    $statement  = $this->_dbHandle->prepare($query);

    // SET NUMBER OF PARAMS TO BE SET FOR EACH PRODUCT_ID
    $types = str_repeat('s', count($ids));

    //BIND PARAMS IN QUERY(? = S) TO THE ARRAY OF PRODUCT_ID (...$ARRAY = {[1][2] === 1,2})
    $statement->bindParam($types, ...$ids);
    $statement->execute($ids);

    while( $row = $statement->fetch(PDO::FETCH_ASSOC) ) {
      $products[] = new ProductData($row);

    }
    // print_r($products);
    return $products;

  }
  // returns arary of all product names
  public function fetchAllProductNames() {

        $sqlQuery = 'SELECT products.id, garments.name, products.slug, products.img  FROM garments
                      INNER JOIN products
                      ON garments.id = products.garment_id';

        //debugging purposes
        //echo $sqlQuery;

        $statement = $this->_dbHandle->prepare($sqlQuery);
        $statement->execute();

        $dataSet = [];
        while($row = $statement->fetch()) {
          $dataSet[$row['name']] = $row;
        }
        return $dataSet;

  }

  public function getProducts($ids) {
    // print_r($ids);
    $products = [];

    // SET NUMBER OF PARAMS TO BE SET FOR EACH PRODUCT_ID
    $param  = str_repeat('?,', count($ids) - 1) . '?';

    $query = "SELECT products.id as prod_id, garments.name, garments.id as garm_id, garments.desc, products.img, products.price, products.slug, products.colour_id, colours.name as colour, colours.hex, SUM(product_order_stock_view.quantity) AS total_stock
              FROM products
              INNER JOIN
              stock ON products.id = stock.product
              INNER JOIN garments
              ON products.garment_id = garments.id
              INNER JOIN colours
              ON products.colour_id = colours.id
              INNER JOIN product_order_stock_view
              ON stock.id = product_order_stock_view.id
              WHERE products.id IN ($param)
              GROUP BY products.colour_id, products.id";

    $statement  = $this->_dbHandle->prepare($query);

    // SET NUMBER OF PARAMS TO BE SET FOR EACH PRODUCT_ID
    $types = str_repeat('s', count($ids));

    //BIND PARAMS IN QUERY(? = S) TO THE ARRAY OF PRODUCT_ID (...$ARRAY = {[1][2] === 1,2})
    $statement->bindParam($types, ...$ids);
    $statement->execute($ids);

    while( $row = $statement->fetch(PDO::FETCH_ASSOC) ) {
      
      $products[] = new ProductData($row);

    }
    // print_r($products);
    return $products;
  }

  // debug - sort out product class to hierarchy class of product and garm class

  // returns remianing stock for each product size 
  public function getAllGarmData($slug) {


    $query = "SELECT stock.id as stock_id, products.id as product_id, garments.id as garm_id, garments.name, products.img, products.price, products.slug, garments.desc, colours.id as colour_id, colours.name as colour, Sizes.size, colours.hex, product_order_stock_view.quantity as stock 
              FROM garments
              INNER JOIN products
              ON garments.id = products.garment_id
              INNER JOIN colours
              ON colour_id = colours.id
              INNER JOIN stock
              ON products.id = stock.product
              INNER JOIN Sizes
              ON stock.size = Sizes.id
              INNER JOIN product_order_stock_view
              ON stock.id = product_order_stock_view.id
              WHERE garments.id = ( 
                SELECT garment_id
                FROM products 
                WHERE slug = :slug)";

    $statement = $this->_dbHandle->prepare($query);

    $statement->execute(array('slug' => $slug));

    $data = [];
    $colours = [];
    $size_stock = [];
    $totalStock;

    while($row = $statement->fetch(PDO::FETCH_ASSOC)) {

      $totalStock += $row['stock'];

      // stock_total - array of size stock and # of total stock
      
      $data[$row['slug']] = [

        'name' => $row['name'],
        'prod_id' => $row['product_id'],
        'garm_id' => $row['garm_id'],
        'price' => $row['price'],
        'desc' => $row['desc'],
        'img' => $row['img'],
        'slug' => $row['slug'],
        'colour_id' => $row['colour_id'],
        'colour' => $row['colour'],
        'hex' => $row['hex'],
        'stock' => '',
        'total_stock' =>  [],

      ];

      //getting all stock for each product (garment of each colour)
      $size_stock[ $row['slug'] ][ $row['size'] ] = [
        'quantity' => $row['stock'],
        'id' => $row['stock_id']
      ];


    }

    


    // PUSHES EACH SIZE QUANTITY INTO SELECTED PRODUCT STOCK 
    foreach($size_stock as $key => $value) {

      $data[$key]['total_stock'] = $value;
      $data[$key]['total_stock']['total'] = $totalStock;

      // put product data into product obj
      $product = new ProductData($data[$key]);

      $data[$key] = $product;

    }

    return array(
      'product_data' => $data,
      // 'colours' => $colours
    );



  }

   public function getAllGarmDataViaId($id) {


    $query = 'SELECT stock.id as stock_id, products.id as product_id, garments.id as garm_id, garments.name, products.img, products.price, products.slug, garments.desc, colours.id as colour_id, colours.name as colour, Sizes.size, colours.hex, product_order_stock_view.quantity as stock 
              FROM garments
              INNER JOIN products
              ON garments.id = products.garment_id
              INNER JOIN colours
              ON colour_id = colours.id
              INNER JOIN stock
              ON products.id = stock.product
              INNER JOIN Sizes
              ON stock.size = Sizes.id
              INNER JOIN product_order_stock_view
              ON stock.id = product_order_stock_view.id
              WHERE garments.id = :id';

    $statement = $this->_dbHandle->prepare($query);

    $statement->execute(array('id' => $id));

    $data = [];
    $colours = [];
    $garm = [];

    while($row = $statement->fetch(PDO::FETCH_ASSOC)) {

      // print_r($row);
      // $totalStock = $data[$row['colour_id']['total_stock']] + $row['stock'];
      $totalStock = $data[$row['colour_id']]['total_stock'] + $row['stock'];
      // echo $row['stock'];
      
      $data[$row['colour_id']] = [

        'name' => $row['name'],
        'prod_id' => $row['product_id'],
        'garm_id' => $row['garm_id'],
        'price' => $row['price'],
        'desc' => $row['desc'],
        'img' => $row['img'],
        'slug' => $row['slug'],
        'colour_id' => $row['colour_id'],
        'colour' => $row['colour'],
        'hex' => $row['hex'],
        'stock' => [],
        'total_stock' => $totalStock ,

      ];

      //getting all stock for each product (garment of each colour)
      $garm[$row['colour_id']][$row['size']] = $row['stock_id'];

    }


    // PUSHES EACH SIZE QUANTITY INTO SELECTED PRODUCT STOCK 
    foreach($garm as $key => $value) {

      $data[$key]['stock'] = $value;

      // put product data into product obj
      $product = new ProductData($data[$key]);

      $data[$key] = $product;

    }

    return array(
      'product_data' => $data,
      // 'colours' => $colours
    );



  }

  public function fetchAllColours() {
    $sqlQuery = 'SELECT * FROM Colours';

    $statement = $this->_dbHandle->prepare($sqlQuery); //prepare PDO $statement
    $statement->execute();

    $dataSet = [];
    while($row = $statement->fetch()) {
      $dataSet[] = $row;
    }
    return $dataSet;

  }


  // //////
  //    FILTER AND SORT
  // //////

  // takes 2 param ( 1. slug of type 2. number of filters for type )
  public function createQueryType($slug, int $length) {

    $query = "";

    
    // creates # of placeholders '?' for query with given length
    $placeholder = str_repeat('?,', $length - 1) . '?';


    // check to see given $slug type
    switch ($slug) {
      case 'category':
        $query = "categories.slug IN ($placeholder)";
        break;
      case 'collection':
        $query = "collections.slug IN ($placeholder)";
        break;
      case 'colours':
        $query = "colours.name IN ($placeholder)";
        break;
      case 'size':
        $query = "stock.size IN ($placeholder)";
        break;
      default:
        throw new NoSlugMatch;
        return;
        break;
    }
    // var_dump('<br>' . $query . '<br>');
    
    // return sql query for given type with number of placeholders
    return $query;
  }

  public function filterProducts($filters, $sort) {

    // EMPTY SORTING QUERY
    $sorting;
        // CREATE SORTING QUERY
    if($sort === 'high') {
      $sorting = " ORDER BY price DESC;";
    } elseif($sort === 'low') {
      $sorting = " ORDER BY price ASC;";
    } else {
      $sorting = "";
    }

    print_r($filters);


    $params  = str_repeat('?,', count($filters) - 1) . '?';

    $query = "SELECT product_id FROM product_category
              INNER JOIN categories
              ON product_category.category_id = categories.id
              WHERE categories.slug IN ($params)" . $sorting;

    $statement  = $this->_dbHandle->prepare($query);

    //BIND PARAMS IN QUERY(? = S) TO THE ARRAY OF PRODUCT_ID (...$ARRAY = {[1][2] === 1,2})
    $statement->execute($filters);

    $ids = [];

    while($row = $statement->fetch(PDO::FETCH_ASSOC)) {
      $ids[] = $row['product_id'];
    }

    return $this->getProducts($ids);

  }

  //$filter - array of filters ( [x,y] ) && $sort - string 'high' || 'low
  public function sortAndFilterProducts($filters, $sort) {
    // EMPTY SQL QUERY
    $sql;
    // EMPTY SORTING QUERY
    $sorting;
    // EMPTY STATEMENT
    $statement;

    // CREATE SORTING QUERY
    if($sort === 'high') {
      $sorting = " ORDER BY prod_price DESC;";
    } elseif($sort === 'low') {
      $sorting = " ORDER BY prod_price ASC;";
    } else {
      $sorting = ";";
    }

    //check if there are filters
    if($filters[0] === '') {
      $sql = "SELECT * FROM Products". $sorting;

      $statement  = $this->_dbHandle->prepare($sql);
      $statement->execute();
    } else {
      // SET NUMBER OF PARAMS TO BE SET FOR EACH PRODUCT_ID
      $param  = str_repeat('?,', count($filters) - 1) . '?';

      $sql = "SELECT * from Products WHERE id IN (SELECT id FROM Product_Categories WHERE category in ($param))" . $sorting;

      $statement  = $this->_dbHandle->prepare($sql);

      // SET NUMBER OF PARAMS TO BE SET FOR EACH PRODUCT_ID
      $types = str_repeat('s', count($filters));

      //BIND PARAMS IN QUERY(? = S) TO THE ARRAY OF PRODUCT_ID (...$ARRAY = {[1][2] === 1,2})
      $statement->bindParam($types, ...$filters);
      $statement->execute($filters);
    }

    
    
    $dataSet = [];

    while($row = $statement->fetch()) {
      $dataSet[] = new ProductData($row);
    }
    return $dataSet;    

  }

  // takes in current filter/s and array of slugs of new filter/s 
  public function filterCategory(String $slug ,$newFilters) {

    return $this->filterType('category', $slug, $newFilters);

  }

  public function filterCollection(String $slug ,$newFilters) {

    // $typeInFilter = false;

    // // iterate through to check if type in arr
    // for($i = 0; $i < count($newFilters); $i++) {
    //   // if so add in to string
    //   if(preg_match('/collection/', $newFilters[$i] ) ) {
    //     $newFilters[$i] .= '|' . $slug;
    //     $typeInFilter = true;
    //   }

    // }

    // // if type not in the array then add into typeArr for filtering
    // if(!$typeInFilter) {

    //   array_push($newFilters, ('collection|' . $slug));

    // }


    // $queryBlock = "SELECT DISTINCT product FROM stock
    //                 INNER JOIN products ON stock.product = products.id
    //                 INNER JOIN garments ON products.garment_id = garments.id
    //                 INNER JOIN colours ON products.colour_id = colours.id
    //                 INNER JOIN product_category ON products.id = product_category.product_id
    //                 INNER JOIN collection_products ON products.id = collection_products.product_id
    //                 INNER JOIN categories ON product_category.category_id = categories.id
    //                 INNER JOIN collections ON collection_products.collection_id = collections.id
    //                 WHERE ";


    
    // // array to store queries
    // $queryArr = [];

    // // array of types
    // $typeArr = [];

    // foreach ($newFilters as $type) {
      
    //   $arrOfType = explode('|', $type);
    //   // print_r(count($arrOfType) - 1);
    //   array_push($typeArr, ...array_slice( explode('|', $type), 1 ) );
      
    //   $queryArr[] = $this->createQueryType($arrOfType[0], ( count($arrOfType) - 1 ) );

    // }

    // $filterQuery = "";
    // // arr == 1 then just add to the end
    // if(count($queryArr) === 1) {

    //   $filterQuery = $queryArr[0];

    // } else {
    //   // if more than one iterate through
    //   for($i = 0; $i < count($queryArr); $i++) {
    //     // if the first just add to query string
    //     if($i === 0) {

    //       $filterQuery = $queryArr[0];

    //     }
    //     else {
    //       // after the [1] each time you add to end of string put AND before it to complete query
    //       $filterQuery .= " AND " . $queryArr[$i];

    //     }

    //   }

    // }
    // // add string to end of block
    // $queryBlock = $queryBlock . $filterQuery;

    // $statement  = $this->_dbHandle->prepare($queryBlock);

    // $statement->execute(array(...$typeArr));

    // // fetch all ids from query
    // $product_ids = [];
    // while($row = $statement->fetch(PDO::FETCH_NUM)) {

    //   $product_ids[] = $row[0];

    // }

    // // //returns array of selected products
    // return $this->getProducts($product_ids);

    return $this->filterType('collection', $slug, $newFilters);


  }

  public function filterType(String $type, String $slug, $newFilters) {

    $typeInFilter = false;

    // iterate through to check if type in arr
    for($i = 0; $i < count($newFilters); $i++) {
      // if so add in to string
      if(preg_match('/'.$type.'/', $newFilters[$i] ) ) {
        $newFilters[$i] .= '|' . $slug;
        $typeInFilter = true;
      }

    }

    // if type not in the array then add into typeArr for filtering
    if(!$typeInFilter) {

      array_push($newFilters, ($type .'|' . $slug));

    }


    $queryBlock = "SELECT DISTINCT product FROM stock
                    INNER JOIN products ON stock.product = products.id
                    INNER JOIN garments ON products.garment_id = garments.id
                    INNER JOIN colours ON products.colour_id = colours.id
                    INNER JOIN product_category ON products.id = product_category.product_id
                    INNER JOIN collection_products ON products.id = collection_products.product_id
                    INNER JOIN categories ON product_category.category_id = categories.id
                    INNER JOIN collections ON collection_products.collection_id = collections.id
                    WHERE ";


    
    // array to store queries
    $queryArr = [];

    // array of types
    $typeArr = [];

    foreach ($newFilters as $filter) {
      
      $arrOfType = explode('|', $filter);
      // print_r(count($arrOfType) - 1);
      array_push($typeArr, ...array_slice( explode('|', $filter), 1 ) );
      
      $queryArr[] = $this->createQueryType($arrOfType[0], ( count($arrOfType) - 1 ) );

    }

    $filterQuery = "";
    // arr == 1 then just add to the end
    if(count($queryArr) === 1) {

      $filterQuery = $queryArr[0];

    } else {
      // if more than one iterate through
      for($i = 0; $i < count($queryArr); $i++) {
        // if the first just add to query string
        if($i === 0) {

          $filterQuery = $queryArr[0];

        }
        else {
          // after the [1] each time you add to end of string put AND before it to complete query
          $filterQuery .= " AND " . $queryArr[$i];

        }

      }

    }
    // add string to end of block
    $queryBlock = $queryBlock . $filterQuery;

    $statement  = $this->_dbHandle->prepare($queryBlock);

    $statement->execute(array(...$typeArr));

    // fetch all ids from query
    $product_ids = [];
    while($row = $statement->fetch(PDO::FETCH_NUM)) {

      $product_ids[] = $row[0];

    }

    // //returns array of selected products
    return $this->getProducts($product_ids);

  }

  public function sortProducts($sort) {

    $sql = "SELECT * FROM Products ORDER BY ";

    if($sort === 'high') {
      $sql += "prod_price DESC";
    } elseif($sort === 'low') {
      $sql += 'prod_price ASC';
    }
    
    $statement  = $this->_dbHandle->prepare($sql);
    $statement->execute($ids);
    
    $dataSet = [];

    while($row = $statement->fetch()) {
      $dataSet[] = $row['id'];
      // $dataSet[] = $this->FetchProduct($row['id']);
    }

    return $this->collectProducts($ids);
    // return $dataSet;

  }

  protected function sortProductStock(array $stock) {

    // return array of stock sizes

  }

}


// /////
//  STEP BEFORE BIN, LAST CHECKS TO SEE IF IN USE ELSEWHERE IN APP
// /////

// public function FetchAllCategories() {

  //   $query = 'SELECT id, `name`, slug, is_category FROM categories';

  //   $statement = $this->_dbHandle->query($query);
  //   $statement->execute();

  //   // SEPERATE ARRAYS FOR BOTH CATEGORY AND COLLECTION
  //   $cat = [];
  //   $coll = [];

  //   while($row = $statement->fetch()) {

  //     if($row['is_category']) {
  //       array_push($cat,$row);
  //     }
  //     else {
  //       array_push($coll,$row);
  //     }
  //   }

  //   $dataSet = [
  //     'category' => $cat,
  //     'collection' => $coll,
  //   ];

  //   return $dataSet;


  // }
// public function fetchFilters($ids) {

  //   // SET NUMBER OF PARAMS TO BE SET FOR EACH PRODUCT_ID
  //   $param  = str_repeat('?,', count($ids) - 1) . '?';

  //   $sql = "SELECT product_id FROM product_categories WHERE category_id IN ($param)";
    
  //   $statement  = $this->_dbHandle->prepare($sql);

  //   // SET NUMBER OF PARAMS TO BE SET FOR EACH PRODUCT_ID
  //   $types = str_repeat('s', count($ids));

  //   //BIND PARAMS IN QUERY(? = S) TO THE ARRAY OF PRODUCT_ID (...$ARRAY = {[1][2] === 1,2})
  //   $statement->bindParam($types, ...$ids);
  //   $statement->execute($ids);
    
  //   $dataSet = [];

  //   while($row = $statement->fetch()) {
  //     $dataSet[] = $this->fetchProduct($row['product_id']);
  //   }
  //   return $dataSet;

  // }
// public function getAllProducts() {

  //   $query = 'SELECT products.id as products_id, garments.id as garm_id, garments.name, products.img, products.price, garments.slug, garments.desc, colours.id as colour_id, colours.name AS colour, colours.hex
  //             FROM products
  //             INNER JOIN
  //             garments ON garment_id = garments.id
  //             INNER JOIN colours
  //             ON colour_id = colours.id';

  //   $statement = $this->_dbHandle->prepare($query);
  //   $statement->execute();
  //   $data = [];

  //   while($row = $statement->fetch()) {
  //     $data[] = new ProductData($row);
  //   }
  //   return $data;

  // }
// public function getCollectionProducts($slug) {

    

  //   $query = "SELECT product_id FROM collection_products WHERE collection_id = 
  //             (SELECT id FROM collections WHERE slug = :slug)";

  //   $statement = $this->_dbHandle->prepare($query);
  //   // $statement->bindParam();
  //   $statement->execute(array('slug' => $slug));

  //   $product_ids = [];

  //   while($row = $statement->fetch(PDO::FETCH_ASSOC)) {

  //     $product_ids[] = $row['product_id'];
  //   }
    
  //   $dataSet[] = $this->getProducts($product_ids);
  //   // print_r($dataSet);


  //   return $dataSet;

  // }

// public function getCategoryProducts($slug) {

  //   $query = "SELECT product_id FROM product_category WHERE category_id = 
  //             (SELECT id FROM categories WHERE slug = :slug)";

  //   $statement = $this->_dbHandle->prepare($query);
  //   $statement->bindParam('slug', $slug);
  //   $statement->execute();

  //   $product_ids = [];

  //   while($row = $statement->fetch(PDO::FETCH_ASSOC)) {

  //     $product_ids[] = $row['product_id'];
  //   }
    
  //   $dataSet[] = $this->getProducts($product_ids);
  //   // print_r($dataSet);


  //   return $dataSet;

  // }




