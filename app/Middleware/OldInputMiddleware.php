<?php
namespace App\Middleware;


use App\Middleware\Middleware;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class OldInputMiddleware extends Middleware {


                            // req, req handler
    public function __invoke(ServerRequestInterface $request, RequestHandlerInterface $handler) {

        $this->container->get('view')->offsetSet('oldFormInput', $_SESSION['oldFormInput']);
        $_SESSION['oldFormInput'] = $request->getParsedBody();

        

        // give response to next by creating response
        // assign res using handler handle the req
        $response = $handler->handle($request);
        return $response;

    }

}