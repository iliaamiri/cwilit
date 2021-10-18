<?php
namespace Model;

use Core\database;
use Core\model;

class roles extends model{
    private static $columns;
    private static $table;
    function __construct()
    {
        parent::__construct();
        self::$table = parent::$main_tbl_names['role_cat'];
        $witcher = new \witcher();
        self::$columns = $witcher->getModelConfigs("roles_category.php");
    }

    public function getRoles($columns = "*"){
        $db = new database();
        $sql = $db->mdb_query("SELECT $columns FROM ".self::$table,1);
        return $sql->fetchAll(\PDO::FETCH_ASSOC);
    }
    public function getCustomRoles($columns = "*",$statement){ // select * form table $statement
        $db = new database();
        $sql = $db->mdb_query("SELECT $columns FROM ".self::$table." $statement",1);
        return $sql->fetchAll(\PDO::FETCH_ASSOC);
    }
    public function newRole($id,$name,$children_array){
        // NOTE : children array yani majmooe naghsh haayi (roles) ke naghshe jadid (newRole) mitoone access dashte bashe beheshoon ( read or write ) 
    }
    public function deactiveRolesA($array_id){
        $db = new database();
        foreach ($array_id as $role_id){
            $db->mdb_query("UPDATE ".self::$table." SET ".self::$columns['using_stat']." = 0 WHERE ".self::$columns['role_id']." = '".$role_id."'",1);
        }
        return true;
    }
    public function removeRolesA($array_id){
        $db = new database();
        foreach ($array_id as $role_id){
            $db->mdb_query("DELETE FROM ".self::$table." WHERE ".self::$columns['role_id']." = '".$role_id."'",1);
        }
        return true;
    }
    public function getChildrenArray($parent_role_id){
        $db = new database();
        $sql = $db->mdb_query("SELECT ".self::$columns['children_roles']." FROM ".self::$table." WHERE ".self::$columns['role_id']." = '".$parent_role_id."'",1);
        return $sql->fetch(\PDO::FETCH_COLUMN);
    }
    public function isThisItsChild($parent_role_id,$child_role_id){
        $children_of_parent = $this->getChildrenArray($parent_role_id);
        $i = 0;
        foreach ($children_of_parent as $child_id)
        {
            if ($child_id == $child_role_id)
                $i++;
        }
        return $i == 1 ? true : false;
    }
    public function isThisRoleExisted($role_id){
        $db = new database();
        $sql = $db->mdb_query("SELECT * FROM ".self::$table." WHERE ".self::$columns['role_id']." = '".$role_id."'",1);
        return ($sql->rowCount() > 0) ? true : false;
    }
}