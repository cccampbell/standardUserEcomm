<?php

use App\Model\App;
use Slim\Csrf\Guard;
use Slim\Views\Twig;
use App\Model\Basket;
use App\Model\Account;
use Slim\Psr7\Response;

use Slim\Flash\Messages;
use App\Model\ProductData;
use App\Model\ProductDataSet;
use App\Validation\Validator;

use Slim\Views\TwigMiddleware;
use App\Middleware\AuthMiddleware;
use App\Middleware\ClientLocation;
use App\Middleware\FlashMiddleware;
use App\TwigExtension\CsrfExtension;
use Slim\Middleware\ErrorMiddleware;
use App\Middleware\CheckUserLoggedIn;
use App\Middleware\OldInputMiddleware;
use App\Support\Storage\SessionStorage;
use Slim\Exception\HttpNotFoundException;
use App\Middleware\ValidationErrorsMiddleware;
use App\Support\Storage\Contract\StorageInterface;

$errorMiddleware = new ErrorMiddleware(
    $app->getCallableResolver(),
    $app->getResponseFactory(),
    true,
    false,
    false
);

// add error middleware to app
$app->add($errorMiddleware);


// set view in container
$container->set('view', function () {
    return Twig::create('views', [
        'cache' => false,
        'globals' => [
            // 'basket' => Basket::class,
            // 'app' => App::class,
            'checkLocation' => 'here i am'
        ]
    ]);
});


// $container->set('csrf', function () use ($app) {
//     return new Guard($app->getResponseFactory());
// });

// $app->add('csrf');


// $container->set('user', function ($container) {

//     return $container->get('account');

// });


// GLOBALS IN 'VIEW' SO CAN BE ACCESSED ON WHOLE APP
$container->get('view')->offsetSet('basket', $container->get('basket'));
// $container->get('view')->offsetSet('user', $container->get('user'));
$container->get('view')->offsetSet('app', $container->get('app'));
$container->get('view')->offsetSet('flash',  new Messages());
// $container->get('view')->offsetSet('location', $location);


$app->add(new ValidationErrorsMiddleware($container));

$app->add(new OldInputMiddleware($container));
$app->add(new ClientLocation($container));
$app->add(new CheckUserLoggedIn($container));

$app->add(new FlashMiddleware($container));



// CUSTOM ERROR PAGE FOR PAGE NOT FOUND
$errorMiddleware->setErrorHandler(HttpNotFoundException::class, function ($request, $exception) use ($container) {
            
            // commented out should work but being a dick (error 500)
            
    // // bring container into scope 
    // // create instance of Response to return on view
    // $response = new Response();

    // return $container->get('view')->render($response->withStatus(404), 'errors/404.twig');

    die('Could not be found');

});

// add Twig middleware
$app->add(TwigMiddleware::createFromContainer($app));



// $container->get('view')->addExtension(
//     new CsrfExtension($container->get('csrf'))
// );

// /////////
//      MIDDLEWARE
// /////////

    // in each middleware you have to return a response

    // can use before middleware to:
            // check if user logged in 
            // authentication

    //                          request, request handler
    // $beforeMiddleware = function ($request, $handler) {
    //     $response = $handler->handle($request);

    //     // var_dump("before");

    //     $signed_in = false;

    //     !$signed_in ? die('not signed in') : die('Signed in');

    //     return $response;

    // };

    // CLOSURE - ANONYMOUS FUNCTION
    // $app->add($beforeMiddleware);


// $app->add(new AuthMiddleware());
