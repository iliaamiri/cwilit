<?php

namespace Core;

class configs{
    private static $config_file;
    protected static $configs;
    public static $exceptionsMessages;
    public static function set_config($path){
        self::$config_file = $path;
        $witcher = new \witcher();
        self::$configs = $witcher->getCoreConfigs(self::$config_file);
    }
    public function set_exceptionsMessages_config($path){
        $witcher = new \witcher();
        self::$exceptionsMessages = $witcher->getCoreConfigs("ExceptionsMessages/".$path);
    }
}