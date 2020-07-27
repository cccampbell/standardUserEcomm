<?php

namespace App\Model;

use PDO;
use App\Model\Database;
use App\Model\ProductDataSet;

require_once('Database.php');

class App {
  protected $_dbHandle, $_dbInstance;

  //dataset constructor
  public function __construct() {
        $this->_dbInstance = Database::getInstance();
        
        $this->_dbHandle = $this->_dbInstance->getdbConnection();
        
  }

  public function getAllCategories() {

    $query = "(SELECT categories.id, categories.name, categories.slug, COUNT(product_id) as count, '1' as is_category
				        FROM product_category
                INNER JOIN categories ON category_id = categories.id
                GROUP BY category_id
                )
                UNION
                (
                SELECT collections.id, collections.name, collections.slug, COUNT(product_id) as count, '0' as is_category
                FROM collection_products
                INNER JOIN collections ON collection_id = collections.id
                GROUP BY collection_id)";

    $statement = $this->_dbHandle->query($query);
    $statement->execute();

    // SEPERATE ARRAYS FOR BOTH CATEGORY AND COLLECTION
    $cat = [];
    $coll = [];

    while($row = $statement->fetch(PDO::FETCH_ASSOC)) {

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

  public function getAllCountries() {

    $query = 'SELECT * FROM countries';

    $statement = $this->_dbHandle->prepare($query);

    $statement->execute();

    $countries = [];

    while($row = $statement->fetch()) {

      array_push($countries, $row['name'] = ['id' => $row['id'], 'name' => $row['name'], 'code' => $row['code']]);

    }

    return $countries;

  }


}