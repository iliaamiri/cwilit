<?php
namespace Core;

class module{
    protected static $config_tbl_names;
    protected static $main_tbl_names;
    protected static $db;
    protected static $tbl_columns;
    public static $token = false;
    function __construct()
    {
        $witcher = new \witcher();
        self::$config_tbl_names = $witcher->getCoreConfigs("configDb_tables.php");
        self::$main_tbl_names = $witcher->getCoreConfigs("mainDb_tables.php");
        self::$db = new database();
        if (self::$token){
            if (!tokenCSRF::is_set()){
                $t = new tokenCSRF();
            }
        }
    }
    protected function setTblColumnsof($config_file){
        $witcher = new \witcher();
        self::$tbl_columns = $witcher->getModelConfigs($config_file);
    }
}