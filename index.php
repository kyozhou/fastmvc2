<?php
apiLimit();
global $timeStart;
$timeStart = microtime(true);
if(!empty($_GET['PHPSESSID'])) {
    session_id($_GET['PHPSESSID']);
}

set_error_handler("errorHandler");
register_shutdown_function('shutdownHandler');

include dirname(__FILE__) . '/common/common.php';
include dirname(__FILE__) . '/common/lib/Router.php';

if(!empty($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'], 'android') !== false && !compareVersion('1.6.0')) {
    die(json_encode(array('error' => 0, 'errno' => 'version_too_low', 'error_message' => 'APP版本过低，请及时更新')));
}

global $timeStart;
logger("----------------------------------" . date('Y-m-d H:i:s') . "-----------------------------------");
logger("TIME START : " . $timeStart);
logger("HEADERS : " . print_r($_SERVER, true));
logger("SESSION : " . print_r($_SESSION, true));
logger("COOKIE : " . print_r($_COOKIE, true));
logger("GET : " . print_r($_GET, true));
logger("POST : " . print_r($_POST, true));
logger("FILES : " . print_r($_FILES, true));
logger("INPUT : " . print_r(json_decode(file_get_contents("php://input"), true), true));

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

function apiLimit() {
    
}

$router = new Router(APP_ROOT);
$router->action();
