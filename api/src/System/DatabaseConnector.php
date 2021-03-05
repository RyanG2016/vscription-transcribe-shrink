<?php
namespace  Src\System;

use PDO;
use PDOException;
date_default_timezone_set('America/Winnipeg');
class DatabaseConnector {

    private $dbConnection = null;

    public function __construct()
    {
        $host = getenv('DB_HOST');
        $port = getenv('DB_PORT');
        $db   = getenv('DB_DATABASE');
        $user = getenv('DB_USERNAME');
        $pass = getenv('DB_PASSWORD');

        try {
            $this->dbConnection = new PDO(
                "mysql:host=$host;port=$port;charset=utf8mb4;dbname=$db",
                $user,
                $pass
            );

            $this->dbConnection->exec("set global time_zone = 'America/Winnipeg';
                                        set @@global.time_zone = 'America/Winnipeg';
                                        SET time_zone = 'America/Winnipeg';");
        } catch (PDOException $e) {
//            exit($e->getMessage());
            exit("Failed to connect (VS4772");
        }
    }

    public function getConnection()
    {
        return $this->dbConnection;
    }
}