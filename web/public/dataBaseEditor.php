<?php

namespace Roman\Func;

use DateTimeImmutable;
use Firebase\JWT\JWT;
use PDO;
use PHPMailer\PHPMailer\PHPMailer;

class dataBaseEditor
{
    static $key = '34dddghre2rtjkyd';

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
        if ($data['firstName'] != '' && $data['lastName'] != '' && $data['email'] != '' && filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $resultDB = $dataBaseConnect->prepare("insert into users values (null, :firstName, :lastName, :email, false)");
            $resultDB->execute(array(':firstName' => $data['firstName'], ':lastName' => $data['lastName'], ':email' => $data['email']));

            $issuedAt   = new DateTimeImmutable();
            $data1 = [
                'exp'  => $issuedAt->modify('+1 minutes')->getTimestamp(),
                'id' => $dataBaseConnect->lastInsertId()
            ];

            $jwt = JWT::encode($data1, self::$key);

            self::sendMessage($jwt, $data);

            $res = $dataBaseConnect->lastInsertId();

            self::echoResults($res, 201);
        } else {
            self::echoResults('The username or password or email is incorrect', 400);
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

    static function confirmEmail($dataBaseConnect, $token)
    {
        $resultDB = $dataBaseConnect->prepare("select * from users where id = $token AND status = 0");
        $resultDB->execute();
        $res = $resultDB->fetch(PDO::FETCH_ASSOC);

        if($res) {
            $resultDB = $dataBaseConnect->prepare("update users set status = true where id = $token");
            $resultDB->execute();
            return true;
        } else {
            return false;
        }
    }

    static function sendMessage($token, $data)
    {
        $mail = new PHPMailer();
        $mail->CharSet = 'UTF-8';
        $mail->isSMTP();                   // Отправка через SMTP
        $mail->Host = 'ssl://smtp.mail.ru';  // Адрес SMTP сервера
        $mail->SMTPAuth = true;          // Enable SMTP authentication
        $mail->Username = 'kinash2001@list.ru';       // ваше имя пользователя (без домена и @)
        $mail->Password = '89505690579hjvf';    // ваш пароль
        $mail->SMTPSecure = 'ssl';         // шифрование ssl
        $mail->Port = 465;               // порт подключения

        $mail->setFrom('kinash2001@list.ru');// от кого
        $mail->addAddress($data['email']); // кому

        $mail->Subject = 'Подтверждение email';
        $mail->msgHTML("<html><body>
                <h1>Здравствуйте!</h1>
                <p>Подтвердите свою почту по ссылке: <a href='http://task2.loc/confirm/$token'>ссылка</a></p>
                </html></body>");
        $mail->send();
        // Отправляем
        /*if ($mail->send()) {
            echo 'Письмо отправлено!';
        } else {
            echo 'Ошибка: ' . $mail->ErrorInfo;
        }*/
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