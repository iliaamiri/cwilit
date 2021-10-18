<?php

namespace Core;

class log extends configs {
    private $log_file;
    private $log_message;
    function __construct()
    {
        parent::set_config("log.php");
    }
    public function set_log_file($file_name){
        $this->log_file = DIR_ROOT."storage/logs/".$file_name;
    }
    public function set_log_message($message){
        $this->log_message = $message;
    }
    public function create_log(){
        $file = fopen($this->log_file,"a");
        $configs = parent::$configs;
        $date_log = "";
        if ($configs['dates']['status']){
            $date_log .= date("Y-m-d");
            if ($configs['dates']['log_day']){
                $date_log .= " ".date("D");
            }
            if ($configs['dates']['log_at_least_second']){
                $date_log .= " ".date("H:i:s");
            }
        }
        if ($configs['log_http_response_code']){
            $date_log .= " |HTTP_RESPONSE: ".http_response_code();
        }
        fwrite($file,$date_log." | ".$this->log_message."\r\n");
        fclose($file);
    }
}