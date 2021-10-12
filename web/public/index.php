<?php

use Roman\Func\ConnectToDB;
use Roman\Func\dataBaseEditor;
require '/var/www/composer/vendor/autoload.php';

use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;

$dataBaseConnect = ConnectToDB::connect();

try {
    $route1 = new Route('/users/{id}');
    $route2 = new Route('/users');
    $routes = new RouteCollection();
    $routes->add('users1', $route1);
    $routes->add('users2', $route2);
    $context = new RequestContext($_SERVER['REQUEST_URI']);
    $matcher = new UrlMatcher($routes, $context);
    $parameters = $matcher->match($_SERVER['REQUEST_URI']);
    if (isset($parameters['_rout'])) {
        dataBaseEditor::echoResults('The request is incorrect', 400);
        return;
    }

    if (!isset($parameters['id'])) {
        if ($context->getMethod() == 'GET') {
            dataBaseEditor::getUsers($dataBaseConnect);
            return;
        }

        if ($context->getMethod() == 'POST') {
            $data = file_get_contents('php://input');
            $data = json_decode($data, true);

            dataBaseEditor::addUser($dataBaseConnect, $data);
            return;
        }

        dataBaseEditor::echoResults('The request is incorrect', 400);
        die();
    }

    if ($context->getMethod() == 'GET') {
        dataBaseEditor::getUser($dataBaseConnect, $parameters['id']);
        return;
    }

    if ($context->getMethod() == 'PUT') {
        $data = file_get_contents('php://input');
        $data = json_decode($data, true);

        dataBaseEditor::updateUser($dataBaseConnect, $parameters['id'], $data);
        return;
    }

    if ($context->getMethod() == 'DELETE') {
        dataBaseEditor::deleteUser($dataBaseConnect, $parameters['id']);
        return;
    }

    dataBaseEditor::echoResults('The request is incorrect', 400);
}
catch(ResourceNotFoundException $ex){
    dataBaseEditor::echoResults('The request is incorrect', 400);
}