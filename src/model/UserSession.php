<?php
namespace model;

class UserSession extends Base implements \ArrayAccess{

    private $expireSeconds = 2592000;
    private $session = array();
    private static $sessionObj = null;

    static function get($sessionId) {
        if(self::$sessionObj == null) {
            self::$sessionObj = new UserSession($sessionId);
        }
        return self::$sessionObj;
    }

    function __construct($sessionId) {
        parent::__construct();
        $sessionInfo = $this->db->fetchRow("SELECT session_id, user_id, time_updated, is_deleted FROM user_session WHERE session_id=? AND is_deleted=0 ", [$sessionId]);
        $isExpired = !empty($sessionInfo['session_id']) && time() - $sessionInfo['time_updated'] >= $this->expireSeconds;
        if($isExpired) {
            $this->db->execute("UPDATE user_session SET time_updated=?, is_deleted=1 WHERE session_id=?", [time(), $sessionId]);
        }
        if(empty($sessionInfo['session_id']) || $isExpired) {
            $sessionId = md5(uniqid());
            $this->db->insert("INSERT INTO user_session SET session_id=?, time_created=?, time_updated=? ", [$sessionId, time(), time()]);
            $sessionInfo = array('session_id' => $sessionId);
            setcookie("session_id", $sessionId, time() + $this->expireSeconds);
        }else {
            $timeNow = time();
            $this->db->execute("UPDATE user_session SET time_updated=? WHERE session_id=?", [$timeNow, $sessionId]);
        }
        $this->session = $sessionInfo;
    }

    function __get($key) {
        $value = $this->session[$key];
        return $value;
    }

    function __set($key, $value) {
        $this->db->execute("UPDATE user_session SET $key=? WHERE session_id=?", [$value, $this->session['session_id']]);
        $this->session[$key] = $value;
        return true;
    }

    public function offsetSet($key, $value) {
        if (is_null($key)) {
        } else {
            $this->session[$key] = $value;
            $this->db->execute("UPDATE user_session SET $key=? WHERE session_id=?", [$value, $this->session['session_id']]);
        }
    }

    public function offsetExists($key) {
        return isset($this->session[$key]);
    }

    public function offsetUnset($key) {
        unset($this->session[$key]);
    }

    public function offsetGet($key) {
        return isset($this->session[$key]) ? $this->session[$key] : null;
    }
}
