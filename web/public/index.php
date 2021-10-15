<?php

use Firebase\JWT\JWT;
use Roman\Func\ConnectToDB;
use Roman\Func\dataBaseEditor;

require '/var/www/composer/vendor/autoload.php';

use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;
use Symfony\Component\HttpFoundation\Request;

$dataBaseConnect = ConnectToDB::connect();

try {
    $routes = new RouteCollection();

    $routes->add('getUserId', new Route('/users/{id}'));
    $routes->add('getUsers', new Route('/users'));
    $routes->add('confirmUser', new Route('/confirm/{token}'));

    $context = new RequestContext();
    $context->fromRequest(Request::createFromGlobals());

    $matcher = new UrlMatcher($routes, $context);
    $parameters = $matcher->match($_SERVER['REQUEST_URI']);

    if ($parameters['_route'] == 'confirmUser') {
        $decoded = JWT::decode($parameters['token'], dataBaseEditor::$key, array('HS256'));

        if (dataBaseEditor::confirmEmail($dataBaseConnect, $decoded->id)) {
            dataBaseEditor::echoResults('Email confirmed', 200);
            return;
        } else {
            if ($decoded->time < time()) {
                dataBaseEditor::deleteInactiveUsers($dataBaseConnect, $decoded->id);
                dataBaseEditor::echoResults('The address is invalid', 404);
                return;
            } else {
                dataBaseEditor::echoResults('Address not found', 404);
            }
        }
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
        return;
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
} catch (ResourceNotFoundException $ex) {
    dataBaseEditor::echoResults('The request is incorrect', 400);
}