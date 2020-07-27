<?php

namespace App\Middleware;


// base middleware
class Middleware {

    protected $container;

    public function __construct($container) {
        $this->container = $container;
    }

}