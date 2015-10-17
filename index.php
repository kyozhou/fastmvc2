<?php
$timeStart = microtime(true);
include dirname(__FILE__) . '/common/common.php';
$router = new lib\Router(dirname(__FILE__) . '/src');
$router->action();


