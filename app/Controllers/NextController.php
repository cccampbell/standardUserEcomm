<?php

namespace App\Controllers;

use PDO;
use Slim\Views\Twig;
use App\Controllers\Controller;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class NextController extends Controller  {

    protected $view;

    public function __construct(Twig $view, $db) {

        $this->view = $view;

        $query = $db->query('SELECT * FROM products');

        $query->execute();

        print_r($query->fetchAll(PDO::FETCH_OBJ));

    }

    public function __invoke(Request $request, Response $response) {

        return $this->view->render($response, 'home.twig');

    }
    
}