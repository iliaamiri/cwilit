<?php
namespace Core;

class preg extends configs {

    private $preg_types;
    function __construct()
    {
        parent::set_config("preg_types.php");
        $this->preg_types = parent::$configs;
    }

    public function push($value,$preg_type){
        if (!isset($this->preg_types[$preg_type])){
            return false;
        }
        if (preg_match($this->preg_types[$preg_type],$value)){
            return true;
        }else{
            return false;
        }
    }
    public function push_email($value){
        if (filter_var($value, FILTER_VALIDATE_EMAIL)){
            if (preg_match('/^[a-zA-Z0-9_.-@+,]*$/i',$value)){
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }
    public function push_custom($pattern,$value){
        if (preg_match($pattern,$value)){
            return true;
        }else{
            return false;
        }
    }
}