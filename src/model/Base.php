<?php
namespace model;

class Base {

    protected $db = null;
    
    function __construct() {
        global $dbs;
        $this->db = \lib\DB::get($dbs['main']);
    }

}
