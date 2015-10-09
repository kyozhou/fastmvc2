<?php

/**
 * DB操作类（针对mysql）
 * by kyozhou@sina.com
 * at 20130614
 */
class DB {

    private $host;
    private $user;
    private $password;
    private $port;
    private $charset;
    private $databaseName;
    private $link;
    private static $dbInstances;

    //singleton
    static function get($config) {
        $dbKey = md5($config);
        if (!isset(self::$dbInstances[$dbKey])) {
            self::$dbInstances[$dbKey] = new DB(unserialize($config));
        }
        return self::$dbInstances[$dbKey];
    }

    function __clone() {
        trigger_error('Clone is not allow', E_USER_ERROR);
    }

    function __construct($config) {
        $this->host = $config['host'];
        $this->port = $config['port'];
        $this->user = $config['user'];
        $this->password = $config['password'];
        $this->databaseName = $config['name'];

        if (isset($config['charset']) && $config['charset']) {
            $this->charset = $config['charset'];
        } else {
            $this->charset = 'utf8mb4';
        }

        $this->connect();
    }

    function __destruct() {
        $this->close();
    }

    function connect() {
        $this->close();

        $this->link = mysqli_connect($this->host, $this->user, $this->password, '', $this->port);

        if (empty($this->link)) {
            //$this->reportError("<!-- Can not connect database server [{$server}]<br>\n -->");
            return false;
        }

        $version = floatval(mysqli_get_server_info($this->link));
        if ($version > 4.1) {
            mysqli_query($this->link, "SET character_set_connection={$this->charset}, character_set_results={$this->charset}, character_set_client=binary");
        } else {
            mysqli_query($this->link, "set names '{$this->charset}'");
        }

        $databaseSelected = mysqli_select_db($this->link, $this->databaseName);
        if (!$databaseSelected) {
            //$this->reportError("<!-- Can not open database [{$this->databaseName}] @ [{$server}]<br>\n -->");
            return false;
        }
    }

    private function reportError($message) {
        echo $message;
        exit;
    }

    function close() {
        if (!empty($this->link)) {
            mysqli_close($this->link);
        }
    }

    function autocommitOff(){
        mysqli_autocommit($this->link,FALSE);
    }

    function commitTransaction(){
        mysqli_commit($this->link);
        mysqli_autocommit($this->link,TRUE);
    }

    function rollbackTransaction(){
        mysqli_rollback($this->link);
        mysqli_autocommit($this->link,TRUE);
    }

    /**
     * 执行SQL, 返回影响的行数
     *
     * @param string $sql
     * @return int
     */
    function execute($sql) {
        return $this->query($sql);
    }

    /**
     * 执行SQL, 以数组形式返回所有结果
     * <pre>
     * array(
     *   0 => array('id' => 1, 'name' => 'tom'),
     *   1 => array('id' => 2, 'name' => 'jerry'),
     *   ... ...
     * )
     * </pre>
     *
     *
     * @param  string $sql
     * @return array
     */
    function fetchTable($sql) {
        return $this->query($sql, 'all');
    }

    /**
     * 执行SQL, 获取第一行记录
     * <pre>
     * array(
     *   'id'   => '1',
     *   'name' => 'tom',
     *   ... ...
     * )
     * </pre>
     *
     * @param  string $sql
     * @return array
     */
    function fetchRow($sql) {
        return $this->query($sql, 'array');
    }

    /**
     * 执行SQL, 获取第一列记录
     * <pre>
     * array(
     *   'id'   => '1',
     *   'name' => 'tom',
     *   ... ...
     * )
     * </pre>
     *
     * @param  string $sql
     * @return array
     */
    function fetchColumn($sql) {
        return $this->query($sql, 'column');
    }

    /**
     * 执行SQL, 获取第一行第一列数据
     *
     * @param string $sql
     * @return string
     */
    function fetchCell($sql) {
        return $this->query($sql, '1');
    }

    function insertId() {
        return @mysqli_insert_id($this->link);
    }

    /**
     * 执行SQL, 并返回结果
     *
     * @param string $sql
     * @param string $queryType
     * @return mixed
     */
    private function query($sql, $queryType = '') {
        $stime = microtime(TRUE);

        if (!mysqli_ping(@$this->link)) {
            $this->connect();
        }

        $result = mysqli_query($this->link, $sql);

        $insertId = $this->insertId();
        if($insertId > 0 && stripos($sql, 'insert') !== false) {
            return $insertId;
        }
        if (empty($queryType)){
            return mysqli_affected_rows($this->link);
        }
        if (empty($result))
            return false;

        $queryType = trim(strtolower($queryType));
        switch ($queryType) {
            case '1':
                $row = mysqli_fetch_row($result);
                $data = $row[0];
                break;

            case 'array':
                $data = mysqli_fetch_assoc($result);
                break;

            case 'column':
                $data = array();
                while ($array = mysqli_fetch_row($result)) {
                    $data[] = $array[0];
                }
                break;

            case 'all':
                $data = array();
                while ($array = mysqli_fetch_assoc($result)) {
                    $data[] = $array;
                }
                break;

            default:
                $data = false;
                break;
        }
        $etime = microtime(TRUE) - $stime;
        global $facade_total_sqls;
        $facade_total_sqls[] = array('time' => $etime, 'msg' => $this->host . ':' . $this->port . ' ' . $sql);
        return $data;
    }

}
