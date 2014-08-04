<?php
/**
 * Created by PhpStorm.
 * User: Darius
 * Date: 7/30/14
 * Time: 10:54 AM
 */

namespace application\assets\DataMappers;


use application\assets\Entities\Answer;
use application\assets\Db\DbConnection;
use application\assets\Observers\AnswerObserver;

class AnswerMapper {

    private $_db;
    private $table = '`answers`';
    private $observer;

    public function __construct(DbConnection $db){
        $this->_db = $db->getDbCon();
        $this->attachObserver(new AnswerObserver());
    }

    public function attachObserver(AnswerObserver $obs){
        $this->observer = $obs;
    }
    public function detachObserver(AnswerObserver $obs){
        $this->observer = null;
    }
    public function convert($data){
        if(empty($data)) return null;
        $answer = new Answer();
        $userMapper = new UserMapper(DbConnection::getInstance());
        $questionMapper = new QuestionMapper(DbConnection::getInstance());
        if(count($data)==1){
            $answer->setId($data[0]['id']);
            $answer->setUser($userMapper->find(array('id'=>$data[0]['user_id'])));
            $answer->setQuestion($questionMapper->find(array('id'=>$data[0]['question_id'])));
            $answer->setTitle($data[0]['title']);
            $answer->setAnswer($data[0]['answer']);
            $answer->setRating($data[0]['rating']);
            $answer->setModifiedDate(new \DateTime($data[0]['modified_date']));
            return $answer;
        }else{
            $answers = array();
            for($i = 0; $i<count($data);$i++){
                $answer = new Answer();
                $answer->setId($data[$i]['id']);
                $answer->setUser($userMapper->find(array('id'=>$data[$i]['user_id'])));
                $answer->setQuestion($questionMapper->find(array('id'=>$data[$i]['question_id'])));
                $answer->setTitle($data[$i]['title']);
                $answer->setAnswer($data[$i]['answer']);
                $answer->setRating($data[$i]['rating']);
                $answer->setModifiedDate(new \DateTime($data[$i]['modified_date']));
                array_push($answers,$answer);
            }

        }
        return $answers;
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

    public function save(Answer $obj){
        $id = $obj->getId();
        if(!empty($id)){
            $sql = "UPDATE $this->table SET user_id=:user_id,question_id=:question_id,title=:title,answer=:answer,rating=:rating,modified_date=:modified_date WHERE  id=:id";
            $cols = array(':id',':user_id',':question_id',':title',':answer',':rating',':modified_date');
            $vals = array($obj->getId(),$obj->getUser()->getId(),$obj->getQuestion()->getId(),$obj->getTitle(),$obj->getAnswer(),$obj->getRating(),$obj->getModifiedDate()->format('Y/m/d'));
            $this->_db->beginTransaction();
            try{
                $query = $this->prepareStatement($sql,$cols,$vals);
                $query->execute();
                $this->_db->commit();
                $this->notify($obj->getQuestion());
            }catch (\PDOException $e){
                $this->_db->rollBack();
                echo "Exception caught: ".$e->getMessage();
            }
            return true;
        }
        $sql = "INSERT INTO $this->table (`user_id`,`question_id`,`title`,`answer`,`rating`,`modified_date`) VALUES (:user_id, :question_id, :title, :answer, :rating, :modified_date)";
        $cols = array(':user_id',':question_id',':title',':answer',':rating',':modified_date');
        $vals = array($obj->getUser()->getId(),$obj->getQuestion()->getId(),$obj->getTitle(),$obj->getAnswer(),$obj->getRating(),$obj->getModifiedDate()->format('Y/m/d'));
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

    public function delete(Answer $obj){
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
    public function notify($obj){
        $this->observer->update($obj);
    }

} 