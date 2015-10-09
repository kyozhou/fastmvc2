<?php
/**
 * Created by JetBrains PhpStorm.
 * User: zhoubin
 * Date: 13-9-30
 * Time: 下午7:31
 * To change this template use File | Settings | File Templates.
 */

class Loader {

    static function includeLib($file) {
        self::includeFile($file, APP_ROOT . '/common/lib');
    }

    static function includeFile($file, $basePath) {
        $basePath = !empty($basePath) && is_dir($basePath) ?
            $basePath : APP_ROOT;
        if(is_string($file)) {
            include_once $basePath . '/' . $file . '.php';
        } else if (is_array($file)) {
            foreach ($file as $aFile) {
                self::includeFile($aFile, $basePath);
            }
        }
    }
}
