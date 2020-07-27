<?php

namespace App\Controllers;

use Slim\Views\Twig;
use App\Controllers\Controller;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class AccountController extends Controller {


    public function home(Request $request, Response $response) {
        
        return $this->container->get('view')->render($response, 'account.twig');

    }
    
}