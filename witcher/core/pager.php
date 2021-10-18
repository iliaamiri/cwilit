<?php
namespace Core;

class pager{
    private static $pages;
    function __construct()
    {
        $witcher = new \witcher();
        self::$pages = $witcher->getCoreConfigs("pages.php");
    }

    public static function go_page($page){
        $witcher = new \witcher();
        self::$pages = $witcher->getCoreConfigs("pages.php");
        //header_remove("location:".$page);
        if (array_key_exists($page,self::$pages))
            header("location:".self::$pages[$page]);
        else
            header("location:".self::$pages['404']);
    }
    public static function redirect_page($time,$page){
        header("refresh:".$time.";"."url=".$page);
    }
    public function include_page($page){
        $path = $_SERVER['DOCUMENT_ROOT'];
        $path .= $page;
        include_once($path);
    }
    public static function refresh(){
        header("refresh:0;");
    }
}