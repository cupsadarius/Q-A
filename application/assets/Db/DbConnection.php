<?php
namespace application\assets\Db;


class DbConnection {

    private static $_instance;
    private $_db;

    public static function getInstance(){
        if(self::$_instance === null){
            $user = 'qanda';
            $password = 'axeqw';
            $dsn = 'mysql:host=localhost;dbname=qanda';
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

