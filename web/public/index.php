<?php

//require_once 'database/ConnectToDB.php';
//require_once 'dataBaseEditor.php';

use Roman\Func\ConnectToDB;
use Roman\Func\dataBaseEditor;
require '/var/www/composer/vendor/autoload.php';

$dataBaseConnect = ConnectToDB::connect();

$method = $_SERVER['REQUEST_METHOD'];
$url = rtrim($_SERVER['REQUEST_URI'], '/');
$urlArr = explode('/', $url);

if ($urlArr[1] != 'users') {
    dataBaseEditor::echoResults('The request is incorrect', 400);
    return;
}

if (isset($urlArr[3])) {
    dataBaseEditor::echoResults('The request is incorrect', 404);
    return;
}

$id = $urlArr[2];

if (!$id) {
    if ($method == 'GET') {
        dataBaseEditor::getUsers($dataBaseConnect);
        return;
    }

    if ($method == 'POST') {
        $data = file_get_contents('php://input');
        $data = json_decode($data, true);

        dataBaseEditor::addUser($dataBaseConnect, $data);
        return;
    }

    dataBaseEditor::echoResults('The request is incorrect', 400);
    die();
}

if ($method == 'GET') {
    dataBaseEditor::getUser($dataBaseConnect, $id);
    return;
}

if ($method == 'PUT') {
    $data = file_get_contents('php://input');
    $data = json_decode($data, true);

    dataBaseEditor::updateUser($dataBaseConnect, $id, $data);
    return;
}

if ($method == 'DELETE') {
    dataBaseEditor::deleteUser($dataBaseConnect, $id);
    return;
}

dataBaseEditor::echoResults('The request is incorrect', 400);