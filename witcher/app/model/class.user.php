<?php
namespace Model;

use Core\drjson;
use Core\model;
use Core\preg;
use Core\database;

class user extends model {
    private static $columns;
    function __construct()
    {
        parent::__construct();
        $witcher = new \witcher();
        self::$columns = $witcher->getModelConfigs("users.php");
    }
    private static $permission;
    public $Image_src_target = "panel/src/img";
    
    
    public function getActiveUsers(){
        $db = parent::$db;
        $table = array(parent::$main_tbl_names['users'],parent::$main_tbl_names['users_permissions']);
        $sql = $db->mdb_query("SELECT $table[0].*,$table[1].* FROM $table[0] LEFT JOIN $table[1] ON $table[0].".self::$columns['info_columns']['email']." = $table[1].".self::$columns['perm_columns']['email']." WHERE $table[1].".self::$columns['perm_columns']['role_id']." = 0",1);
        return $sql->fetchAll(\PDO::FETCH_ASSOC);
    }
    public function getUserInfoBy($column,$value){
        $db = parent::$db;
        $table = parent::$main_tbl_names['users'];
        $column = self::$columns['info_columns'][$column];
        $sql = $db->mdb_query("SELECT * FROM $table WHERE $column = '$value'",1);
        return $sql->fetchAll(\PDO::FETCH_ASSOC);
    }
    public function getAll(){
        $db = parent::$db;
        $table = [parent::$main_tbl_names['users'],parent::$main_tbl_names['users_permissions']];
        $sql = $db->mdb_query("SELECT $table[0].*, $table[1].* FROM $table[0] INNER JOIN $table[1] ON $table[0].Email = $table[1].Email",1);
        return $sql->fetchAll(\PDO::FETCH_ASSOC);
    }
    public function getWhoHasWallet(){
        $db = parent::$db;
        $table = new tables();
        $table = [$table->MAIN_TABLES['user'],$table->MAIN_TABLES['Wallet_tbl']];
        $wallet_tbl = $table[1];
        $user_tbl = $table[0];
        $sql = $db->mdb_query("SELECT $user_tbl.* , $wallet_tbl.* FROM $wallet_tbl LEFT JOIN $user_tbl ON $wallet_tbl.Email = $user_tbl.Email",1);
        return $sql->fetchAll(\PDO::FETCH_ASSOC);
    }
    public function getUserRoleCats($Email){
        $db = parent::$db;
        $table = new tables();
        $table = [$table->MAIN_TABLES['permissions'],$table->MAIN_TABLES['Roles']];
        $sql = $db->mdb_query("SELECT $table[1].* FROM $table[0] RIGHT JOIN $table[1] ON $table[0].role_id = $table[1].Role_Id WHERE $table[0].Email = '$Email'",1);
        return $sql->fetch(\PDO::FETCH_ASSOC);
    }
    public function users_custom_select($statements = ""){
        $db = parent::$db;
        $table = parent::$main_tbl_names['users'];
        $sql = $db->mdb_query("SELECT * FROM $table $statements",1);
        return $sql;
    }
    public function get_permissions_custom($statements = ""){
        $db = parent::$db;
        $table = parent::$main_tbl_names['users_permissions'];
        $sql = $db->mdb_query("SELECT * FROM $table $statements",1);
        return $sql->fetchAll(\PDO::FETCH_ASSOC);
    }
    public function user_get_certificate(){
        $preg = new preg();
        if (isset($_SESSION['Certificate_Code'])){
            $preg_code = $preg->push_custom('/^[a-z0-9]*$/i',$_SESSION['Certificate_Code']);
            if ($preg_code){
                $db = new database();
                $table = parent::$main_tbl_names['users'];
                $sql = $db->mdb_query("SELECT * FROM $table WHERE ".self::$columns['info_columns']['session_id']." = '$_SESSION[Certificate_Code]'",1);
                if ($sql->rowCount() > 0){
                    $row = $sql->fetch(\PDO::FETCH_ASSOC);
                    return $row;
                }
                else{
                    return false;
                }
            }else{
                return false;
            }
        }elseif (!isset($_SESSION['Certificate_Code'])){
            return false;
        }
    }
    public function user_get_permission($check_certificate = 1,$by_email = "",$by_custom = ""){
        $user_tbl = parent::$main_tbl_names['users'];
        if ($check_certificate == 1){
            $user = $this->user_get_certificate();
            $where = $user_tbl.".".self::$columns['info_columns']['session_id']."='".$user[self::$columns['info_columns']['session_id']]."'";
        }
        elseif($by_email != "" AND $check_certificate != 1){
            $user = $by_email;
            $where = $user_tbl.".Email = '".$user."'";
        }
        elseif (empty($by_username) AND is_array($by_custom)){
            $where = $user_tbl.".".$by_custom[0]." = '".$by_custom[1]."'";
        }
        $db = new database();
        $sql = $db->mdb_query("SELECT user_permissions.* FROM user_tbl RIGHT JOIN user_permissions ON user_tbl.Email = user_permissions.Email WHERE $where", 1);
        if ($sql->rowCount() > 0 ){
            self::$permission = $sql->fetch(\PDO::FETCH_ASSOC);
            return self::$permission;
        }else{
            return false;
        }
    }
    
    
    public function user_exist($user_name){
        $preg = new preg();
        $user_tbl = parent::$main_tbl_names['users'];
        $preg_user = $preg->push($user_name,'username');
        if ($preg_user){
            $db = new database();
            $sql = $db->mdb_query("SELECT * FROM $user_tbl WHERE ".self::$columns['info_columns']['username']." = '$user_name'",1);
            if ($sql->rowCount() > 0){
                return true;
            }
            else{
                return false;
            }
        }else{
            return false;
        }
    }
    public function email_exist($email){
        $preg = new preg();
        $user_tbl = parent::$main_tbl_names['users'];
        $preg_user = $preg->push_email($email);
        if ($preg_user){
            $db = new database();
            $sql = $db->mdb_query("SELECT * FROM $user_tbl WHERE ".self::$columns['info_columns']['email']." = '$email'",1);
            if ($sql->rowCount() > 0){
                return true;
            }
            else{
                return false;
            }
        }else{
            return false;
        }
    }

