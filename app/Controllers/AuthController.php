<?php

namespace App\Controllers;

use PDO;
use App\Model\Entry;
use Slim\Views\Twig;
use App\Model\AccountDataSet;
use App\Validation\Validator;
use App\Controllers\Controller;
use App\Authentication\Authentication;
use App\Validation\Exceptions\InvalidSigninDetails;
use Psr\Http\Message\ResponseInterface as Response;
use App\Authentication\Provider\MySQLAuthenticateUser;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Validation\Exceptions\EmailUnavailableException;

class AuthController extends Controller {

    // public function __construct(Twig $view, Validator $validator, Entry $entry) {

    //     $this->view = $view;
    //     $this->validator = $validator;
    //     $this->entry = $entry;

    // }

    public function signup(Request $request, Response $response) {

        return $this->container->get('view')->render($response, 'signup.twig');

    }

    public function postSignup(Request $request, Response $response) {

        // get passed form data
        $data = $request->getParsedBody();

        $validation = $this->container->get('validator')->validate($data, [
            'firstname' => ['name', 'lengthMinShort'],
            'lastname' => ['name', 'lengthMinShort'],
            'email' => ['emailFormat', 'emailAvailable'],
            'password' => [
                'containUppercase',
                'containLowercase',
                'containNumber',
                'containSpecial',
                'lengthMinMedium'
            ],
            'verify_password' => [
                'containUppercase',
                'containLowercase',
                'containNumber',
                'containSpecial',
                'lengthMinMedium'
            ],
        ]);

        $this->container->get('validator')->checkPasswords(
            $data['password'],$data['verify_password']
        );

        if($validation->failed()) {
            // return to sign-up page
            return $response
                ->withHeader('Location', '/signup')
                ->withStatus(302);
        } 
        else {
            $this->container->get('entry')->signup($data);
            $_SESSION['flash']['success'] = 'Welcome to Company Name'; 
            $this->container->get('entry')->check($data['email'], $data['password']);
            return $response
                ->withHeader('Location', '/')
                ->withStatus(302);
        }
        
    }

    public function signin(Request $request, Response $response) {
        return $this->container->get('view')->render($response, 'signin.twig');
    }

    public function postSignin(Request $request, Response $response) {

        $data = $request->getParsedBody();

        $validation = $this->container->get('validator')->validate($data, [
            'email' => ['emailFormat'],
            'password' => ['lengthMinMedium']
        ]);

        // $this->container->get('flash')->addMessage('success', 'Welcome back ');

        if($validation->failed()) {
            // return to sign-up page
            return $response
                ->withHeader('Location', '/signin')
                ->withStatus(302);
        } 

        // use input to check whether to authorise
        try {
            $check = $this->container->get('auth')->check($data['email'], $data['password']);
            
        } catch(InvalidSigninDetails $e) {
            
            $_SESSION['flash']['failed'] = $e->getMessage();

            return $response
            ->withHeader('Location', '/signin')
            ->withStatus(302);
        }

        // SetEnvIf Authorization "(.*)" HTTP_AUTHORIZATION=$1 
        // $request = $request->withAttribute('token', $check);
        // var_dump($response);
        // $response = $response->withAddedHeader('Authorization', $check)->withStatus(302);

        $_SESSION['flash']['success'] = 'Welcome back to Company Name'; 
        // find way to add token to authorization header here
        die($check);
        $response = $response
        ->withHeader('Location', '/')
        ->withStatus(302);
        // var_dump(1);
        

        // $api_url = '';

        // $context = stream_context_create(array(
        //     'http' => array(
        //         'header' => "Authorization: Bearer " . $check
        //     ),
        // ));

        // $result = file_get_contents('/', false, $context);
        // var_dump($response->withHeader('Location', '/')->getHeader('Location')[0]);
        // die($context);
        return $response;
        // return $response->withHeader('Authorization', 'Bearer ' . $check);
        

    }



    public function signout(Request $request, Response $response) {

        $this->container->get('entry')->signout();

        // return $response->withRedirect($this->router->pathFor('home'));
        return $response
                ->withHeader('Location', '/')
                ->withStatus(302);

    }


}