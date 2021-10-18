<?php
namespace Module;

use Core\database;
use Core\model;
use Core\module;
use Core\preg;
use Core\tokenCSRF;
use Model\user;

class loginModule extends module {
    public $auth_method = "POST";
    private $password_requires = true;
    private $use_email_for_login = false;
    private $request_array;
    private $login;
    private $password;


    function __construct()
    {
        parent::$token = true;
        parent::__construct();
        switch ($this->auth_method){
            case "POST":
                $this->request_array = $_POST;
                break;
            case "GET":
                $this->request_array = $_GET;
                break;
        }
    }

    public function login(){
        $witcher = new \witcher();
        $messages = $witcher->getExceptionsMessages("authentication.php")['login'];
        try{
            if (parent::$token){
                if (!isset($this->request_array['token'])){
                    throw new \Exception($messages['token_isnot_set']);
                }
                if (tokenCSRF::get_token() != $this->request_array['token']){
                    throw new \Exception($messages['token_isnot_set']);
                }
            }
            if (!$this->set_login()['status']){
                if ($this->set_login()['cause'] == "notset"){
                    throw new \Exception($messages['username_not_found_in_request']);
                }elseif($this->set_login()['cause'] == "invalid"){
                    throw new \Exception($messages['username_preg_failed']);
                }
            }
            if (!$this->authenticate_login()){
                throw new \Exception($messages['username_not_found_in_tables']);
            }
            if (!$this->set_password()['status']){
                if ($this->set_password()['cause'] == "notset"){
                    throw new \Exception($messages['password_not_found_in_request']);
                }elseif($this->set_password()['cause'] == "invalid"){
                    throw new \Exception($messages['password_preg_failed']);
                }
            }
            if (!$this->true_password()){
                throw new \Exception($messages['password_not_match']);
            }
            if (!$this->can_login()){
                throw new \Exception($messages['cannot_login']);
            }
            return ['status' => true];
        }catch (\Exception $e){
            if (parent::$token){
                $token = new tokenCSRF();
            }
            return ['status' => false,'message' => $e->getMessage()];
        }
    }
    public function after_login_changes(){
        $user = new user();
        $_SESSION['Login'] = $this->login;
        if ($this->password_requires) {
            $_SESSION['Password'] = md5(sha1(md5($this->password)));
        }
        $_SESSION['Certificate_Code'] = md5(sha1(md5(sha1(md5(sha1(md5(rand(1000,9999))))))));
        $Last_ip = $_SERVER['REMOTE_ADDR'];
        $Last_login = date("Y/m/d h:i:sa");
        parent::setTblColumnsof("users.php");
        $clms = parent::$tbl_columns['info_columns'];
        $statement = $clms['session_id']." = '".$_SESSION['Certificate_Code']."' ,".$clms['last_ip']." = '".$Last_ip."' , ".$clms['last_browser']." = 'unknown' WHERE ".$clms['username']." = '".$this->login."' OR ".$clms['email']." = '".$this->login."'";
        return $user->UpdateUserTblCustom($statement);
    }
    public function logout(){
        $user = new user();
        $user_info = $user->user_get_certificate();
        $witcher = new \witcher();
        $columns = $witcher->getModelConfigs("users.php");
        $where = " ".$columns['info_columns']['session_id']." = NULL , ".$columns['info_columns']['log']." = 0 WHERE ".$columns['info_columns']['email']." = '".$user_info[$columns['info_columns']['email']]."'";
        return ($user->UpdateUserTblCustom($where)) ? true : false;
    }

    public function set_login(){
        $preg = new preg();
        $max_len = 250;
        $min_len = 1;
        $login = $this->request_array['login'];
        if (isset($login) and strlen($login) < $max_len and strlen($login) > $min_len){
            if ($this->use_email_for_login){
                if ($preg->push_email($login) or $preg->push($login,'username')){
                    $this->login = $login;
                    return ['status' => true];
                }else{
                    return ['status' => false,'cause' => 'invalid'];
                }
            }else{
                if ($preg->push($login,'username')){
                    $this->login = $login;
                }else{
                    return ['status' => false,'cause' => 'invalid'];
                }
            }
        }else{
            return ['status' => false,'cause' => 'notset'];
        }
    }
    public function can_login(){
        $user = new user();
        parent::setTblColumnsof("users.php");
        $email = $this->authenticate_login()[0][parent::$tbl_columns['perm_columns']['email']];
        $permission = $user->get_permissions_custom("WHERE ".parent::$tbl_columns['info_columns']['email']." = '".$email."'")[0];
        if ($permission[parent::$tbl_columns['perm_columns']['role_id']] > -1 and $permission[parent::$tbl_columns['perm_columns']['login']] == 1){
            return true;
        }else{
            return false;
        }
    }
    public function set_password(){
        $preg = new preg();
        $max_len = 300;
        $min_len = 1;
        $password = $this->request_array['password'];
        if (isset($password) and strlen($password) < $max_len and strlen($password) > $min_len ){
            if ($preg->push($password,'password')){
                $this->password = $password;
                return ['status' => true];
            }else{
                return ['status' => false,'cause' => 'invalid'];
            }
        }else{
            return ['status' => false,'cause' => 'notset'];
        }
    }

    public function authenticate_login(){
        if ($this->login != null){
            $users = new user();
            if ($users->user_exist($this->login)){
                return $users->getUserInfoBy('username',$this->login);
            }elseif ($this->use_email_for_login and count($users->getUserInfoBy('email',$this->login)) > 0){
                return $users->getUserInfoBy('email',$this->login);
            }else{
                return false;
            }
        }else{
            return false;
        }
    }
    public function true_password(){
        if ($this->login != null and $this->password != null){
            if (!$this->authenticate_login()){
                return false;
            }
            $password = md5(sha1(md5($this->password)));
            $loginer = $this->authenticate_login()[0];
            if ($loginer['Password'] != $password){
                return false;
            }
            return true;
        }else{
            return false;
        }
    }
    public function is_login(){
        $preg = new preg();
        $user = new user();
        if (isset($_SESSION['Certificate_Code']) AND isset($_SESSION['Login']) AND isset($_SESSION['Password'])){
            $username = $_SESSION['Login'];
            $password = $_SESSION['Password'];
            $preg_username = $preg->push($username,'username');
            $preg_email = $preg->push_email($username);
            $preg_password = $preg->push($password,'password');
            if ($preg_password AND ($preg_username OR $preg_email)){
                    parent::setTblColumnsof("users.php");
                    $check = $user->users_custom_select("WHERE (".parent::$tbl_columns['info_columns']['username']." = '".$username."' OR ".parent::$tbl_columns['info_columns']['email']." = '".$username."') AND ".parent::$tbl_columns['info_columns']['password']." = '".$password."' AND ".parent::$tbl_columns['info_columns']['session_id']." = '".$_SESSION['Certificate_Code']."'");
                    if ($check->rowCount() == 1){
                        return true;
                    }else{
                        return false;
                    }
            }
            else{
                return false;
            }
        }
        elseif (!isset($_SESSION['Certificate_Code']) OR !isset($_SESSION['Login']) OR !isset($_SESSION['Password'])){
            return false;
        }
    }
}