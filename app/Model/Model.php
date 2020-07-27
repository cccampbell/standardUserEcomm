<?php
namespace App\Model;

use App\Model\Database;

require_once('Database.php');

class Model {

    protected $_dbHandle, $_dbInstance;

    //dataset constructor
    public function __construct() {

            $this->_dbInstance = Database::getInstance();
            $this->_dbHandle = $this->_dbInstance->getdbConnection();
    }

    
    

}