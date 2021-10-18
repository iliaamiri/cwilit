<?php

namespace Core;


class drjson
{
    public function new_json($dir,$filename,$content){
        if (file_exists($dir)){
            $file = fopen($dir."/".$filename.".json","w");
            if (!$file){
                return false;
            }
            fwrite($file,$content);
            fclose($file);
            return true;
        }else{
            return false;
        }
    }
    public function get_json($path){
        if (file_exists($path)){
            $content = file_get_contents($path);
            return json_decode($content,true);
        }else{
            return false;
        }
    }
}