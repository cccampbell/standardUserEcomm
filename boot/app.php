<?php

use DI\Container;
use Stripe\Stripe;
use Slim\Factory\AppFactory;

session_start();

require_once __DIR__ . '/../vendor/autoload.php';

$env = include(__DIR__ . '/../env.php');



require 'container.php';


// create application
$app = AppFactory::create();

Stripe::setApiKey($env['SECRET_KEY']);

// $errorMiddleware = $app->addErrorMiddleware(true, true, true, $logger);
// $app->setBasePath("/work/public/");

require 'middleware.php';

require '../routes/web.php';
