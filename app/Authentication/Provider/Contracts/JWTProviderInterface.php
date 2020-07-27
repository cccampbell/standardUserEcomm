<?php 
namespace App\Authentication\Provider\Contracts;

interface JWTProviderInterface {


    public function encode(array $claims);
    public function decode($token);

}