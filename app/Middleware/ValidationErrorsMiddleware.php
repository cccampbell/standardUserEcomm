<?php
namespace App\Middleware;


use App\Middleware\Middleware;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ValidationErrorsMiddleware extends Middleware {

    // ----> comes in 
    //      (core)
    // <---- goes out
                            // req, req handler
    public function __invoke(ServerRequestInterface $request, RequestHandlerInterface $handler) {


        $this->container->get('view')->offsetSet('errors', [
            'validate_errors' => $_SESSION['validate_errors']
        ]);
        
        unset($_SESSION['validate_errors']);

        // give response to next by creating response
        // assign res using handler handle the req
        $response = $handler->handle($request);
        return $response;

    }

}