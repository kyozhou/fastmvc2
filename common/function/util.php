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

function filtPost($keys = array(), $type = 'string', $defaultValue = null) {
    return filtRequest($keys, $type, $_POST, $defaultValue);
}

function filtGet($keys = array(), $type = 'string', $defaultValue = null) {
    return filtRequest($keys, $type, $_GET, $defaultValue);
}

function filtRequest($keys = array(), $type = 'string', $inputArray = array(), $defaultValue = null) {
    $request = empty($inputArray) ? array_merge($_GET, $_POST) : $inputArray;
    if (!empty($request)) {
        if (is_array($keys)) {
            if (!empty($keys)) {
                $dataFilted = array();
                foreach ($keys as $key) {
                    $dataFilted[$key] = empty($request[$key]) ? null : filtRequest($request[$key], $type);
                }
                return $dataFilted;
            } else {
                foreach ($request as $key => $value) {
                    $request[$key] = empty($value) ? null : filtRequest($value, $type);
                }
                return $request;
            }
        } else {
            $data = !empty($request[$keys]) ? $request[$keys] : '';
            switch ($type) {
            case 'string':
                $defaultValue = $defaultValue === null ? '' : $defaultValue;
                return !empty($data) ? $data : $defaultValue;
                break;
            case 'int':
                $defaultValue = $defaultValue === null ? 0 : $defaultValue;
                return !empty($data) && is_numeric($data) ? intval($data) : $defaultValue;
                break;
            case 'uint':
                $defaultValue = $defaultValue === null ? 0 : $defaultValue;
                return !empty($data) && is_numeric($data) && intval($data) >= 0 ? abs(intval($data)) : $defaultValue;
                break;
            case 'float':
                $defaultValue = $defaultValue === null ? 0 : $defaultValue;
                return !empty($data) && is_numeric($data) ? round($data, 2) : $defaultValue;
                break;
            case 'array':
                $defaultValue = $defaultValue === null ? array() : $defaultValue;
                return !empty($data) && is_array($data) ? $data : $defaultValue;
                break;
            default:
                return $defaultValue;
                break;
            }
        }
    } else {
        return $defaultValue;
    }
}

function cleanXss(&$string, $low = False) {
    if (! is_array ( $string )) {
        $string = trim ( $string );
        $string = strip_tags ( $string );
        $string = htmlspecialchars ( $string );
        if ($low)
        {
            return True;
        }
        $string = str_replace ( array ('"', "\\", "'", "/", "..", "../", "./", "//" ), '', $string );
        $no = '/%0[0-8bcef]/';
        $string = preg_replace ( $no, '', $string );
        $no = '/%1[0-9a-f]/';
        $string = preg_replace ( $no, '', $string );
        $no = '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S';
        $string = preg_replace ( $no, '', $string );
        return true;
    }
    $keys = array_keys ( $string );
    foreach ( $keys as $key ) {
        cleanXss ( $string [$key] );
    }
}

