<?php
namespace App\Middleware;


use App\Middleware\Middleware;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ProductQuantityInBaketChanged extends Middleware {


                            // req, req handler
    public function __invoke(ServerRequestInterface $request, RequestHandlerInterface $handler) {

        // debug - add method to basket to check if quantity changed

        $response = $handler->handle($request);

        try {

            $this->container->get('basket')->refresh();

        } catch (Exception $e) {

            $_SESSION['flash']['failed'] = 'A Product quantity in your basket has changed';

        }

        return $response;

    }

}