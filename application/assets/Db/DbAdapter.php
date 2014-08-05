<?php
/**
 * Created by PhpStorm.
 * User: Darius
 * Date: 8/5/14
 * Time: 11:21 AM
 */

namespace application\assets\Db;


class DbAdapter {

    private static $_instance;
    private $_db;

    public static function getInstance($obj = null){
        if(self::$_instance === null){
            $user = $obj->getUser();
            $password = $obj->getPassword();
            $dsn = $obj->getType().':host='.$obj->getHost().';dbname='.$obj->getDb();
            self::$_instance = new self($dsn, $user, $password);
        }
        return self::$_instance;
    }

    private function __construct($dsn,$user,$password){
        $this->_db = new \PDO($dsn,$user,$password);
        $this->_db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $this->_db->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);
    }
    public function getDbCon(){
        return $this->_db;
    }

    public function __destruct(){
        self::$_instance = null;
        $this->_db = null;
    }

    public function __clone(){}


} 