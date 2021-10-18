<?php
namespace Core;


class session extends configs
{
    private $session_name = "PHPSSID";
    private $session_secure = true;
    private $session_httponly = true;
    private $session_path;
    private $session_limit;
    function __construct()
    {
        parent::set_config("session.php");
        $this->auto_setter();
        session_name($this->session_name);
        session_set_cookie_params($this->session_limit, $this->session_path, $_SERVER['SERVER_NAME'],$this->session_secure, $this->session_httponly);
        session_start();
    }

    private function auto_setter(){
        $config = parent::$configs;
        $this->session_name = $config['session_name'];
        $this->session_secure = $config['session_secure'];
        $this->session_httponly = $config['session_httponly'];
        $this->session_path = $config['session_path'];
        $this->session_limit = $config['session_limit'];
    }
}