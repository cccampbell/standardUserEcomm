<?php 

namespace App\Authentication;

use App\Authentication\AuthParser;
use App\Authentication\AuthFactory;
use App\Authentication\Contracts\JwtIdentifier;
use App\Authentication\Provider\Contracts\AuthenticateUser;



class Authentication {


    protected   $auth,
                $factory,
                $authParser,
                $user = null;

    public function __construct(AuthenticateUser $auth, AuthFactory $factory, AuthParser $authParser) {
        
        $this->auth = $auth;
        $this->factory = $factory;
        $this->authParser = $authParser;

    }


    public function check($email, $password) {

        
        // if no user
        if(!$user = $this->auth->checkUser($email, $password)) {
            return false;
        }
        return $this->fromSubject($user);

    }

    public function AuthenticateViaHeader($token) {
        
        $this->user = $this->auth->byId($this->authParser->decode($token)->sub);
        return $this->getUser();
    }
    public function getUser() {

        return $this->user;
    }

    public function loggedIn() {
        
        return $this->user === null ? FALSE : TRUE;  

    }

    // create a factory to build up the Jwt 

    protected function fromSubject(JwtIdentifier $subject) {

        return $this->factory->encode($this->makePayload($subject));

    }

    protected function makePayload(JwtIdentifier $subject) {

        // factory
        return $this->factory->withClaims(
            // id from user
            $this->getClaimsForSubject($subject)
        )->make();

    }

    protected function getClaimsForSubject(JwtIdentifier $subject) {

        return [
            'sub' => $subject->JwtIdentifier()
        ];

    }

}