<?php
namespace App\Middleware;


use App\Middleware\Middleware;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class FlashMiddleware extends Middleware {


                            // req, req handler
    public function __invoke(ServerRequestInterface $request, RequestHandlerInterface $handler) {



        $this->container->get('view')->offsetSet('flash', $_SESSION['flash']);

        unset($_SESSION['flash']);

        // give response to next by creating response
        // assign res using handler handle the req
        $response = $handler->handle($request);
        return $response;

    }

}