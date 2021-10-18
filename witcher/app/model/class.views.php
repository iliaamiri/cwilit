<?php
namespace Model;

class views {
    public static $views;
    public static $data;

    public static function setViews($array){
        return self::$views = $array;
    }
    public static function setData($array){
        return self::$data = $array;
    }
    function Show(){
        $stat = array();
        $witcher = new \witcher();
        foreach (self::$views as $views){
            $fullpath = $witcher->root()."witcher/view/".$views;
            if (file_exists($fullpath)){
                $witcher->requireView($views);
                $stat = array_merge([$views => "Exists"],$stat);
            }else{
                $stat = array_merge([$views => "Does not exist."],$stat);
            }
        }
        return $stat;
    }
    function ErrorHandler($code){
        switch ($code){
            case "404":
               $array = array($this->setPage("errors/404.php"));
               return $this->Show($array);
               break;
            case "403":
                $array = array($this->setPage("errors/403.php"));
                return $this->Show($array);
                break;
        }
    }
}