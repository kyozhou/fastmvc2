<?php
header("Content-type:text/html;charset=utf-8");
//header("Access-Control-Allow-Origin: *");
require_once dirname(__FILE__).'/env.php';
require_once dirname(__FILE__).'/config/'.ENVIRONMENT.'.php';
include_once APP_ROOT . '/common/lib/Loader.php';
include_once APP_ROOT . '/common/function/default.php';
include_once APP_ROOT . '/common/function/util.php';

session_start();
