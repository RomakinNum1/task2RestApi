<?php

namespace Roman\Func;

use PDO;
use PDOException;

class ConnectToDB
{
    static function connect()
    {
        try {
            $config = ['host' => 'mysql',
                'db_name' => 'test',
                'username' => 'dev',
                'password' => 'dev',
                'charset' => 'utf8'
            ];
            return new PDO('mysql:host=' . $config['host'] . ';dbname=' . $config['db_name'], $config['username'], $config['password']);
        } catch (PDOException $e) {
            echo $e->getMessage();
            die();
        }
    }
}

