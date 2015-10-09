<?php 

/**
 * * A PHP session handler to keep session data within a MySQL database
 *
 * ini_set('session.save_handler', 'user');
 *
 * require_once('SessionHandler.php');
 * $session = new SessionHandler();
 * $session->setDbDetails('localhost', 'username', 'password', 'database');
 * $session->setDbTable('session_handler_table');
 * session_set_save_handler(array($session, 'open'),
 *      array($session, 'close'),
 *      array($session, 'read'),
 *      array($session, 'write'),
 *      array($session, 'destroy'),
 *      array($session, 'gc'));
 * register_shutdown_function('session_write_close');
 * session_start();
 * */

class SessionCustom{
    protected $dbConnection;
    protected $dbTable;
    protected $timeExpire = 2592000;

    public function setDbDetails($dbHost, $dbUser, $dbPassword, $dbDatabase){
        //create db connection
        $this->dbConnection = new mysqli($dbHost, $dbUser, $dbPassword, $dbDatabase);
        $this->dbConnection->set_charset('utf8');

        //check connection
        if (mysqli_connect_error()) {
            throw new Exception('Connect Error (' . mysqli_connect_errno() . ') ' . mysqli_connect_error());
        }
    }

    public function setDbConnection($dbConnection){
        $this->dbConnection = $dbConnection;
    }

    public function setDbTable($dbTable){
        $this->dbTable = $dbTable;
    }

    public function open() {
        //delete old session handlers
        $limit = time() - $this->timeExpire;
        $sql = sprintf("DELETE FROM %s WHERE time_created < %s", $this->dbTable, $limit);
        return $this->dbConnection->query($sql);
    }

    public function close() {
        return $this->dbConnection->close();
    }

    public function read($sessionId) {
        $sql = sprintf("SELECT session_id, data FROM %s WHERE session_id = '%s'", $this->dbTable, $this->dbConnection->escape_string($sessionId));
        if ($result = $this->dbConnection->query($sql)) {
            if ($result->num_rows && $result->num_rows > 0) {
                $record = $result->fetch_assoc();
                return $record['data'];
            } else {
                return false;
            }
        } else {
            return false;
        }
        return true;
    }

    public function write($sessionId, $data) {
        if(!empty($sessionId)) {
            $dataArray = self::unserializeData($data);
            $userId = empty($dataArray['user']['id']) ? 0 : $dataArray['user']['id'];
            $superUserId = empty($dataArray['user']['super_user_id']) ? 0 : $dataArray['user']['super_user_id'];
            $sql = sprintf("REPLACE INTO %s(`session_id`, `user_id`, `data`, `time_created`) VALUES('%s', '%s', '%s', '%s')",
                $this->dbTable, 
                $this->dbConnection->escape_string($sessionId),
                $this->dbConnection->escape_string($superUserId > 0 ? $superUserId : $userId),
                $this->dbConnection->escape_string($data),
                time());
            return $this->dbConnection->query($sql);
        }else {
            logger("sessionid empty, data is : " . print_r($data, true));
        }
    }
    
    public function destroy($sessionId) 
    {
        $sql = sprintf("DELETE FROM %s WHERE `sessionId` = '%s'", $this->dbTable, $this->dbConnection->escape_string($sessionId));
        return $this->dbConnection->query($sql);
    }

    public function destoryByUserId($userId) {
        $sql = sprintf("DELETE FROM %s WHERE `user_id` = '%s'", $this->dbTable, $this->dbConnection->escape_string($userId));
        return $this->dbConnection->query($sql);
    }

    public function gc($max)
    {
        $sql = sprintf("DELETE FROM %s WHERE `time_created` < '%s'", $this->dbTable, time() - intval($this->timeExpire));
        return $this->dbConnection->query($sql);
    }

    function getDataByUserId($userId) {
        $sql = sprintf("SELECT session_id, data FROM %s WHERE user_id = '%s'", $this->dbTable, $userId);
        if ($result = $this->dbConnection->query($sql)) {
            if ($result->num_rows && $result->num_rows > 0) {
                $record = $result->fetch_assoc();
                return self::unserializeData($record['data']);
            } else {
                return false;
            }
        }else {
            return false;
        }
    }

    function setDataByUserId($userId, $data) {
        $data = self::serializeData($data);
        $sql = sprintf("UPDATE %s SET data='%s' WHERE user_id='%s'", $this->dbTable, $data, $userId);
        return $this->dbConnection->query($sql);
    }

    static function unserializeData($session_data) {
        $return_data = array();
        $offset = 0;
        while ($offset < strlen($session_data)) {
            if (!strstr(substr($session_data, $offset), "|")) {
                throw new Exception("invalid data, remaining: " . substr($session_data, $offset));
            }
            $pos = strpos($session_data, "|", $offset);
            $num = $pos - $offset;
            $varname = substr($session_data, $offset, $num);
            $offset += $num + 1;
            $data = unserialize(substr($session_data, $offset));
            $return_data[$varname] = $data;
            $offset += strlen(serialize($data));
        }
        return $return_data;
    }

    static function serializeData($data){
        //$ret = [];
        $string2Return = '';
        foreach($data as $k=>$v){
            //$ret[] = $k.'|'.serialize($v);
            $string2Return .= $k.'|'.serialize($v);
        }
        return $string2Return;
    }
}
