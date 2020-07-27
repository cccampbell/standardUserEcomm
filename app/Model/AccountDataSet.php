<?php
namespace App\Model;

use PDO;
use App\Model\Database;
use App\Validation\Exceptions\EmailUnavailableException;


require_once('AccountData.php');
require_once('Database.php');

class AccountDataSet {

  protected $_dbHandle, $_dbInstance;

  //dataset constructor
  public function __construct() {

        $this->_dbInstance = Database::getInstance();
        $this->_dbHandle = $this->_dbInstance->getdbConnection();
  }

  public function cleanInput($array) {

    foreach ($array as $el) {
            $el = htmlspecialchars($el);
    }

    return $array;

  }

  public function populateUser($data) {


    // 


  }
  

}
