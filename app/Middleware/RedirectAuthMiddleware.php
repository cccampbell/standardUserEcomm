<?php
namespace App\Middleware;


use App\Middleware\Middleware;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class RedirectAuthMiddleware extends Middleware {


                            // req, req handler
    public function __invoke(ServerRequestInterface $request, RequestHandlerInterface $handler) {


        $response = $handler->handle($request);

        if( $this->container->get('view')->offsetGet('user') !== null ) {

            $_SESSION['flash']['failed'] = 'You are signed in you cannot access this';

            return $response
            ->withHeader('Location', '/')
            ->withStatus(401);

        } else {

            return $response;

        }

    }

}