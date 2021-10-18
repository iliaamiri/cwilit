<?php
namespace Controller;

use Core\preg;
use Model\user;
use Model\views;
use Module\loginModule;

class ajax extends views {
    private static $method = "GET";
    private static $index_of_method = "ajax_req";
    private static $index_of_object = "object";
    private static $ajax_command;
    private static $ajax_object;

    private static $logIn_Status = false;
    private static $user_permissions;
    private static $user_info;
    function __construct()
    {
        $login_module = new loginModule();
        if ($login_module->is_login()){
            self::$logIn_Status = true;
            $user = new user();
            self::$user_permissions = $user->user_get_permission();
            self::$user_info = $user->user_get_certificate();
        }
        switch (self::$method){
            case "GET":
                $request = $_GET;
                self::$ajax_command = $_GET[self::$index_of_method];
                self::$ajax_object = $_GET[self::$index_of_object];
                break;
            case "POST":
               /* self::$ajax_command = $_POST[self::$index_of_method];
                self::$ajax_object = $_POST[self::$index_of_object]; */
                $request = $_POST;
                break;
            default:
                die("unknown ajax request");
                break;
        }
        if (!isset($request[self::$index_of_method])){
            die("failed to catch command");
        }elseif (!isset($request[self::$index_of_object])){
            die("failed to catch object");
        }

        self::$ajax_command = $request[self::$index_of_method];
        self::$ajax_object = $request[self::$index_of_object];

        if (!isset(self::$ajax_command['parent'])){
            die("failed to catch parent");
        }elseif (!isset(self::$ajax_command['child'])){
            die("failed to catch child");
        }
    }

    public function start(){
        $commands_list = $this->get_available_commands();
        if (!array_key_exists(self::$ajax_command['parent'],$commands_list)){
            die("command not found");
        }elseif (!isset($commands_list[self::$ajax_command['parent']][self::$ajax_command['child']])){
            die("command not found");
        }


    }

    private function get_available_commands(){
        /* true | false meaning(finglish) :
                vaziate ejaze dashtane Ajax Controller baraye estefade kardan dastoore morede nazar.
        */
        return array(

            'user_rel_modules' =>
                array(
                    'follow' => true,
                    'unfollow' => true,
                    'delete_follower' => true,
                    'report' => true,
                    'block' => true,
                    'turn_notifications_on' => true,
                    'turn_notifications_off' => true,
                    'pv_chat_request' => true
                ),


            'post_modules' =>
                array(
                    'new_post' => true,
                    'edit_post' => true,
                    'like_post' => true,
                    'seen_post' => true,
                    'delete_post' => true,
                    'dislike_post' => true,
                    'report_post' => true,
                    'block_post' => true
                ),


            'user_auth_modules' =>
                array(
                    'login_request' => true,
                    'signup_request' => true,
                    'fill_fullname' => true
                )

        );
    }
}