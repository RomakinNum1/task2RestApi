<?php

namespace Roman\Func;

use PDO;
use PDOException;

class ConnectToDB
{
    static function connect()
    {
        try {
            $config = ['host' => $_ENV["MYSQL_HOST"],
                'db_name' => $_ENV["MYSQL_DATABASE"],
                'username' => $_ENV["MYSQL_USER"],
                'password' => $_ENV["MYSQL_PASSWORD"]
            ];
            return new PDO('mysql:host=' . $config['host'] . ';dbname=' . $config['db_name'], $config['username'], $config['password']);
        } catch (PDOException $e) {
            echo $e->getMessage();
            die();
        }
    }
}

