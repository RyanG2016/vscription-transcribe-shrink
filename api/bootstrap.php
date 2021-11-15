<?php
require 'vendor/autoload.php';

use Dotenv\Dotenv;
use Src\System\DatabaseConnector;

$dotenv = new DotEnv(__DIR__);

$dotenv->load();


// test code, should output:
// api://default
// when you run $ php bootstrap.php
//echo getenv('OKTAAUDIENCE');

$dbConnection = (new DatabaseConnector())->getConnection();
