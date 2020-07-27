<?php

namespace App\Controllers;


use App\Controllers\Controller;
use Psr\Http\Message\{
    ServerRequestInterface as Request,
    ResponseInterface as Response
};
use Slim\Views\Twig;
use App\Model\Collection;

class HomeController extends Controller {

    // protected $view;


    public function index(Request $request, Response $response) {

        $collection = Collection::getNewCollection();

        // var_dump($this->container->get('user')->say());

        return $this->container->get('view')->render($response, 'home.twig', [
            'collection' => $collection
        ]);

    }

    public function loader(Request $request, Response $response) {

        // var_dump($this->container->get('user')->say());

        return $this->container->get('view')->render($response, 'loader.twig');

    }
    
}