    public function AddUser($data){
        $db = parent::$db;
            $data['Password'] = md5(sha1(md5($data['Password'])));
            $user_tbl = parent::$main_tbl_names['users'];
            $user_permissions = parent::$main_tbl_names['users_permissions'];
            try{
                $db->mdb_query("INSERT INTO $user_permissions (".self::$columns['perm_columns']['email'].") VALUE ('$data[Email]')",1);
                $db->mdb_query("INSERT INTO $user_tbl (".self::$columns['info_columns']['username'].",".self::$columns['info_columns']['email'].",".self::$columns['info_columns']['password'].") VALUE ('$data[Username]','$data[Email]','$data[Password]')",1);
                $dir = DIR_ROOT."/witcher/app/users/".$data['Email'];
                $images_path = DIR_ROOT."/witcher/app/users/".$data['Email']."/images";
                mkdir($dir."/");
                mkdir($images_path."/");
                $drjson = new drjson();
                $drjson->new_json($dir,"followings","{}");
                $drjson->new_json($dir,"followers","{}");
                $drjson->new_json($dir,"follow_requests","{}");
                $drjson->new_json($dir,"block_users","{}");
                $drjson->new_json($dir,"reports","{}");
                return true;
            }catch (\PDOException $e){
                return $e;
            }
    }
    public function CountUsers(){
        $db = parent::$db;
        $table = parent::$main_tbl_names['users'];
        $sql = $db->mdb_query("SELECT * FROM $table",1);
        return $sql->rowCount();
    }
    public function CountUsersBy($column,$value){
        $db = parent::$db;
        $table = parent::$main_tbl_names['users'];
        $sql = $db->mdb_query("SELECT * FROM $table WHERE $column = '$value'",1);
        return $sql->rowCount();
    }
    
