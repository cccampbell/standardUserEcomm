<?php

use Slim\Views\Twig;

use App\Controllers\AuthController;
use App\Controllers\HomeController;
use App\Controllers\NextController;
use App\Controllers\OrderController;
use App\Controllers\BasketController;
use Slim\Routing\RouteCollectorProxy;
use App\Controllers\AccountController;
use App\Controllers\ProductController;
use App\Middleware\RedirectAuthMiddleware;
use App\Middleware\RedirectGuestMiddleware;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;



$app->get('/', HomeController::class . ':index' )->setName('home');

// /////
//      PRODUCTS
// /////
// v2
$app->get('/products/{slug}', ProductController::class . ':show')->setName('product.show');

//  all products page
$app->get('/products', ProductController::class . ':showAllInStock')->setName('product.showAll');

//  search products
$app->get('/products/search/{str}', ProductController::class . ':search')->setName('products.search');

$app->get('/products/{type}/{slug}', ProductController::class . ':showTypeProducts')->setName('type.show');

// filter products 
$app->get('/products/filter/{params:.*}[/sort/{sort}]', ProductController::class . ':filterProducts')->setName('products.filter');

// filter categories
$app->get('/products/category/{slug}/filter/{params:.*}', ProductController::class . ':filterCategory')->setName('category.filter');

$app->get('/products/collection/{slug}/filter/{params:.*}', ProductController::class . ':filterCollection')->setName('category.filter');

// /////
//      BASKET
// /////
//  basket home page
$app->get('/basket', BasketController::class . ':index')->setName('basket');

// add to basket
// $app->post('/basket/add/{slug}', BasketController::class . ':add')->setName('basket.add');
$app->get('/basket/add/{id}', BasketController::class . ':add')->setName('basket.add');

// remove from basket
$app->get('/basket/remove/{id}', BasketController::class . ':remove')->setName('basket.remove');

$app->get('/basket/add/{id}{slug}{quantity}', BasketController::class . ':update')->setName('basket.update');


$app->get('/signup', AuthController::class . ':signup' )
->add(new RedirectAuthMiddleware($container))
->setName('auth.signup');

$app->post('/signup', AuthController::class . ':postSignup' )
->add(new RedirectAuthMiddleware($container));

$app->get('/signin', AuthController::class . ':signin' )
->add(new RedirectAuthMiddleware($container))
->setName('auth.signin');

$app->post('/signin', AuthController::class . ':postSignin' )
->add(new RedirectAuthMiddleware($container));



$app->get('/signout', AuthController::class . ':signout')
->add(new RedirectGuestMiddleware($container))
->setName('auth.signout');



// 
// ACCOUNT
// 

$app->get('/account', AccountController::class . ':home')
->add(new RedirectGuestMiddleware($container))
->setName('account.home');
// ->add(new AuthRedirect());
// $app->get('/signin', function (Request $request, Response $response) {

//     return $this->get('view')->render($response, 'auth/signin.twig');

// })->setName('auth.signin');

// 
// CHECKOUT
// 

$app->get('/basket/checkout', OrderController::class . ':index')->setName('basket.checkout');

$app->post('/basket/checkout', OrderController::class . ':postOrderDetails')->setName('basket.checkout');

$app->get('/basket/checkout/get_delivery_services/{id}', OrderController::class . ':getCountryDeliveryService')->setName('basket.getDeliveryServices');

$app->get('/basket/checkout/get_postage_info/{id}/{price}', OrderController::class . ':getPostageInfo')->setName('basket.getPostageInfo');

$app->get('/basket/payment/{hash}_{id}', OrderController::class . ':payment')->setName('basket.payment');


$app->get('/spinner', HomeController::class . ':loader')->setName('loader');

$app->get('/basket/checkout/confirmation', OrderController::class . ':confirmation')->setName('confirmation');

// $app->options('/{routes:.+}', function ($request, $response, $args) {
//     return $response;
// });

// $app->add(function ($request, $handler) {
//     $response = $handler->handle($request);
//     return $response
//             ->withHeader('Access-Control-Allow-Credentials', 'true')
//             ->withHeader('Access-Control-Allow-Origin', '*')
//             ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
//             ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
// });

// /////
//  STEP BEFORE BIN, LAST CHECKS TO SEE IF IN USE ELSEWHERE IN APP
// /////































// $app->get('/', function(Request $request, Response $response) {

//     $data = [
//         'hello',
//         'there',
//         'goodbye'
//     ];


//     return $this->get('view')->render($response, 'home.twig', [
//         'data' => $data,
//     ]);
// })->setName('home');


// $app->get('/about', function(Request $request, Response $response) {

//     return $this->get('view')->render($response, 'about.twig');

// })->setName('about');


// $app->get('/contact', function (Request $request, Response $response) {

//     return $this->get('view')->render($response, 'contact.twig');


// })->setName('contact');


// $app->post('/contact', function (Request $request, Response $response) {

//     // give all info parsed from boyd i.e json, form data etc
//     $data = $request->getParsedBody();

//     $response->getBody()->write($data['name']);

//     return $response;

// });

// $app->get('/error', function (Request $request, Response $response) use ($app) {

//     return $response
//         ->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('help'))
//         ->withStatus(301);

// });

// $app->get('/help', function (Request $request, Response $response) {

//     return $this
//         ->get('view')
//         ->render($response, 'help.twig');
// })
// // setName so that if url changes you dont have to go debug later on
// ->setName('help');


// $app->group('/user/{user_id}', function(RouteCollectorProxy $group) {

//     $group->get('', function (Request $request, Response $response, $args) {

//         return $this->get('view')->render($response, 'user.twig', [
//             'user_id' => $args['user_id'],
//         ]);

//     })->setName('profile');

//     $group->get('/posts/{post_id}', function (Request $request, Response $response, $args) {

//         return $this->get('view')->render($response, 'posts.twig', [
//             'user_id' => $args['user_id'],
//             'post_id' => $args['post_id'],
//         ]);

//     })->setName('users_posts');

// });

// $app->get('/json', function (Request $request, Response $response) {

//     $data = [
//         ['name' => 'phil', 'email' => 'phil@bill.com'],
//         ['name' => 'bill', 'email' => 'bill@phil.com'],
//     ];

//     $response->getBody()->write(json_encode($data));

//     return $response
//         ->withHeader('Content-Type', 'application/json');

// });