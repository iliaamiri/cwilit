<?php
/*
 * todo : make functions about json files and users folder stuffs
 *
 * */
namespace Model;

use Core\drjson;
use Core\module;

class userRels extends module {
    private $user_id;
    /* set */
    public function set_user($user_id){
        $this->user_id = $user_id;
    }
    /* /set */


    /* get */
    public function get_followings(){
        $user = new user();
        parent::setTblColumnsof("users.php");
        $user_info = $user->getUserInfoBy(parent::$tbl_columns['info_columns']['id'],$this->user_id)[0];
        $email = $user_info[parent::$tbl_columns['info_columns']['email']];
        $drjson = new drjson();
        $path = DIR_ROOT."/witcher/app/users/".$email."/followings.json";
        return $drjson->get_json($path);
    }
    public function get_followers(){
        $user = new user();
        parent::setTblColumnsof("users.php");
        $user_info = $user->getUserInfoBy(parent::$tbl_columns['info_columns']['id'],$this->user_id)[0];
        $email = $user_info[parent::$tbl_columns['info_columns']['email']];
        $drjson = new drjson();
        $path = DIR_ROOT."/witcher/app/users/".$email."/followers.json";
        return $drjson->get_json($path);
    }
    public function get_blocked_users(){
        $user = new user();
        parent::setTblColumnsof("users.php");
        $user_info = $user->getUserInfoBy(parent::$tbl_columns['info_columns']['id'],$this->user_id)[0];
        $email = $user_info[parent::$tbl_columns['info_columns']['email']];
        $drjson = new drjson();
        $path = DIR_ROOT."/witcher/app/users/".$email."/block_users.json";
        return $drjson->get_json($path);
    }
    public function get_follow_requests(){
        $user = new user();
        parent::setTblColumnsof("users.php");
        $user_info = $user->getUserInfoBy(parent::$tbl_columns['info_columns']['id'],$this->user_id)[0];
        $email = $user_info[parent::$tbl_columns['info_columns']['email']];
        $drjson = new drjson();
        $path = DIR_ROOT."/witcher/app/users/".$email."/follow_requests.json";
        return $drjson->get_json($path);
    }
    public function get_reports(){
        $user = new user();
        parent::setTblColumnsof("users.php");
        $user_info = $user->getUserInfoBy(parent::$tbl_columns['info_columns']['id'],$this->user_id)[0];
        $email = $user_info[parent::$tbl_columns['info_columns']['email']];
        $drjson = new drjson();
        $path = DIR_ROOT."/witcher/app/users/".$email."/reports.json";
        return $drjson->get_json($path);
    }
    /* /get */

    /* true false functions */
    public function did_follow($email){

    }
    public function did_follow_request_to($email){

    }
    public function is_followed_by($email){

    }
    public function is_blocked_by($email){

    }
    /* /true false functions*/

    public function add_follower($follower_id){
        $user = new user();
        parent::setTblColumnsof("users.php");
        $user_info = $user->getUserInfoBy(parent::$tbl_columns['info_columns']['id'],$this->user_id)[0];
        $follower_info = $user->getUserInfoBy(parent::$tbl_columns['info_columns']['id'],$follower_id)[0];
        $user_email = $user_info[parent::$tbl_columns['info_columns']['email']];
        $follower_email = $follower_info[parent::$tbl_columns['info_columns']['email']];
        if ($follower_email == $user_email){
            return false;
        }
        $drjson = new drjson();
        $path = DIR_ROOT."/witcher/app/users/".$user_email."/followers.json";
        $last_followers = $drjson->get_json($path);
        if (in_array($follower_info[parent::$tbl_columns['info_columns']['email']],$last_followers)){
            return false;
        }
        $last_followers[$follower_id] = $follower_email;
        $new_followers = json_encode($last_followers);
        $dir = DIR_ROOT."/witcher/app/users/".$user_email;
        return $drjson->new_json($dir,"followers",$new_followers);
    }
    public function remove_follower($follower_id){
        $user = new user();
        parent::setTblColumnsof("users.php");
        $user_info = $user->getUserInfoBy(parent::$tbl_columns['info_columns']['id'],$this->user_id)[0];
        $follower_info = $user->getUserInfoBy(parent::$tbl_columns['info_columns']['id'],$follower_id)[0];
        $user_email = $user_info[parent::$tbl_columns['info_columns']['email']];
        $follower_email = $follower_info[parent::$tbl_columns['info_columns']['email']];
        $drjson = new drjson();
        $path = DIR_ROOT."/witcher/app/users/".$user_email."/followers.json";
        $last_followers = $drjson->get_json($path);
        if (isset($last_followers[$follower_id])){
            unset($last_followers[$follower_id]);
        }else{
            return false;
        }
        $new_followers = json_encode($last_followers);
        $dir = DIR_ROOT."/witcher/app/users/".$user_email;
        return $drjson->new_json($dir,"followers",$new_followers);
    }
    public function add_following($following_id){
        $user = new user();
        parent::setTblColumnsof("users.php");
        $user_info = $user->getUserInfoBy(parent::$tbl_columns['info_columns']['id'],$this->user_id)[0];
        $follower_info = $user->getUserInfoBy(parent::$tbl_columns['info_columns']['id'],$following_id)[0];
        $user_email = $user_info[parent::$tbl_columns['info_columns']['email']];
        $follower_email = $follower_info[parent::$tbl_columns['info_columns']['email']];
        if ($follower_email == $user_email){
            return false;
        }
        $drjson = new drjson();
        $path = DIR_ROOT."/witcher/app/users/".$user_email."/followings.json";
        $last_followers = $drjson->get_json($path);
        if (in_array($follower_info[parent::$tbl_columns['info_columns']['email']],$last_followers)){
            return false;
        }
        $last_followers[$following_id] = $follower_email;
        $new_followers = json_encode($last_followers);
        $dir = DIR_ROOT."/witcher/app/users/".$user_email;
        return $drjson->new_json($dir,"followings",$new_followers);
    }
    public function remove_following($following_id){
        $user = new user();
        parent::setTblColumnsof("users.php");
        $user_info = $user->getUserInfoBy(parent::$tbl_columns['info_columns']['id'],$this->user_id)[0];
        $following_info = $user->getUserInfoBy(parent::$tbl_columns['info_columns']['id'],$following_id)[0];
        $user_email = $user_info[parent::$tbl_columns['info_columns']['email']];
        $following_email = $following_info[parent::$tbl_columns['info_columns']['email']];
        $drjson = new drjson();
        $path = DIR_ROOT."/witcher/app/users/".$user_email."/followings.json";
        $last_followers = $drjson->get_json($path);
        if (isset($last_followers[$following_id])){
            unset($last_followers[$following_id]);
        }else{
            return false;
        }
        $new_followers = json_encode($last_followers);
        $dir = DIR_ROOT."/witcher/app/users/".$user_email;
        return $drjson->new_json($dir,"followings",$new_followers);
    }
}