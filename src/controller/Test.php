<?php
namespace controller;

class Test extends Base{

    function output() {
        $this->session['user_id'] = '1234';
        print_r($this->session['user_id'] . ' ');
        print_r($this->session['session_id']);
        die;
        $testModel = new \model\Test();
        $result = $testModel->test();
        var_dump($result);
        echo 'test/output success';
    }
}
