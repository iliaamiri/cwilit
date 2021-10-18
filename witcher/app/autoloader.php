<?php

class witcher{
    public $environment = "development";
    public function Run(){
        $this->preRun();
        ob_flush();
        date_default_timezone_set('Asia/Tehran');
    }
    public function Stop(){
        session_destroy();
        ob_flush();
        echo "server has stopped serving to this ip.";
        die();
    }
    public function unsetSession($custom = array()){
        if (count($custom) > 0){
            foreach ($custom as $index){
                if (isset($_SESSION[$index]))
                    unset($_SESSION[$index]);
            }
        }
    }
    public function requireclass($pathh){
        $path = $pathh;
        include_once($path);
    }
    private function requireModel(){
        $dirs = scandir(DIR_MODELS);
        foreach ($dirs as $classes) {
            $a= explode('.', $classes);
            $end = end($a);
            if ($end === "php") {
                require_once (DIR_MODELS.$classes);
            }
        }
    }
    private function requireCoreClasses(){
        $dirs = scandir(DIR_CORE);
        foreach ($dirs as $classes) {
            $a= explode('.', $classes);
            $end = end($a);
            if ($end === "php") {
                require_once (DIR_CORE.$classes);
            }
        }
    }
    public function getCoreConfigs($config_path){
        return include DIR_CORE."config/".$config_path;
    }
    public function getExceptionsMessages($config_path){
        return include DIR_CORE."config/ExceptionsMessages/".$config_path;
    }
    public function getModelConfigs($config_path){
        return include DIR_MODELS."configs/".$config_path;
    }
    public function requireController($class){
        $path = $this->root()."witcher/app/controller/".$class.".php";
        require_once($path);
    }
    public function requireModules($path = "/"){
        $p = $this->root()."witcher/app/module".$path;
        $files = scandir($p);
        foreach ($files as $file ){
            $exp = explode(".",$file);
            $end = end($exp);
            if ($end === "php"){
                require_once $p.$file;
            }
        }
    }
    public function startController($class,$details){
        // $details is an array , First index say that if start() function has any arguman or not.
        // Second index is filled by value of the start() function's arguman.
        $controller_path = $this->root()."witcher/app/controller/".$class.".php";
        if (file_exists($controller_path)){
            $name = "\Controller\ ".$class;
            $name = str_replace(" ","",$name);
            $object = new $name;
            if ($details[0] AND isset($details[1])){
                $object->start($details[1]);
            }else{
                $object->start();
            }
        }
    }
    public function requireView($dir){
        $path = $this->root()."witcher/view/".$dir;
        require_once($path);
    }

    private function requirePlugins(){
        $files = scandir($this->root()."witcher/app/plugin");
        foreach ($files as $classes) {
            $a= explode('.', $classes);
            $end = end($a);
            if ($end === "php") {
                require_once $this->root()."witcher/app/plugin/".$classes;
            }
        }
    }
    public function requirePlugin($path){
        require_once $this->root()."witcher/app/plugin/".$path;
    }
    private function preRun(){
        ob_start();
        switch ($this->environment){
            case "development":
                error_reporting(-1);
                ini_set('display_errors', 1);
                break;
            case "serving":
                ini_set('display_errors', 0);
                break;
            default:
                header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
                echo 'The application environment is not set correctly.';
                exit(1);
                break;
        }
        $this->requireCoreClasses();
    }
    public function DownWithCookie($cookie,$cookies = array()){
        if (!empty($cookie)){
            setcookie($cookie,null,time() - 3600,'/');
        }
        elseif (count($cookies) > 0 AND empty($cookie)){
            foreach ($cookies as $cookie_name)
                setcookie($cookie_name,null,time() - 3600,'/');
        }
    }
    public function root(){
        return DIR_ROOT;
    }
    public function load(){
        $this->requireModel();
        $this->requirePlugins();
        $session = new \Core\session();
        $_GET = array_unique($_GET);
        if (isset($_GET['DIR']) AND isset($_GET['CERTIFY_CODE']) AND $_GET['DIR'] != "" AND $_GET['CERTIFY_CODE'] != ""){
            if ($_GET['DIR'] == "" OR $_GET['CERTIFY_CODE'])
                $Certify_Code = $_GET['CERTIFY_CODE'];
            $CODE = md5(sha1(sha1(md5(sha1("AAKKDDR)OO84648846O6546O654O!2d1656464ODL8652312582568869720423105")))));
            if ($Certify_Code !== $CODE){
                $this->requireController("welcome");
                $home = new \Controller\welcome();
                $home->start();
                exit();
            }
            $preg = new \Core\preg();
            if (!$preg->push_custom('/^[A-Za-z0-9.]*$/i',$_GET['DIR'])){
                $this->requireController("welcome");
                $home = new \Controller\welcome();
                $home->start();
                exit();
            }
            $path = $this->root()."witcher/app/controller/".$_GET['DIR'];
            if (file_exists($path)){
                require_once $path;
                $classname = explode(".",$_GET['DIR']);
                $name = "\Controller\ ".$classname[0];
                $name = str_replace(" ","",$name);
                $object = new $name;
                $object->start();
            }else{
                $this->requireController("welcome");
                $home = new \Controller\welcome();
                $home->start();
                exit();
            }
        }elseif (!isset($_GET['DIR']) OR !isset($_GET['CERTIFY_CODE']) OR $_GET['DIR'] == "" OR $_GET['CERTIFY_CODE'] == "") {
            $this->requireController("welcome");
            $home = new \Controller\welcome();
            $home->start();
            exit();
        }
    }
}
