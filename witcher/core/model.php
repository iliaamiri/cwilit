<?php
namespace Core;

class model{
    protected static $config_tbl_names;
    protected static $main_tbl_names;
    protected static $db;
    function __construct()
    {
        $witcher = new \witcher();
        self::$config_tbl_names = $witcher->getCoreConfigs("configDb_tables.php");
        self::$main_tbl_names = $witcher->getCoreConfigs("mainDb_tables.php");
        self::$db = new database();
    }
}