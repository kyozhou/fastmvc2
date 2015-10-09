<?php
function where($array, $condition) {
    if(is_array($array) && is_array($condition)) {
        $count = count($array);
        $resultArray = array();
        for($i = 0; $i < $count; $i++) {
            $row = $array[$i];
            $isOffer = true;
            foreach($condition as $key => $value) {
                $isOffer = $isOffer && $row[$key] == $value;
            }
            if($isOffer) {
                $resultArray[] = $row;
            }
        }
        return $resultArray;
    }else {
        return false;
    }
}

function pluck($array, $column) {
    if(is_array($array)) {
        $count = count($array);
        $resultArray = array();
        for($i = 0; $i < $count; $i++) {
            $row = $array[$i];
            $resultArray[] = $row[$column];
        }
        return $resultArray;
    }else {
        return [];
    }
}

function get_real_ip(){ 
    $ip = ''; 
    if(!empty($_SERVER['HTTP_CLIENT_IP'])){ 
        $ip=$_SERVER['HTTP_CLIENT_IP']; 
    }
    if(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){ 
        $ips=explode (', ', $_SERVER['HTTP_X_FORWARDED_FOR']); 
        if($ip){ array_unshift($ips, $ip); $ip=FALSE; }
        for ($i=0; $i < count($ips); $i++){
            if(!eregi ('^(10│172.16│192.168).', $ips[$i])){
                $ip=$ips[$i];
                break;
            }
        }
    }
    return ($ip ? $ip : $_SERVER['REMOTE_ADDR']); 
}

function httpGet($url, $headers = []) {
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查      
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 1); // 从证书中检查SSL加密算法是否存在      
    curl_setopt($curl, CURLOPT_USERAGENT, @$_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器      
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转      
    curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer      
    curl_setopt($curl, CURLOPT_HTTPGET, 1); // 发送一个常规的Post请求      
    curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环      
    curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容      
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回      
    $tmpInfo = curl_exec($curl); // 执行操作      
    if (curl_errno($curl)) {      
        return false;
    }      
    curl_close($curl);
    return $tmpInfo; // 返回数据  
}

function httpPost($url, $params, $headerMap = []) {
    foreach($headerMap as $key => $value ) { 
        $headers[] = $key .':' . $value;
    }
    $curl = curl_init(); // 启动一个CURL会话      
    curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址                  
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查      
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 1); // 从证书中检查SSL加密算法是否存在      
    curl_setopt($curl, CURLOPT_USERAGENT, @$_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器      
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转      
    curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer      
    curl_setopt($curl, CURLOPT_POST, TRUE); // 发送一个常规的Post请求      
    curl_setopt($curl, CURLOPT_POSTFIELDS, $params); // Post提交的数据包      
    curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环      
    curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容      
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
    curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($curl, CURLOPT_USERPWD, "miaoke:music111"); 
    curl_setopt($curl, CURLOPT_HTTPHEADER , $headers);   
    curl_setopt($curl, CURLOPT_HEADER, 1);
    $tmpInfo = curl_exec($curl); // 执行操作      
    if (curl_errno($curl)) {
        return false;
    }      
    curl_close($curl); // 关键CURL会话      
    return $tmpInfo; // 返回数据      
}

function tcpRequest($address, $data) {
    list($ip, $port) = explode(':', $address);
    $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
    $connection = socket_connect($socket, $ip, $port);
    $return = '';
    if ($connection === true) { 
        if (!socket_write($socket, "$data\n")) { 
            return false;
        }
        while ($buffer = socket_read($socket, 1024, PHP_NORMAL_READ)) { 
            break;
        }
    }
    socket_close($socket);
    return $return;
}

