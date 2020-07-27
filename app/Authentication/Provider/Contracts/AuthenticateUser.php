<?php

namespace App\Authentication\Provider\Contracts;

interface AuthenticateUser {


    public function checkUser($email, $password);

    public function byId($id);


}