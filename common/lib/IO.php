<?php
/**
 * Created by JetBrains PhpStorm.
 * User: zhoubin
 * Date: 13-12-19
 * Time: 下午3:16
 * To change this template use File | Settings | File Templates.
 */

spl_autoload_register('my_autoload');
function my_autoload($file){
    $filePath = str_replace('\\','/',dirname(__FILE__).'/'.$file);
    if(file_exists($filePath. '.php')) {
        require_once $filePath . '.php';
        return true;
    }else {
        return false;
    }
}

class IO {
    static function makePath($path) {
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }
        return $path;
    }
}

