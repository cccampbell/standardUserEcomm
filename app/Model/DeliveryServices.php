<?php

namespace App\Model;

use PDO;
use App\Model\Model;
use App\Support\Exceptions\DeliveryServicesNotAvailable;

class DeliveryServices extends Model {

    protected   $id,
                $name,
                $code,
                $price;

    protected $fillable = [
        'name',
        'price',
        'country_id'
    ];

    public function __construct() {

        parent::__construct();

    }

    public static function withID($id) {

        $self = new self();
        $self->getById($id);
        return $self;

    }

    public static function getPostageWithIDAndPrice($id, $price) {
        $self = new self();
        // debug - change to getPostage -> takes in array of key value pairs of each param
        return $self->getPostageByIDAndPrice($id, $price);
    }

    public static function getWithCountryID($id) {

        $self = new self();
        return $self->getByCountryID($id);

    }

    public static function getWithCountryCode($code) {

        $self = new self();
        return $self->getByCountryCode($code);


    }

    public static function withRow(array $data) {

        $self = new self();
        $self->fill($data);
        return $self;

    }

    protected function getPostageByIDAndPrice($id, $price) {

        $query = "SELECT * FROM delivery_services WHERE id = :id and price = :price";

        $statement = $this->_dbHandle->prepare($query);

        $statement->execute([
            'id' => $id,
            'price' => $price
        ]);

        

        // check if returned any
        if($statement->rowCount() > 0) {

            $row = $statement->fetch();

            return [
                'id' => $row['id'],
                'name' => $row['name'],
                'price' => $row['price'],
                'country_id' => $row['country_id'],
            ];

            return $arr;

        } else {

            throw new DeliveryServicesNotAvailable();

        }

    }

    protected function getById($id) {

        $query = 'SELECT * FROM delivery_services WHERE id = :id';

        $statement = $this->_dbHandle->prepare($query);

        $statement->execute(array(
            'id' => $id
        ));

        $this->fill($statement->fetch(PDO::FETCH_ASSOC));

    }
    protected function getByCountryID($id) {

        $arr = [];

        $query = 'SELECT * FROM delivery_services WHERE country_id = :id';

        $statement = $this->_dbHandle->prepare($query);

        $statement->execute(array(
            'id' => $id
        ));


        // check if returned any
        if($statement->rowCount() > 0) {

            while($row = $statement->fetch()) {

                array_push($arr, [
                    'id' => $row['id'],
                    'name' => $row['name'],
                    'price' => $row['price'],
                    'country_id' => $row['country_id'],
                ]);

            }
            return $arr;

        } else {

            throw new DeliveryServicesNotAvailable();

        }
        

    }
    protected function getByCountryCode($code) {

        $arr = [];

        $query =    'SELECT delivery_services.id, delivery_services.name, delivery_services.price, delivery_services.country_id FROM delivery_services
                    INNER JOIN countries ON country_id = countries.id
                    WHERE countries.code = :code';

        $statement = $this->_dbHandle->prepare($query);

        $statement->execute(array(
            'code' => $code
        ));


        // check if returned any
        if($statement->rowCount() > 0) {

            return [
                'delivery_available' => TRUE,
                'country_id' => $statement->fetch()['country_id']
            ];

        } else {

            return NULL;

        }
        

    }
    public function fill(array $row) {

        $this->id = $row['id'];
        $this->name = $row['name'];
        $this->code = $row['code'];
        $this->price = $row['price'];

    }

    public function getId() {

        return $this->id;
    }

    public function getPrice() {

        return (float) $this->price;

    }
    

    private function sortDataForPDO(array $data) {


        $column = '(';
        $values = '(';
        $execute_array = [];

        // match key against fillable array
        foreach ($data as $key => $value) {

            if(in_array($key, $this->fillable)) {

                if(array_key_first($data) === $key) {

                    $column .= $key;
                    $values .= ':' . $key;

                } else {

                    $column .= ', ' . $key;
                    $values .= ', :' . $key;

                }

                // add key and value to execute_array
                $execute_array[':'.$key] = $data[$key];

            }
        }

        $column .= ')';
        $values .= ')';

        return [
            'column' => $column,
            'values' => $values,
            'execute' => $execute_array
        ];

    }


}