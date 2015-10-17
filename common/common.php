<?php
header("Content-type:text/html;charset=utf-8");
//header("Access-Control-Allow-Origin: *");
set_error_handler("errorHandler");
register_shutdown_function('shutdownHandler');
require_once dirname(dirname(__FILE__)).'/config/env.php';
require_once dirname(dirname(__FILE__)).'/config/'.ENVIRONMENT.'.php';
include dirname(__FILE__) . '/function/default.php';
include dirname(__FILE__) . '/function/util.php';

function __autoload($path) {
    $root = dirname(dirname(__FILE__));
    $pathArray = explode('\\', $path);
    $pathPre = $pathArray[0];
    $className = $pathArray[count($pathArray) - 1];
    unset($pathArray[0]);
    $pathSuf = implode('/', $pathArray);
    $pathMap = array(
        'lib' => $root . '/common/lib', 
        'function' => $root . '/common/function',
        'controller' => $root . '/src/controller',
        'model' => $root . '/src/model',
        'view' => $root . '/src/view',
    );
    $path = $pathMap[$pathPre] . '/' . $pathSuf . '.php';
    if(file_exists($path)) {
        include_once($path);
    }elseif(!class_exists($className)) {
        logger('path:'. $path .' is not exists');
        die('path:'. $path .' is not exists');
    }
}

function errorHandler($errno, $errmsg, $filename, $linenum, $vars) {
    global $timeStart;
    logger("-----------------$timeStart-------------------");
    logger("errno:$errno");
    logger("errmsg:$errmsg");
    logger("filename:$filename");
    logger("linenum:$linenum");
    logger("vars:". print_r($vars, true));
    logger('------------------------------------');
}

function shutdownHandler() {
    $errorInfo = error_get_last();
    if($errorInfo && in_array($errorInfo['type'],array(1,4,16,64,256,4096,E_ALL))){
        global $timeStart;
        logger("-----------------$timeStart-------------------");
        logger('致命错误:' . $errorInfo['message']);
        logger('文件:'.$errorInfo['file']);
        logger('在第'.$errorInfo['line'].'行');
    }
}

