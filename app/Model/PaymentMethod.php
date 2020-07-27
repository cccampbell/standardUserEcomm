<?php

namespace App\Model;

use PDO;
use App\Model\Model;

class PaymentMethod extends Model {

    protected   $id,
                $customer_id,
                $card_type,
                $last_four,
                $default_card,
                $provider_id;

    protected $fillable = [
        'customer_id',
        'card_type',
        'last_four',
        'default_card',
        'provider_id'
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

        $query = 'SELECT * FROM payment_methods WHERE id = :id';

        $statement = $this->_dbHandle->prepare($query);

        $statement->execute(array(
            'id' => $id
        ));

        $this->fill($statement->fetch(PDO::FETCH_ASSOC));

    }
    
    public function fill(array $row) {

        $this->id = $row['id'];
        $this->customer_id = $row['customer_id'];
        $this->card_type = $row['card_type'];
        $this->last_four = $row['last_four'];
        $this->default_card = $row['default_card'];
        $this->provider_id = $row['provider_id'];

    }

    public function create(Array $array) {

        // change $execute_array['default'] value from string to bool
        $array['default_card'] === TRUE ? $array['default_card'] = 1 : $array['default_card'] = 0;

        $query_ready = $this->sortDataForPDO($array);

        $query = 'INSERT INTO payment_methods '. $query_ready['column'] .' VALUES ' . $query_ready['values'];

        $statement = $this->_dbHandle->prepare($query);

        $statement->execute($query_ready['execute']);

        // die(var_dump($statement->errorInfo()));

        // call method with data to get just created to make sure its in db - returns obj
        return $this->getPaymentMethod($array['customer_id'], $array['provider_id']);

    }

    public function getId() {

        return $this->id;
    }

    public function getProviderID() {

        return $this->provider_id;

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

    protected function getPaymentMethod($customer_id, $provider_id) {

        $query = 'SELECT * FROM payment_methods WHERE customer_id = :customer_id AND provider_id = :provider_id';

        $statement = $this->_dbHandle->prepare($query);

        $statement->execute(array(
            'customer_id' => $customer_id,
            'provider_id' => $provider_id
        ));

        return self::withRow( $statement->fetch(PDO::FETCH_ASSOC) );

    }


}