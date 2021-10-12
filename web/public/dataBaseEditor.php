<?php

namespace Roman\Func;

use PDO;

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

        self::echoResults($userList, 200);
    }

    static function getUser($dataBaseConnect, $id)
    {
        $resultDB = $dataBaseConnect->prepare("select * from users where id = $id");
        $resultDB->execute();
        $res = $resultDB->fetch(PDO::FETCH_ASSOC);

        if (!$res) {
            self::echoResults('User not found', 404);
            die();
        }
        self::echoResults($res, 200);
    }

    static function addUser($dataBaseConnect, $data)
    {
        if ($data['firstName'] != '' && $data['lastName'] != '' && $data['email'] != '') {
            $resultDB = $dataBaseConnect->prepare("insert into users values (null, :firstName, :lastName, :email, null)");
            $resultDB->execute(array(':firstName' => $data['firstName'], ':lastName' => $data['lastName'], ':email' => $data['email']));

            $res = $dataBaseConnect->lastInsertId();

            self::echoResults($res, 201);
        } else {
            self::echoResults('The username or password is incorrect', 400);
        }
    }

    static function updateUser($dataBaseConnect, $id, $data)
    {
        $resultDB = $dataBaseConnect->prepare("select * from users where id = $id");
        $resultDB->execute();
        $res = $resultDB->fetch(PDO::FETCH_ASSOC);

        if ($data['firstName'] != '' && $data['lastName'] != '' && $res && $data['email'] != '') {
            $resultDB = $dataBaseConnect->prepare("update users set firstName = :firstName, lastName = :lastName, email = :email where id = $id");
            $resultDB->execute(array(':firstName' => $data['firstName'], ':lastName' => $data['lastName'], ':email' => $data['email']));

            $res = 'User is updated';

            self::echoResults($res, 202);
        } else self::echoResults('The username or password is incorrect', 400);
    }

    static function deleteUser($dataBaseConnect, $id)
    {
        $resultDB = $dataBaseConnect->prepare("delete from users where id = $id");
        $resultDB->execute();

        self::echoResults('', 204);
    }

    static function echoResults($res, $code)
    {
        http_response_code($code);
        echo json_encode($res);
    }

    /*private static function getColumnNames($dataBaseConnect)
    {
        $sth = $dataBaseConnect->prepare("SELECT `COLUMN_NAME` FROM `INFORMATION_SCHEMA`.`COLUMNS` WHERE `TABLE_SCHEMA`='test' AND `TABLE_NAME`='users'");
        $sth->execute();
        $output = [];
        while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
            $output[] = $row['COLUMN_NAME'];
        }
        return $output;
    }*/
}

