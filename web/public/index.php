<?php

require_once 'database/connect.php';
require_once 'functions.php';

global $dataBaseConnect;

$method = $_SERVER['REQUEST_METHOD'];
$url = rtrim($_SERVER['REQUEST_URI'], '/');
$urlArr = explode('/', $url);

if ($urlArr[1] != 'users') {
    echoResults('The request is incorrect', 400);
    return;
}

if (isset($urlArr[3])) {
    echoResults('The request is incorrect', 404);
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

    echoResults('The request is incorrect', 400);
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

echoResults('The request is incorrect', 400);