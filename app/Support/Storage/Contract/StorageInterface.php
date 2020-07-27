<?php

namespace App\Support\Storage\Contract;

interface StorageInterface {

    // GETS ITEM FROM STORAGE
    public function get($index);

    // ADD ELEMENT TO STORAGE
    public function set($index, $quantity);

    // GETS ALL ELEMENTS FROM STORAGE
    public function all();

    public function has($index);

    // CHECKS IF ELEMENTS EXISTS IN STORAGE
    public function exists($index);

    // DELETES ELEMENTS FROM STORAGE
    public function unset($index);

    // DELETES ALL ELEMENTS FROM STORAGE
    public function clear();

}