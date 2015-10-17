<?php
error_reporting(E_ALL);

define('APP_URL', 'http://localhost');
define('LOG_ROOT', '/data/log/');

$dbs = array(
    'main' => array(
        'host'     => '127.0.0.1',
        'port'     => '3306',
        'user'     => 'root',
        'password' => 'iampassword',
        'name'     => 'iamdatabasename',
        'charset'  => 'utf8mb4',
    )
);

