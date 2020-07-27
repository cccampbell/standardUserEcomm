<?php
namespace App\Middleware;


use App\Middleware\Middleware;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class RedirectGuestMiddleware extends Middleware {


                            // req, req handler
    public function __invoke(ServerRequestInterface $request, RequestHandlerInterface $handler) {


        $response = $handler->handle($request);

        if( $this->container->get('view')->offsetGet('user') !== null ) {

            return $response;

        } else {

            $_SESSION['flash']['failed'] = 'You have to be signed in to access that';

            return $response
            ->withHeader('Location', '/signin')
            ->withStatus(401);

        }

    }

}