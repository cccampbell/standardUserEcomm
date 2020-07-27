<?php

namespace App\Controllers;

use Exception;
use Slim\Views\Twig;
use App\Model\Basket;
use App\Model\ProductDataSet;
use App\Controllers\Controller;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class BasketController extends Controller  {

    // protected   $view,
    //             $basket;

    // public function __construct(Twig $view, ProductDataSet $productDS, Basket $basket) {

    //     $this->view = $view;
    //     $this->productDS = $productDS;
    //     $this->basket = $basket;

    // }

    public function index(Request $request, Response $response) {

        // die(var_dump($this->container->get('basket')));

        try {

            $this->container->get('basket')->refresh();

        } catch(Exception $e) {
            

            $_SESSION['flash']['failed'] = $e->getMessage();

            return $response->withHeader('Location', '/basket')
                            ->withStatus(302);

        }

        

        $products = $this->container->get('basket')->all();

        return $this->container->get('view')->render($response, 'basket.twig', [
            'products' => $products,
        ]);

    }

    public function add(Request $request, Response $response, $args) {
        

        $product = $this->container->get('productDS')->getProductWithStockID($args['id']);

        if(!$product) {
            // debug - sort this out proper
            return $response->withHeader('Location', '/')
                            ->withStatus(302);
        }

        $product = $this->container->get('basket')->add($product, 1);


        return $this->container->get('view')->render($response, 'layouts/partials/basket_pane_product.twig', [
            'product' => $product
        ]);
        
    }

    public function remove(Request $request, Response $response, $args) {

        $this->container->get('basket')->remove($args['id']);

        return $this->container->get('view')->render($response, 'basket.twig');

    }
    
}