    public function CountUsersBy_Permission($permission,$value){
        $db = parent::$db;
        $table = parent::$main_tbl_names['users_permissions'];
        $sql = $db->mdb_query("SELECT * FROM $table WHERE $permission = '$value'",1);
        $row = $sql->fetchAll(\PDO::FETCH_ASSOC);
        return count($row);
    }
    public function SwitchPermission($permission,$statement,$email){
        $db = parent::$db;
        $table = self::$main_tbl_names['users_permissions'];
        $value = $db->mdb_query("SELECT * FROM $table WHERE Email = '$email'",1)->fetch(\PDO::FETCH_ASSOC)[$permission];
        if ($value == 0 ) {
            $newvalue = 1;
        }else {
            $newvalue = 0;
        }
        $db->mdb_query("UPDATE $table SET $permission = '$newvalue' $statement",1);
    }
    public function UpdateRolePermission($email,$new){
        $db = parent::$db;
        $table = new tables();
        $table = $table->MAIN_TABLES['permissions'];
        $db->mdb_query("UPDATE $table SET Role_Id = '$new' WHERE Email = '$email'",1);
    }
    public function UpdateUserTbl($password,$image,$Email,$newEmail = "",$newFull = "",$newusername = ""){
        $db = parent::$db;
        $tbl = parent::$main_tbl_names['users'];
        $custom = "";
        $custom2 = "";
        $custom3 = "";
        if ($newEmail != ""){
            $custom = ", Email = '".$newEmail."'";
        }
        if ($newFull != ""){
            $custom2 = " , Full_Name = '".$newFull."'";
        }
        if ($newusername != ""){
            $custom3 = " , Username = '".$newusername."'";
        }
        $db->mdb_query("UPDATE $tbl SET Password = '$password', Profile_Image = '$image' $custom $custom2 $custom3 WHERE Email = '$Email'",1);
    }
    public function UpdateUserTblCustom($statements){
        $db = parent::$db;
        $table = parent::$main_tbl_names['users'];
        $db->mdb_query("UPDATE $table SET $statements",1);
        return true;
    }
    public function HowCompleteIsProfile($email){
        $db = parent::$db;
        $table = new tables();
        $tbl = [$table->MAIN_TABLES['user'],$table->MAIN_TABLES['permissions']];
        $sql = $db->mdb_query("SELECT * FROM $tbl[0] WHERE Email = '$email'",1);
        $sql2 = $db->mdb_query("SELECT * FROM $tbl[1] WHERE Email = '$email'",1);
        $user_info = $sql->fetch(\PDO::FETCH_ASSOC);
        $user_permissions = $sql2->fetch(\PDO::FETCH_ASSOC);
        $total_info = array_merge($user_info,$user_permissions);
        $percentages_names = ['Active','Invite_Code','Profile_Image','Message'];
        $total = count($percentages_names);
        $i = 0;
        foreach ($percentages_names as $key){
            if (isset($user_info[$key])){
                if (strlen($user_info[$key]) > 0){
                    $i++;
                }
            }
            if (isset($user_permissions[$key])){
                if ($user_permissions[$key] == 1){
                    $i++;
                }
            }
        }
        return ($i * 100) / $total;
    }
    public function UpdateColumn($column,$value,$statement){
        $db = parent::$db;
        $tbl = parent::$main_tbl_names['users'];
        $db->mdb_query("UPDATE $tbl SET $column = '$value' $statement" ,1 );
        return true;
    }
    public function UpdatePermission($column,$value,$email){
        $db = parent::$db;
        $tbl = parent::$main_tbl_names['users_permissions'];
        $db->mdb_query("UPDATE $tbl SET $column = '$value' WHERE Email = '$email'" ,1 );
        return true;
    }
    public function PercentageOfBrowsers($browsers_list){
        $db = parent::$db;
        $tbl = parent::$main_tbl_names['users'];
        $result = [];
        $sql_users = $db->mdb_query("SELECT * FROM $tbl",1);
        $rows = $sql_users->fetchAll(\PDO::FETCH_ASSOC);
        foreach ($browsers_list as $browser){
            $sql = $db->mdb_query("SELECT * FROM $tbl WHERE Last_Browser = '$browser'",1);
            $result[$browser] = $sql->rowCount();
        }
        $last_result = [];
        foreach ($result as $key => $cal){
            $last_result[$key] = 100 * $cal / $sql_users->rowCount();

        }
        return $last_result;
    }
    public function getRoles(){
        $db = parent::$db;
        $tbl = parent::$main_tbl_names['roles'];
        //$table = $table->MAIN_TABLES['Roles'];
        $sql = $db->mdb_query("SELECT * FROM $tbl",1);
        return $sql->fetchAll(\PDO::FETCH_ASSOC);
    }
    public function exist_role($id){
        $db = parent::$db;
        $tbl = parent::$main_tbl_names['roles'];
        //$table = $table->MAIN_TABLES['Roles'];
        $sql = $db->mdb_query("SELECT * FROM $tbl WHERE Role_Id = '$id'",1);
        if ($sql->rowCount() > 0 )
            return true;
        else
            return false;
    }
    public function delete($email){
        $db = parent::$db;
        $tbl = [parent::$main_tbl_names['users'],parent::$main_tbl_names['users_permissions']];
        $db->mdb_query("DELETE FROM $tbl[0] WHERE Email = '$email'",1);
        $db->mdb_query("DELETE FROM $tbl[1] WHERE Email = '$email'",1);
        return true;
    }
}