<?php

namespace Core;


class tokenCSRF extends configs
{
    function __construct()
    {
        parent::set_config("token.php");
        $this->set_token();
    }
    private function set_token(){
        if (parent::$configs['encryption_key'] == "AUTO"){
            $salt = 'SUPER_SALTY';
            retry:
            $hash =
            sha1(md5(sha1(md5(sha1(md5(sha1(rand(1000,9999)),$salt)),$salt)),$salt));
            if (isset($_SESSION[parent::$configs['session_index_name']]) and $_SESSION[parent::$configs['session_index_name']] == $hash){
                goto retry;
            }
            $_SESSION[parent::$configs['session_index_name']] = $hash;
        }else{
            // encrypt process
            die();
        }
    }
    public static function is_set(){
        parent::set_config("token.php");
        $index = trim(parent::$configs['session_index_name']);
        if (isset($_SESSION[$index])){
            return true;
        }elseif (!isset($_SESSION[$index])){
            return false;
        }
    }
    public static function get_token(){
        parent::set_config("token.php");
        $index = trim(parent::$configs['session_index_name']);
        return $_SESSION[$index];
    }
}
