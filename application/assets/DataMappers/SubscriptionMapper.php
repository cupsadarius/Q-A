<?php
/**
 * Created by PhpStorm.
 * User: Darius
 * Date: 7/30/14
 * Time: 10:54 AM
 */

namespace application\assets\DataMappers;
use application\assets\Db\DbConnection;
use application\assets\Entities\Subscription;
class SubscriptionMapper {

    private $_db;
    private $table = '`subscription`';

    public function __construct(DbConnection $db){
        $this->_db = $db->getDbCon();
    }

    public function convert($data){
        if(empty($data)) return null;
        $subscription = new Subscription();
        $userMapper = new UserMapper(DbConnection::getInstance());
        $questionMapper = new QuestionMapper(DbConnection::getInstance());
        if(count($data) == 1){
            $subscription->setId($data[0]['id']);
            $subscription->setUser($userMapper->find(array('id'=>$data[0]['user_id'])));
            $subscription->setQuestion($questionMapper->find(array('id'=>$data[0]['question_id'])));
            return $subscription;
        } else {
            $subscriptions = array();
            for($i = 0; $i < count($data); $i++){
                $subscription = new Subscription();
                $subscription->setId($data[$i]['id']);
                $subscription->setUser($userMapper->find(array('id'=>$data[$i]['user_id'])));
                $subscription->setQuestion($questionMapper->find(array('id'=>$data[$i]['question_id'])));
                array_push($subscriptions,$subscription);
            }

        }
        return $subscriptions;
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

    public function save(Subscription $obj){
        $id = $obj->getId();
        if(!empty($id)){
            $sql = "UPDATE $this->table SET user_id=:user_id, question_id=:question_id WHERE  id=:id";
            $cols = array(':id',':user_id',':question_id');
            $vals = array($obj->getId(),$obj->getUser()->getId(),$obj->getQuestion()->getId());
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
        $sql = "INSERT INTO $this->table (`user_id`, `question_id`) VALUES(:user_id, :question_id)";
        $cols = array(':user_id',':question_id');
        $vals = array($obj->getUser()->getId(),$obj->getQuestion()->getId());
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

    public function delete(Subscription $obj){
        $this->_db->beginTransaction();
        $id = $obj->getId();
        try{
            $query = $this->_db->prepare("DELETE FROM $this->table WHERE id=$id");
            $query->execute();
            $this->_db->commit();
        }catch (\PDOException $e){
            $this->_db->rollBack();
            echo "Exception caught: ".$e->getMessage();
        }
    }
} 