<?php

namespace App\Authentication;

use App\Authentication\Provider\Contracts\JWTProviderInterface;

class AuthParser {

    protected $jwtProvider;

    public function __construct(JWTProviderInterface $jwtProvider)
    {
        $this->jwtProvider = $jwtProvider;
    }


    public function decode($token) {

        // decode jwt token
        return $this->jwtProvider->decode(
        //     // takes in extracted token
            $this->extractToken($token)
        );
        // return $this->extractToken($token);
    }

    protected function extractToken($token) {
        // extract to just jwt token
        if(preg_match('/^Bearer\s(\S+)$/', $token, $match)) {
            return $match[1];
        }
        return null;

    }

}