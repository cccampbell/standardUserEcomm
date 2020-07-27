<?php
namespace App\Middleware;


use Exception;
use App\Middleware\Middleware;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;

class CheckUserLoggedIn extends Middleware {

                            // req, req handler
    public function __invoke(Request $request, RequestHandlerInterface $handler) {

        $response = $handler->handle($request);

        // DOES NOT HAVE AUTH HEADER -> MEANS NOT SIGNED IN 
        if(!$token = $this->getAuthorisationHeader($request)) {
            // todo write exception
            // var_dump('here');
            // tell not signed in because no authorizatoin in header

            return $response;
        }

        try{
            
            $a = $this->container->get('auth')->AuthenticateViaHeader($token);
        
        } catch (Exception $e) {

            // HAS TOKEN BUT REASON FOR NOT WORKING -> SHOW REASON 

            // make exception
            $_SESSION['flash']['failed'] = $e->getMessage();
            return $response->withStatus(401);
        }
        

        // die(print_r($a->getUser()));
        // if(isset($_SESSION['user'])) {
            
        //     $this->container->get('view')->offsetSet('user', $_SESSION['user']);
        
        // } else {
        
        //     $this->container->get('view')->offsetUnset('user');
        
        // }
        // var_dump($this->container->get('auth')->loggedIn());
        // give response to next by creating response
        // assign res using handler handle the req
        return $response;
        // return $response
        // ->withHeader('Access-Control-Allow-Origin', '*')
        // ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
        // ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');

    }

    protected function getAuthorisationHeader(Request $request) {

        // var_dump($request->getHeaders());
        if(!list($header) = $request->getHeader('Authorization', false)) {
            return false;
        }
        return $header;

    }

}