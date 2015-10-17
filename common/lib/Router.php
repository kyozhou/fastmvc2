<?php
namespace lib;
class Router {
    
    private $root = null;
    private $splitString = '-';

    function __construct($appRoot) {
        $this->root = $appRoot;
    }

    function action() {
        $request = array_merge($_GET, $_POST);
        unset($request['c']);
        
        $controllerName = !empty($_GET['c']) ? $_GET['c'] : 'index/index';

        $controllerArray = explode('/', $controllerName);
        $controllerClass = !empty($controllerArray[0]) ? $controllerArray[0] : 'index';
        $this->nameFormat($controllerClass, true);
        $controllerClass = file_exists($this->root . "/controller/$controllerClass.php") ? $controllerClass : 'Index';

        $controllerClassFile = $this->root . '/controller/' . $controllerClass . '.php';
        if(file_exists($controllerClassFile)) {
            include $controllerClassFile;
            $controllerMethod = !empty($controllerArray[1]) ? $controllerArray[1] : 'index';
            $this->nameFormat($controllerMethod, false);
            if (strtolower($controllerClass) === strtolower($controllerMethod) || in_array(strtolower($controllerMethod), array('list', 'print'))) {
                $controllerMethod = $this->splitString . $controllerMethod;
            }
            $controllerClass = "\\controller\\$controllerClass";
            $controller = new $controllerClass();
            if (method_exists($controller, $controllerMethod)) {
                unset($_GET['c']);
                $controller->$controllerMethod();
            } else {
                die('action not exists');
            }
        }else {
            die('action not exists');
        }
    }

    //index_list -> IndexList
    private function nameFormat(&$name, $isClass = true) {
        $nameArray = explode($this->splitString, $name);
        $nameTemp = '';
        foreach ($nameArray as $index => $namePart) {
            if ($index === 0 && !$isClass) {
                $nameTemp .= $namePart;
            } else {
                $nameTemp .= ucfirst($namePart);
            }
        }
        $name = $nameTemp;
    }
}
