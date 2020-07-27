<?php
namespace App\Authentication;

use Carbon\Carbon;
use DI\Container;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Authentication\Exceptions\UnknownClaim;

class ClaimsFactory {

    protected $defaultClaims = [
        'iss',
        'iat',
        'nbf',
        'jwi',
        'exp'        
    ];

    public function __construct(array $settings) {

        $this->settings = $settings;
        // print_r($this->container);

    }

    public function getDefaultClaims() {

        return $this->defaultClaims;

    }


    protected function iss() {

        return "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

    }

    protected function iat() {

        return Carbon::now()->getTimestamp();

    }

    protected function nbf() {

        return Carbon::now()->getTimestamp();

    }

    protected function jwi() {

        return bin2hex(random_bytes(32));

    }

    protected function exp() {

        return Carbon::now()->addMinutes($this->settings['jwt']['expiry'])->getTimestamp();

    }

    public function getClaim($claim) {

        // if method not there
        if(!method_exists($this, $claim)) {

            throw UnknownClaim;
            return;
        }

        return $this->{$claim}();

    }

}