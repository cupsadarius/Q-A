<?php
/**
 * Created by PhpStorm.
 * User: Darius
 * Date: 7/30/14
 * Time: 10:54 AM
 */

namespace application\assets\DataMappers;
use application\assets\Db\DbAdapter;
use application\assets\Entities\User;

class UserMapper {

    private $_db;
    private $table = '`users`';

    public function __construct(DbAdapter $db){
        $this->_db = $db->getDbCon();
    }



    private function convert($data){
        if(empty($data)) return null;
        $user = new User();
        if(count($data) == 1){
            $user->setId($data[0]['id']);
            $user->setFirstName($data[0]['first_name']);
            $user->setLastName($data[0]['last_name']);
            $user->setEmail($data[0]['email']);
            $user->setUsername($data[0]['username']);
            $user->setSalt($data[0]['salt']);
            $user->setPassword($data[0]['password']);
            $user->setHash($data[0]['hash']);
            $user->setBirthday(new \DateTime($data[0]['birthday']));
            return $user;
        } else {
            $users = array();
            for($i = 0;$i < count($data);$i++){
                $user = new User();
                $user->setId($data[$i]['id']);
                $user->setFirstName($data[$i]['first_name']);
                $user->setLastName($data[$i]['last_name']);
                $user->setEmail($data[$i]['email']);
                $user->setUsername($data[$i]['username']);
                $user->setSalt($data[$i]['salt']);
                $user->setPassword($data[$i]['password']);
                $user->setHash($data[$i]['hash']);
                $user->setBirthday(new \DateTime($data[$i]['birthday']));
                array_push($users,$user);
            }
        }

        return $users;

    }

    private function prepareStatement($sql, $cols = null, $vals = null){
        $query = $this->_db->prepare($sql);
        for ($i = 0; $i < count($cols); $i++) {
            $query->bindParam($cols[$i], $vals[$i]);
        }
        return $query;
    }

    public function find(array $conditions = null, $orderby = null,$order = 'DESC', $offset = null, $limit = null){
        $ord = is_null($orderby)?'':"ORDER BY $orderby $order";
        $off = is_null($offset)?'':"OFFSET $offset";
        $lim = is_null($limit)?'':"LIMIT $limit";
        if($conditions == null){
            $query = $this->_db->prepare("SELECT * FROM $this->table $ord $lim $off");
            $query->execute();
            $data = $query->fetchAll(\PDO::FETCH_ASSOC);
        }else{
            $cols = array();
            $vals = array();
            $sql = "SELECT * FROM $this->table WHERE";
            foreach($conditions as $col => $val){
                array_push($cols,":$col");
                array_push($vals,$val);
                $sql .= " $col=:$col AND";
            }
            $sql = substr($sql,0,strlen($sql)-4);
            $sql .= " $ord $lim $off";
            $query = $this->prepareStatement($sql,$cols,$vals);
            $query->execute();
            $data = $query->fetchAll(\PDO::FETCH_ASSOC);
        }
        return $this->convert($data);
    }

    public function save(User $user){
        $id = $user->getId();
        if(!empty($id)){
            $sql = "UPDATE $this->table SET first_name=:first_name, last_name=:last_name,email=:email,username=:username,salt=:salt,password=:password, hash=:hash, birthday=:birthday WHERE id=:id";
            $cols = array(':id',':first_name',':last_name',':email',':username',':salt',':password',':hash',':birthday');
            $vals = array($user->getId(),$user->getFirstName(),$user->getLastName(),$user->getEmail(),$user->getUsername(),$user->getSalt(),$user->getPassword(),$user->getHash(),$user->getBirthday()->format('Y/m/d'));
            $this->_db->beginTransaction();
            try{
                $query = $this->prepareStatement($sql,$cols,$vals);
                $query->execute();
                $this->_db->commit();
            }catch (\PDOException $e){
                $this->_db->rollBack();
                echo "Exception caught: ".$e->getMessage();
            }
            return true;
        }
        $sql = "INSERT INTO $this->table (`first_name`, `last_name`, `email`, `username`, `salt`, `password`,`hash`, `birthday`) VALUES (:first_name, :last_name, :email, :username, :salt, :password, :hash, :birthday)";
        $cols = array(':first_name',':last_name',':email',':username',':salt',':password',':hash',':birthday');
        $vals = array($user->getFirstName(),$user->getLastName(),$user->getEmail(),$user->getUsername(),$user->getSalt(),$user->getPassword(),$user->getHash(),$user->getBirthday()->format('Y/m/d'));
        $this->_db->beginTransaction();
        try{
            $query = $this->prepareStatement($sql,$cols,$vals);
            $query->execute();
            $this->_db->commit();
        }catch (\PDOException $e){
            $this->_db->rollBack();
            echo "Exception caught: ".$e->getMessage();
        }
        return true;
    }



    public function delete(User $user){
        $this->_db->beginTransaction();
        $uid = $user->getId();
        try{
            $query = $this->_db->prepare("DELETE FROM $this->table WHERE id=$uid");
            $query->execute();
            $this->_db->commit();
        }catch (\PDOException $e){
            $this->_db->rollBack();
            echo "Exception caught: ".$e->getMessage();
        }

    }
} 