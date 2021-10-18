<?php
namespace Controller;

use Core\pager;
use Model\message;
use Model\userRels;
use Model\views;
use Module\loginModule;
use Module\signupModule;

class welcome extends views {

    function __construct()
    {
        $witcher = new \witcher();
        $witcher->requireModules();
    }

    public function start(){
        /*$test = new userRels();
        $test->set_user('1');
        var_dump($test->remove_following('2'));
        die();*/
        $login_module = new loginModule();
        $signup_module = new signupModule();
        if ($login_module->is_login()){
            $views_array =  array("home.php");
        }else{
            $views_array =  array("welcome.php");
            $req_method = $_SERVER['REQUEST_METHOD'];
            if ($req_method == "POST") {
                $request = $_POST;
                if (!isset($request['action'])){
                    pager::refresh();
                    exit();
                }
                switch ($request['action']){
                    case "login":
                        $login_stat = $login_module->login();
                        if (!$login_stat['status']){
                            message::msg_box_session_prepare($login_stat['message'],"danger");
                            pager::refresh();
                            exit();
                        }
                        $login_module->after_login_changes();
                        pager::refresh();
                        break;
                    case "signup":
                        $witcher = new \witcher();
                        $excpetion_messages = $witcher->getExceptionsMessages('authentication.php');
                        $signup_stat = $signup_module->signup();
                        if (!$signup_stat['status']){
                            message::msg_box_session_prepare($signup_stat['message'],"danger");
                            pager::refresh();
                            exit();
                        }
                        $signup_module->add_user();
                        message::msg_box_session_prepare($excpetion_messages['signup']['success'],"success");
                        pager::refresh();
                        break;
                }
            }
        }
        parent::setViews($views_array);
        parent::Show();
    }
}