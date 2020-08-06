<?php

require_once('ProductData.php');
require_once('Database.php');

class ProductDataSet {
  protected $_dbHandle, $_dbInstance;

  //dataset constructor
  public function __construct() {
        $this->_dbInstance = Database::getInstance();
        $this->_dbHandle = $this->_dbInstance->getdbConnection();
  }

  public function getAllProducts() {
    $sqlQuery = 'SELECT distinct product FROM Stock';
    return $sqlQuery;
  }

  public function fetchAllProducts() {

    $sqlQuery = 'SELECT * FROM Products';

    //echo $sqlQuery; //helpful for debugging
    $statement = $this->_dbHandle->prepare($sqlQuery); //prepare PDO $statement
    $statement->execute();

    $dataSet = [];
    while($row = $statement->fetch()) {
      $dataSet[] = new ProductData($row);
    }
    return $dataSet;

  }

  public function fetchAllColours() {
    $sqlQuery = 'SELECT * FROM Colours';

    $statement = $this->_dbHandle->prepare($sqlQuery); //prepare PDO $statement
    $statement->execute();

    $dataSet = [];
    while($row = $statement->fetch()) {
      $dataSet[] = new ProductData($row);
    }
    return $dataSet;

  }

  public function fetchFirst() {

    $sqlQuery = 'SELECT * FROM Products WHERE ProductId = 1';

    echo $sqlQuery; //helpful for debugging
    $statement = $this->_dbHandle->prepare($sqlQuery); //prepare PDO $statement
    $statement->execute();

    $dataSet = [];
    while($row = $statement->fetch()) {
      $dataSet[] = new ProductData($row);
    }
    return $dataSet;

  }
  //get product via ID
  public function FetchProduct($id) {

    $sqlQuery = "SELECT * from Products WHERE ProductId = :id" ;

    //echo $sqlQuery; //helpful for debugging
    $statement = $this->_dbHandle->prepare($sqlQuery);
    $statement->bindParam(':id', $id); //prepare PDO $statement
    $statement->execute();

    $dataSet = new ProductData($statement->fetch());
    return $dataSet;

  }

  // GETS STOCK QUANTITY OF SPECIFIED PRODUCT
  public function getProductQuantity($id) {
    //query
    $query = "SELECT stock FROM Stock WHERE product = :id";
    //set up
    $statement = $this->_dbHandle->prepare($query);
    $statement->bindParam(':id',$id); //PREPARES STATEMENT
    $statement->execute();
    // $dataSet = [];
    $total = 0;
    while($row = $statement->fetch()) {
      // $dataSet[] = $row;
      $total += $row['stock'];
    }
    return $total;
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

  public function FetchAllCategories() {

    $query = 'SELECT id, name, is_category FROM Categories';

    $statement = $this->_dbHandle->query($query);
    $statement->execute();

    // SEPERATE ARRAYS FOR BOTH CATEGORY AND COLLECTION
    $cat = [];
    $coll = [];

    while($row = $statement->fetch()) {

      if($row['is_category']) {
        array_push($cat,$row);
      }
      else {
        array_push($coll,$row);
      }
    }

    $dataSet = [
      'category' => $cat,
      'collection' => $coll,
    ];

    return $dataSet;


  }


  public function fetchCollection($id) {
    $query = 'SELECT * FROM Categories WHERE id = :id';

    $statement = $this->_dbHandle->prepare($query);
    $statement->bindParam(':id', $id);
    $statement->execute();

    return $statement->fetch();

  }

  public function FetchCollectionProducts($id) {

    $query1 = 'SELECT ProductID FROM ProductCategoryRelation WHERE CategoryID = :id';

    $statement = $this->_dbHandle->prepare($query1);
    $statement->bindParam(':id', $id);
    $statement->execute();

    $product_ids = [];

    while($productid = $statement->fetch()) {
      $product_ids[] = $productid['ProductID'];
    }


    $dataSet = [];

    foreach($product_ids as $prod) {
      // echo $prod;
      $this_prod = $this->FetchProduct($prod);
      $dataSet[] = $this_prod;
    }


    return $dataSet;

  }

  // GET QUANTITY OF EACH SIZE
  public function getProductSizeQuantity($id) {

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

    // var_dump($colourArr);
    return $colourArr;

  }

  // FETCH SELECTED COLLECTION/CATEGORY // PARAMETER IS CAT/COLL ID
  public function fetchSelectedCategory($id) {

    $query1 = 'SELECT ProductID from ProductCategoryRelation WHERE CategoryID = :id';

    $statement = $this->_dbHandle->prepare($query1);
    $statement->bindParam(':id', $id);
    $statement->execute();

    while($row = $statement->fetch()) {
      $dataSet[] = $this->fetchProduct($row[0]);
    }

  } 

  public function fetchFilters($ids) {

    // SET NUMBER OF PARAMS TO BE SET FOR EACH PRODUCT_ID
    $param  = str_repeat('?,', count($ids) - 1) . '?';

    $sql = "SELECT ProductID FROM ProductCategoryRelation WHERE CategoryID IN ($param)";
    
    $statement  = $this->_dbHandle->prepare($sql);
    // SET NUMBER OF PARAMS TO BE SET FOR EACH PRODUCT_ID
    $types = str_repeat('s', count($ids));
    //BIND PARAMS IN QUERY(? = S) TO THE ARRAY OF PRODUCT_ID (...$ARRAY = {[1][2] === 1,2})
    $statement->bindParam($types, ...$ids);
    $statement->execute($ids);
    
    $dataSet = [];

    while($row = $statement->fetch()) {
      $dataSet[] = $this->FetchProduct($row['ProductID']);
    }
    return $dataSet;

  }

  //$filter - array of filters [x,y] && $sort - string 'high' || 'low
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

      $sql = "SELECT * from Products WHERE ProductId IN (SELECT ProductID FROM ProductCategoryRelation WHERE CategoryID in ($param))" . $sorting;

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
      $dataSet[] = $this->FetchProduct($row['ProductID']);
    }
    return $dataSet;

  }

  // public function SearchProducts($query) {
  //   $sqlQuery = "SELECT * FROM Products WHERE Prod_name LIKE = :query";
  //
  //   $statement = $this->_dbHandle->prepare($sqlQuery);
  //   $statement->bindParam(':query', $query);
  //   $statement->execute();
  //
  //   $dataSet = [];
  //   while($row = statement->fetch()) {
  //     $dataSet[] = new ProductData($row);
  //   }
  //   return $dataSet;
  // }

  public function fetchAllProductNames() {

        $sqlQuery = 'SELECT Prod_name, ProductId FROM Products';

        //debugging purposes
        //echo $sqlQuery;

        $statement = $this->_dbHandle->prepare($sqlQuery);
        $statement->execute();

        $dataSet = [];
        while($row = $statement->fetch()) {
          $dataSet[$row['Prod_name']] = $row['ProductId'];
        }
        return $dataSet;

    }
}
