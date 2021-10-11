<?php

class dataBaseEditor
{
    static function getUsers($dataBaseConnect)
    {
        $userList = [];
        $resultDB = $dataBaseConnect->prepare("select * from users");
        $resultDB->execute();

        while ($res = $resultDB->fetch(PDO::FETCH_ASSOC)) {
            $userList[] = $res;
        }

        echoResults($userList, 200);
    }

    static function getUser($dataBaseConnect, $id)
    {
        $resultDB = $dataBaseConnect->prepare("select * from users where id = $id");
        $resultDB->execute();
        $res = $resultDB->fetch(PDO::FETCH_ASSOC);

        if (!$res) {
            echoResults('User not found', 404);
            die();
        }
        echoResults($res, 200);
    }

    static function addUser($dataBaseConnect, $data)
    {
        if ($data['firstName'] != '' && $data['lastName'] != '') {
            $resultDB = $dataBaseConnect->prepare("insert into users values (null, :firstName, :lastName)");
            $resultDB->execute(array(':firstName' => $data['firstName'], ':lastName' => $data['lastName']));

            $res = $dataBaseConnect->lastInsertId();

            echoResults($res, 201);
        } else {
            echoResults('The username or password is incorrect', 400);
        }
    }

    static function updateUser($dataBaseConnect, $id, $data)
    {
        $resultDB = $dataBaseConnect->prepare("select * from users where id = $id");
        $resultDB->execute();
        $res = $resultDB->fetch(PDO::FETCH_ASSOC);

        if ($data['firstName'] != '' && $data['lastName'] != '' && $res) {
            $resultDB = $dataBaseConnect->prepare("update users set firstName = :firstName, lastName = :lastName where id = $id");
            $resultDB->execute(array(':firstName' => $data['firstName'], ':lastName' => $data['lastName']));

            $res = 'User is updated';

            echoResults($res, 202);
        } else echoResults('The username or password is incorrect', 400);
    }

    static function deleteUser($dataBaseConnect, $id)
    {
        $resultDB = $dataBaseConnect->prepare("delete from users where id = $id");
        $resultDB->execute();

        echoResults('', 204);
    }
}

function echoResults($res, $code)
{
    http_response_code($code);
    echo json_encode($res);
}

