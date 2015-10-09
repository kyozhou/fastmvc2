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
include_once dirname(__FILE__).'/Qiniu/functions.php';

class IO {
    static function makePath($path) {
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }
        return $path;
    }

    static function upload2Qiniu($filePathFrom, $filePathTo) {
        if(file_exists($filePathFrom)) {
            $authObj = new Qiniu\Auth(QINIU_ACCESS_KEY, QINIU_SECRET_KEY);
            $uploadManagerObj = new Qiniu\Storage\UploadManager();
            $bucketManagerObj = new Qiniu\Storage\BucketManager($authObj);
            $token = $authObj->uploadToken(QINIU_BUCKET_STATIC);
            list($ret, $err) = $uploadManagerObj->putFile($token, $filePathTo, $filePathFrom);
            return empty($err) ? $ret['key'] : false;
        }else {
            return false;
        }
    }

    static function removeFromQiniu($filePath, $isVideo = false) {
        $authObj = new Qiniu\Auth(QINIU_ACCESS_KEY, QINIU_SECRET_KEY);
        $bucketManagerObj = new Qiniu\Storage\BucketManager($authObj);
        $return = $isVideo ? $bucketManagerObj->delete(QINIU_BUCKET_VIDEO, $filePath) : $bucketManagerObj->delete(QINIU_BUCKET_STATIC, $filePath);
        list($ret, $err) = is_array($return) ? $return : ['', $return];
        return $err !== null ? false : true;
    }
}

