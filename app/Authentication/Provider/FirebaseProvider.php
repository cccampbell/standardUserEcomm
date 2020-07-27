<?php
namespace App\Authentication\Provider;

use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\BeforeValidException;
use Firebase\JWT\SignatureInvalidException;
use App\Authentication\Provider\Contracts\JWTProviderInterface;

class FirebaseProvider implements JWTProviderInterface {

    protected $settings;

    public function __construct(array $settings) {
        $this->settings = $settings;

    }

    public function encode(array $claims) {

        return JWT::encode($claims, $this->settings['jwt']['secret'], $this->settings['jwt']['algo']);

    }

    public function decode($token) {


        return JWT::decode(
            $token,
            $this->settings['jwt']['secret'],
            array($this->settings['jwt']['algo'])
        );

    }


}