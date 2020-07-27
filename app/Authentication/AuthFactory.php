<?php

namespace App\Authentication;

use Carbon\Carbon;
use Firebase\JWT\JWT;
use App\Authentication\ClaimsFactory;
use App\Authentication\Provider\Contracts\JWTProviderInterface;


class AuthFactory {
    protected   $claims,
                $claimsFactory,
                $jwtProvider;

    public function __construct(ClaimsFactory $claimsFactory, JWTProviderInterface $jwtProvider) {
        $this->claimsFactory = $claimsFactory;
        $this->jwtProvider = $jwtProvider;

    }
    

    public function make() {

        // build up the array
        // use claims from withClaims and default claims from claimFactory

        $defaultClaims = [];

        foreach($this->claimsFactory->getDefaultClaims() as $claim) {

            try {
                $defaultClaims[$claim] = $this->claimsFactory->getClaim($claim);
            } catch( UnknownClaim $e ) {
                $e->getMessage();
            }

        }

        return array_merge($this->claims, $defaultClaims);


    }

    public function encode(array $claims) {
        // call interface method 
        // use firebase jwt
        return $this->jwtProvider->encode($claims);

    }

    public function withClaims(array $claims) {

        $this->claims = $claims;

        return $this;

    }


}