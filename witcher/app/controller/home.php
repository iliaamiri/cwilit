<?php
namespace Controller;

use Model\db;
use Model\interfaces;
use Model\server;
use Model\views;
use Config\tables;
class home extends views {
    public function start(){
        parent::setData(['mmmmm']);
        parent::setViews(["test.php"]);
        parent::Show();
    }
    public function getData(){
        return parent::$data;
    }
    public function getViews(){
        return parent::$views;
    }
}