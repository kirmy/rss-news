<?php

require_once '../vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Session;

use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;

// $_SERVER['REQUEST_URI'] = preg_replace('|/$|', '', $_SERVER['REQUEST_URI'], 1);
$request = Request::createFromGlobals();
$response = new Response;

$session = new Session;
$session->start();

$request->setSession($session);

$routes = new RouteCollection();

$routes->add('index', new Route('/', ['_controller' => 'App\Controller\Front@getIndex'],
    [], [], '', [], ['GET']));
$routes->add('get_login', new Route('/login', ['_controller' => 'App\Controller\Front@getLogin'],
    [], [], '', [], ['GET']));
$routes->add('post_login', new Route('/login', ['_controller' => 'App\Controller\Front@postLogin'],
    [], [], '', [], ['POST']));
$routes->add('logout', new Route('/logout', ['_controller' => 'App\Controller\Front@getLogout'],
    [], [], '', [], ['GET']));
$routes->add('cabinet', new Route('/cabinet', ['_controller' => 'App\Controller\Cabinet@getIndex'],
    [], [], '', [], ['GET']));
$routes->add('addfeed', new Route('/addfeed', ['_controller' => 'App\Controller\Cabinet@addFeed'],
    [], [], '', [], ['POST']));
$routes->add('deleteSource', new Route('/deletesource', ['_controller' => 'App\Controller\Cabinet@deleteSource'],
    [], [], '', [], ['POST']));
$routes->add('deleteFeeds', new Route('/deletefeeds', ['_controller' => 'App\Controller\Cabinet@deleteFeeds'],
    [], [], '', [], ['POST']));
    

$context = new RequestContext();
$context->fromRequest($request);

$matcher = new UrlMatcher($routes, $context);
//var_dump($context);
try {
    $parameters = $matcher->matchRequest($request);
    //var_dump($context);
    //echo "<br>";
    //var_dump($request->request);
    //echo "<br>";
    $request->attributes->replace($parameters);
    $action = $parameters['_controller'];
    //var_dump($request->request);
} catch (Symfony\Component\Routing\Exception\ResourceNotFoundException $e) {
    $response->setStatusCode('404');
    $response->setContent('404: Page not found');
} catch (Symfony\Component\Routing\Exception\MethodNotAllowedException $e) {
    $response->setStatusCode('405');
    $response->setContent('405: Method not allowed');
}

if (isset($action) && is_string($action)) {
    $controller = explode('@', $action);
    //var_dump($controller);
    $controller_class_name = $controller[0];
    //var_dump($controller,$controller_class_name);
    $controller_instance = new $controller_class_name;
    $method = $controller[1];

    $response = $controller_instance->$method($request, $response);
}

// if (isset($action) && is_callable($action)) {
//     $response = $action($request, $response);
// }

// if (! isset($action)) {
//     $response->setStatusCode('404');
//     $response->setContent('404: Page not found');
// }

$response->send();
