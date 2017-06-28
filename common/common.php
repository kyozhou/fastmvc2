<?php
header("Content-type:text/html;charset=utf-8");
//header("Access-Control-Allow-Origin: *");
set_error_handler("errorHandler");
register_shutdown_function('shutdownHandler');
require_once dirname(dirname(__FILE__)).'/config/env.php';
require_once dirname(dirname(__FILE__)).'/config/'.ENVIRONMENT.'.php';
include dirname(__FILE__) . '/function/default.php';
include dirname(__FILE__) . '/function/util.php';


function logger($content, $commitNow = true) {
    global $logs;
    $logs = empty($logs) ? [] : $logs;
    $logs[] = $content;
    if($commitNow) {
        foreach($logs as $log) {
            $filename = "hour_" . date('Y-m-d_H') . ".log";
            file_put_contents(LOG_ROOT . '/' . $filename, $log . "\n", FILE_APPEND);
        }
        $logs = [];
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

