<?php
namespace model;

class Test extends Base{
    
    function test() {
        $data = $this->db->fetchRow("SELECT * FROM user");
        return $data;
    }
}
