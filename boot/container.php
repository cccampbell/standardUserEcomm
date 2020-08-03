<?php

use PDO;

use DI\Container;
use App\Model\App;
use App\Util\Util;
use App\Model\Entry;
use App\Model\Order;
use App\Model\Basket;
use App\Model\Database;

use Slim\Flash\Messages;
use App\Location\Location;

use App\Model\ProductData;
use DI\Bridge\Slim\Bridge;
use Slim\Factory\AppFactory;
use App\Model\AccountDataSet;
use App\Model\ProductDataSet;
use App\Validation\Validator;
use App\Model\CustomerDataSet;

use App\Authentication\AuthParser;
use App\Authentication\AuthFactory;
use App\Controllers\AuthController;
use App\Controllers\HomeController;
use App\Controllers\NextController;
use App\Controllers\OrderController;
use App\Authentication\ClaimsFactory;
use App\Controllers\BasketController;
use App\Authentication\Authentication;
use App\Controllers\AccountController;
use App\Controllers\ProductController;
use App\Payment\Gateways\StripeGateway;
use App\Support\Storage\SessionStorage;
use App\Authentication\Provider\AuthenticateUser;
use App\Authentication\Provider\FirebaseProvider;
use App\Support\Storage\Contract\StorageInterface;
use App\Authentication\Provider\MySQLAuthenticateUser;


$container = new DI\Container();


AppFactory::setContainer($container);

$container->set('settings', function () use ($env) {
    return [
        'app' => [
            'name' => $env['APP_NAME']
        ],

        'views' => [
            'cache' => $env['VIEW_CACHE_DISABLED'] === 'true' ? false : __DIR__ . '/../storage/views'
        ],
        'jwt' => [
            'expiry' => $env['JWT_TTL'],
            'secret' => $env['JWT_SECRET'],
            'algo' => $env['JWT_ALGO'],
        ]
    ];
});

$container->set('session_storage', function($container) {
    return new SessionStorage('basket');
});

$container->set('app', function($container) {
    return new App;
});

$container->set('validator', function () {
    return new Validator;
});

$container->set('product', function ($container) {
    return new ProductData;
});


$container->set('claimsFactory', function ($container) {
    return new ClaimsFactory(
        $container->get('settings')
    );
});

$container->set('jwtProvider', function ($container) {
    return new FirebaseProvider(
        $container->get('settings')
    );
});

$container->set('paymentGateway', function ($container) {
    return new StripeGateway;
});

$container->set('authFactory', function ($container) {
    return new AuthFactory(
        $container->get('claimsFactory'),
        $container->get('jwtProvider')
    );
});


$container->set('auth', function ($container) {

    return new Authentication(
        new MySQLAuthenticateUser,
        $container->get('authFactory'),
        new AuthParser($container->get('jwtProvider'))
    );
});

$container->set('entry', function ($container) {
    return new Entry;
});

$container->set('productDS', function ($container) {
    return new ProductDataSet;
});

$container->set('order', function ($container) {
    return new Order;
});

$container->set('flash', function ($container) {
    return new \Slim\Flash\Messages;
});

$container->set('customerDS', function ($container) {
    return new CustomerDataSet;
});

// UTIL 
$container->set('location', function ($container) {
    return new Location;
});

$container->set('util', function ($container) {
    return new Util;
});



// $container->set(ProductDataSet::class, function ($container) {
//     return new ProductDataSet;
// });

// $container->set(HomeController::class, function ($container) {
//     return new HomeController;
// });

// $container->set('account', function ($container) {
//     return new Account;
// });

// $container->set(AccountController::class, function ($container) {

//     return new AccountController(
//         $container->get('view')
//     );

// });

// $container->set(AuthController::class, function ($container) {
//     return new AuthController(
//         $container->get('view'),
//         $container->get('validator'),
//         $container->get(Entry::class)
//     );
// });

// $container->set(OrderController::class, function ($container) {
//     return new OrderController(
//         $container->get('view'),
//         $container->get('validator'),
//         $container->get(Basket::class),
//         $container->get(Order::class)
//     );
// });


$container->set('basket', function ($container) {
    
    return new Basket (
        $container->get(SessionStorage::class),
        $container->get(ProductDataSet::class)
    );

});

// $container->set(BasketController::class, function ($container) {

//     return new BasketController (
//         $container->get('view'),
//         $container->get(ProductDataSet::class),
//         $container->get(Basket::class),
//     );

// });
