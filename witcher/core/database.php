<?php
namespace Core;

class database extends configs {
    private $db_kind;
    private $db_username;
    private $db_password;
    private $db_name;
    private $db_host;
    private $db_charset;

    public static $cconn;
    public static $mconn;
    function __construct()
    {
        parent::set_config("database.php");
        parent::set_exceptionsMessages_config("db_connection.php");
        $this->auto_setter();
        self::$cconn = $this->cdb_conn();
        self::$mconn = $this->mdb_conn();
    }

    private function auto_setter($config_type = "default"){
        $config = parent::$configs;
        $config = $config[$config_type];
        $this->db_kind = $config['db_driver'];
        $this->db_host = $config['db_host'];
        $this->db_name = $config['db_name'];
        $this->db_username = $config['db_login'];
        $this->db_password = $config['db_pass'];
        $this->db_charset = $config['db_charset'];
    }
    public function cdb_conn()
    {
        try {
            $conn = new \PDO("mysql:host=$this->db_host;dbname=$this->db_name;charset=$this->db_charset", $this->db_username, $this->db_password);
            $conn->setAttribute(\PDO::ATTR_ERRMODE,\PDO::ERRMODE_EXCEPTION);
            return $conn;
        } catch (\PDOException $e) {
            echo "<p style='text-align: center;margin-top: 10%;font-size: 19px;cursor: none;'>".parent::$exceptionsMessages['connection_failed']."</p>";
            die();
        }
    }
    public function mdb_conn()
    {
        parent::set_config("configDb_tables.php");
        $table = parent::$configs['main_db'];
        $sql = $this->cdb_query("SELECT * FROM $table WHERE active_status = 1",1);
        $row = $sql->fetch(\PDO::FETCH_ASSOC);
        $this->db_host = $row['host'];
        $this->db_name = $row['db_name'];
        $this->db_charset = $row['db_charset'];
        $this->db_username = $row['db_user'];
        $this->db_password = $row['db_pass'];
        try {
            $conn = new \PDO("mysql:host=$this->db_host;dbname=$this->db_name;charset=$this->db_charset", $this->db_username, $this->db_password);
            $conn->setAttribute(\PDO::ATTR_ERRMODE,\PDO::ERRMODE_EXCEPTION);
            return $conn;
        } catch (\PDOException $e) {
            echo "<p style='text-align: center;margin-top: 10%;font-size: 19px;cursor: none;'>".parent::$exceptionsMessages['connection_failed']."</p>";
            die();
        }
    }
    public function cdb_query($query,$execute = 0){
        try{
            $sql = self::$cconn->prepare($query);
            if ($execute == 1){
                $sql->execute();
            }
            return $sql;
        }catch (\PDOException $e){
            die($e);
        }
    }
    public function mdb_query($query,$execute = 0){
        try{
            $sql = self::$mconn->prepare($query);
            if ($execute == 1){
                $sql->execute();
            }
            return $sql;
        }catch (\PDOException $e){
            die($e);
        }
    }
    public function db_conn_custom($array){
        try{
            $conn_custom = new \PDO("mysql:host=$array[hostname];dbname=$array[dbname]",$array['user'],$array['pass']);
            $conn_custom->setAttribute(\PDO::ATTR_ERRMODE,\PDO::ERRMODE_EXCEPTION);
            self::$cconn = $conn_custom;
        }catch (\PDOException $e){
            die(parent::$exceptionsMessages['custom_db_not_found']);
        }
    }

    public function db_charset($charset){
        self::$cconn->exec("SET NAMES ".$charset);
    }
    public function getColumnsName($table){
        try{
            $sql = $this->cdb_query("SELECT `COLUMN_NAME` FROM `INFORMATION_SCHEMA`.`COLUMNS` WHERE `TABLE_SCHEMA`= $this->db_name AND `TABLE_NAME`= $table ",1);
            if ($sql->rowCount() > 0){
                $row = $sql->fetchAll(\PDO::FETCH_ASSOC);
                return $row;
            }else{
                throw new \PDOException(parent::$exceptionsMessages['unknown_table']);
            }
        }catch (\PDOException $e){
            die($e);
        }
    }
}