<?php

namespace App\Model;

use PDO;
use App\Model\Model;

class Orders extends Model {

    protected   $id,
                $hash_id,
                $total,
                $paid,
                $address_id,
                $customer_id,
                $payment_method_id,
                $delivery_service_id;

    protected $fillable = [
        'hash_id',
        'total',
        'paid',
        'address_id',
        'customer_id',
        'payment_method_id',
        'delivery_service_id'
    ];

    public function __construct() {

        parent::__construct();

    }

    public static function withID($id) {

        $self = new self();
        $self->getById($id);
        return $self;

    }

    public static function withRow(array $data) {

        $self = new self();
        $self->fill($data);
        return $self;

    }

    protected function getById($id) {

        $query = 'SELECT * FROM orders WHERE id = :id';

        $statement = $this->_dbHandle->prepare($query);

        $statement->execute(array(
            'id' => $id
        ));

        $this->fill($statement->fetch(PDO::FETCH_ASSOC));

    }
    
    public function fill(array $row) {

        $this->id = $row['id'];
        $this->hash_id = $row['hash_id'];
        $this->total = $row['total'];
        $this->paid = $row['paid'];
        $this->address_id = $row['address_id'];
        $this->customer_id = $row['customer_id'];
        $this->payment_method_id = $row['payment_method_id'];
        $this->delivery_service_id = $row['delivery_service_id'];

    }

    public function create(Array $array) {

        $query_ready = $this->sortDataForPDO($array);

        $query = 'INSERT INTO orders '. $query_ready['column'] .' VALUES ' . $query_ready['values'];

        $statement = $this->_dbHandle->prepare($query);

        $statement->execute($query_ready['execute']);

        // call method with data to get just created to make sure its in db - returns obj
        return $this->getOrder($array['hash_id'], $array['payment_method_id']);

    }
    

    public function getId() {

        return $this->id;
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

    public function update($field, $value) {

        $query = 'UPDATE orders SET ' . $field . ' = :field WHERE id = :id';

        $statement = $this->_dbHandle->prepare($query);

        $statement->execute(array(
        'field' => $value,
        'id' => $this->getId()
        ));

        return self::withID($this->getId());

    }

    protected function getOrder($hash_id, $payment_method_id) {

        $query = 'SELECT * FROM orders WHERE `hash_id` = :hash_id AND payment_method_id = :payment_method_id';

        $statement = $this->_dbHandle->prepare($query);

        $statement->execute(array(
            'hash_id' => $hash_id,
            'payment_method_id' => $payment_method_id
        ));

        return self::withRow( $statement->fetch(PDO::FETCH_ASSOC) );

    }

    public function getTotal() {

        return $this->total;

    }

    public function getHash() {

        return $this->hash_id;

    }


}