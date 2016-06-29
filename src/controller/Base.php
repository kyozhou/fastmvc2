<?php
namespace controller;

class Base {

    protected $session = null;

    function __construct() {
        $this->sessionHandle();
        cleanXss($_POST);
        cleanXss($_GET);
    }
    
    private function sessionHandle() {
        if(empty($this->session)) {
            $requestArgs = array_merge($_GET, $_POST, $_COOKIE);
            $sessionId = empty($requestArgs['session_id']) ? '' : $requestArgs['session_id'];
            $this->session = \model\UserSession::get($sessionId);
        }
        //$this->session = array();
    }

    function checkLogin($type = 'json', $role = null) {
        if (empty($this->defaultValues['user']) || $this->defaultValues['user']['id'] == 0) {
            if($type == 'json'){
                $this->dieJSON(array('error' => 0, 'errno' => 'user_not_login'));
            }elseif($type == 'bool'){
                return false;
            }elseif($type == 'redirect'){
                $this->redirect(PASSPORT_URL);
            }else {
                return false;
            }
        }elseif($role != null && $this->defaultValues['user']['role'] != $role){
            if($type == 'json'){
                $this->dieJSON(array('error' => 0, 'errno' => 'wrong_role'));
            }elseif($type == 'redirect'){
                $this->redirect(PASSPORT_URL);
            }else {
                return false;
            }
        }else {
            return true;
        }
    }

    
}
