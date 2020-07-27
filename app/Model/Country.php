<?php

namespace App\Model;

use PDO;
use App\Model\Model;

class Country extends Model {

    protected   $id,
                $name,
                $code;

    protected $fillable = [
        'name',
        'code'
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

        $query = 'SELECT * FROM countries WHERE id = :id';

        $statement = $this->_dbHandle->prepare($query);

        $statement->execute(array(
            'id' => $id
        ));

        $this->fill($statement->fetch(PDO::FETCH_ASSOC));

    }
    
    public function fill(array $row) {

        $this->id = $row['id'];
        $this->name = $row['name'];
        $this->code = $row['code'];

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


}