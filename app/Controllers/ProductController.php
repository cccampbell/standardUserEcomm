<?php

namespace App\Controllers;

use PDO;
use Slim\Views\Twig;
use App\Model\ProductDataSet;
use App\Controllers\Controller;
use Slim\Exception\HttpNotFoundException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ProductController extends Controller  {

    protected 
            $view,
            $db,
            $productDS;


    // public function __construct(ProductDataSet $productDS) {

    //     $this->productDS = $productDS;

    // }

    public function show(Request $request, Response $response, $args) {

        $products = $this->container->get('productDS')->getAllGarmData( $args['slug'] );

        // die(print_r($products));


        return $this->container->get('view')->render($response, 'product.twig', [
            'products' => $products,
            'slug' => $args['slug']
        ]);

    }

    public function showAllInStock(Request $request, Response $response) {

        $products = $this->container->get('productDS')->getInStockProducts();

        // print_r($products);

        return $this->container->get('view')->render($response, 'products.twig', [
            'products' => $products,
            'count' => count($products)
        ]);

    }

    public function showTypeProducts(Request $request, Response $response, $args) {

        $products = $this->container->get('productDS')->getTypeProducts($args['type'], $args['slug']);



        return $this->container->get('view')->render($response, 'products.twig', [
            'products' => $products[0],
            'args' => $args,
            'count' => count($products[0])
        ]);

    } 

    public function filterCategory(Request $request, Response $response, $args) {

        $filters = explode('/', $args['params']);

        // print_r($filters);


        $products = $this->container->get('productDS')->filterCategory($args['slug'], $filters);


        return $this->view->render($response, 'products.twig', [
            'products' => $products,
            'args' => $args,
            'count' => count($products),
            'filters' => $filters
        ]);

    }

    public function filterCollection(Request $request, Response $response, $args) {

        $filters = explode('/', $args['params']);

        $filterInArray = explode('|', $filters);

        $products = $this->container->get('productDS')->filterCollection($args['slug'], $filters);


        return $this->view->render($response, 'products.twig', [
            'products' => $products,
            'args' => $args,
            'count' => count($products),
            'filters' => $filters
        ]);

    }

    public function filterProducts(Request $request, Response $response, $args) {

        $filters = explode('/', $args['params']);

        $products = $this->container->get('productDS')->filterProducts($filters, $args['sort']);


        return $this->view->render($response, 'products.twig', [
            'products' => $products,
            'args' => $args,
            'count' => count($products),
            'filters' => $filters
        ]);

    }

    
    public function search(Request $request, Response $response, $args) {

        // get all product names
        $list = $this->productDS->fetchAllProductNames();
        // print_r($list);
        $q = $args['str'];
        $suggest = [];

        if($q != "") {
            $q = strtolower($q);
            $len = strlen($q);
            foreach ($list as $name => $value) {
                if(stristr($name, substr($q, 0 ,$len))) {
                    if($suggest === "") {
                        $suggest[] = $name;
                    } else {
                        $suggest[$name] = $value;
                    }
                }
            }
        }

        // print_r($suggest);

        return $this->view->render($response, 'layouts/partials/searchProduct.twig', [
            'suggest' => $suggest,
        ]);

        


    }

}

// /////
//  STEP BEFORE BIN, LAST CHECKS TO SEE IF IN USE ELSEWHERE IN APP
// /////




