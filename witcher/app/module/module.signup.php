<?php
namespace Module;

use Core\module;
use Core\preg;
use Core\tokenCSRF;
use Model\user;

class signupModule extends module{
    public $auth_method = "POST";
    private $request_array;
    private $email;
    private $username;
    private $password;
    private $ret_password;

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

    public function signup(){
        $witcher = new \witcher();
        $messages = $witcher->getExceptionsMessages("authentication.php")['signup'];
        try{
            if (parent::$token){
                if (!isset($this->request_array['token'])){
                    throw new \Exception($messages['token_isnot_set']);
                }
                if (tokenCSRF::get_token() != $this->request_array['token']){
                    throw new \Exception($messages['token_isnot_set']);
                }
            }
            if (!$this->setEmail()['status']){
                if ($this->setEmail()['cause'] == "notset"){
                    throw new \Exception($messages['email_not_found_in_request']);
                }elseif($this->setEmail()['cause'] == "invalid"){
                    throw new \Exception($messages['email_preg_failed']);
                }
            }
            if (!$this->setUsername()['status']){
                if ($this->setUsername()['cause'] == "notset"){
                    throw new \Exception($messages['username_not_found_in_request']);
                }elseif($this->setUsername()['cause'] == "invalid"){
                    throw new \Exception($messages['username_preg_failed']);
                }
            }
            if ($this->isEmailTaken()){
                throw new \Exception($messages['email_already_existing']);
            }
            if ($this->isUsernameTaken()){
                throw new \Exception($messages['username_already_existing']);
            }
            if (!$this->setPassword()['status']){
                if ($this->setPassword()['cause'] == "notset"){
                    throw new \Exception($messages['password_not_found_in_request']);
                }elseif($this->setPassword()['cause'] == "invalid"){
                    throw new \Exception($messages['password_preg_failed']);
                }
            }
            if (!$this->setRetPassword()['status']){
                if ($this->setRetPassword()['cause'] == "notset"){
                    throw new \Exception($messages['repassword_not_found_in_request']);
                }elseif($this->setRetPassword()['cause'] == "invalid"){
                    throw new \Exception($messages['retpassword_preg_failed']);
                }
            }
            if (!$this->setRealPassword()['status']){
                throw new \Exception($messages['password_not_match']);
            }
            return ['status' => true];
        }catch (\Exception $e){
            if (parent::$token){
                $token = new tokenCSRF();
            }
            return ['status' => false,'message' => $e->getMessage()];
        }
    }
    public function add_user(){
        if ($this->signup()['status']){
            $user = new user();
            $data = [
                'Username' => $this->username,
                'Password' => $this->password,
                'Email' => $this->email,
            ];
            $user->AddUser($data);
        }
    }
    public function setEmail(){
        if (isset($this->request_array['email'])){
            $max_lentgh = 250;
            $min_lentgh = 4;
            $preg = new preg();
            $email = $this->request_array['email'];
            if (strlen($email) > $min_lentgh and strlen($email) <= $max_lentgh) {
                if ($preg->push_email($email)) {
                    $this->email = $email;
                    return ['status' => true];
                }else{
                    return ['status' => false,'cause' => 'invalid'];
                }
            }else{
                return ['status' => false,'cause' => 'invalid'];
            }
        }elseif (!isset($this->request_array['email'])){
            return ['status' => false,'cause' => 'notset'];
        }
    }
    public function setUsername(){
        if (isset($this->request_array['username'])){
            $max_lentgh = 250;
            $min_lentgh = 1;
            $preg = new preg();
            $username = $this->request_array['username'];
            if (strlen($username) > $min_lentgh and strlen($username) <= $max_lentgh) {
                if ($preg->push($username,'username')) {
                    $this->username = $username;
                    return ['status' => true];
                }else{
                    return ['status' => false,'cause' => 'invalid'];
                }
            }else{
                return ['status' => false,'cause' => 'invalid'];
            }
        }elseif (!isset($this->request_array['username'])){
            return ['status' => false,'cause' => 'notset'];
        }
    }
    public function setPassword(){
        if (isset($this->request_array['password'])){
            $max_lentgh = 250;
            $min_lentgh = 1;
            $preg = new preg();
            $password = $this->request_array['password'];
            if (strlen($password) > $min_lentgh and strlen($password) <= $max_lentgh) {
                if ($preg->push($password,'password')) {
                    $this->password = $password;
                    return ['status' => true];
                }else{
                    return ['status' => false,'cause' => 'invalid'];
                }
            }else{
                return ['status' => false,'cause' => 'invalid'];
            }
        }elseif (!isset($this->request_array['password'])){
            return ['status' => false,'cause' => 'notset'];
        }
    }
    public function setRetPassword(){
        if (isset($this->request_array['ret_password'])){
            $max_lentgh = 250;
            $min_lentgh = 1;
            $preg = new preg();
            $password = $this->request_array['ret_password'];
            if (strlen($password) > $min_lentgh and strlen($password) <= $max_lentgh) {
                if ($preg->push($password,'password')) {
                    $this->ret_password = $password;
                    return ['status' => true];
                }else{
                    return ['status' => false,'cause' => 'invalid'];
                }
            }else{
                return ['status' => false,'cause' => 'invalid'];
            }
        }elseif (!isset($this->request_array['ret_password'])){
            return ['status' => false,'cause' => 'notset'];
        }
    }
    public function setRealPassword(){
        if ($this->ret_password == $this->password){
            return ['status' => true];
        }else{
            return ['status' => false,'cause' => 'psswd_notmatch'];
        }
    }
    public function isUsernameTaken(){
        $user = new user();
        return $user->user_exist($this->username) ? true : false;
    }
    public function isEmailTaken(){
        $user = new user();
        return $user->email_exist($this->email) ? true : false;
    }
}