<?php

namespace App\Model;

use PDO;
use App\Model\Model;

class Collection extends Model {

    protected   $id,
                $name,
                $slug,
                $img;

    protected $fillable = [
        'name',
        'slug',
        'imgs',
    ];

    public function __construct() {

        parent::__construct();

    }

    public static function withID($id) {

        $self = new self();
        $self->getById($id);
        return $self;

    }

    public static function getNewCollection() {

        $self = new self();
        $self->findNewCollection();
        return $self;

    }

    public static function withRow(array $data) {

        $self = new self();
        $self->fill($data);
        return $self;

    }

    protected function getById($id) {

        $query = 'SELECT * FROM collections WHERE id = :id';

        $statement = $this->_dbHandle->prepare($query);

        $statement->execute(array(
            'id' => $id
        ));

        $this->fill($statement->fetch(PDO::FETCH_ASSOC));

    }

    protected function findNewCollection() {

        $query = 'SELECT * FROM collections ORDER BY updated_at DESC';

        $statement = $this->_dbHandle->prepare($query);

        $statement->execute();

        $this->fill($statement->fetch(PDO::FETCH_ASSOC));

    }
    
    public function fill(array $row) {

        $this->id = $row['id'];
        $this->name = $row['name'];
        $this->slug = $row['slug'];
        $this->img = $row['img'];

    }

    public function getId() {

        return $this->id;
    }

    public function getName() {

        return $this->name;
    }

    public function getSlug() {

        return $this->slug;
    }

    public function getImg() {

        return $this->img;
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