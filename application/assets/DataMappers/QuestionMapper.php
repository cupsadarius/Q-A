<?php
/**
 * Created by PhpStorm.
 * User: Darius
 * Date: 7/30/14
 * Time: 10:54 AM
 */

namespace application\assets\DataMappers;
use application\assets\Db\DbAdapter;
use application\assets\Entities\Question;
use application\assets\Observers\QuestionObserver;

class QuestionMapper {
    private $_db;
    private $table = '`questions`';
    private $observer;

    public function __construct(DbAdapter $db){
        $this->_db = $db->getDbCon();
        $this->attachObserver(new QuestionObserver());
    }
    public function attachObserver(QuestionObserver $obs){
        $this->observer = $obs;
    }
    public function detachObserver(QuestionObserver $obs){
        $this->observer = null;
    }
    public function convert($data){
        if(empty($data)) return null;
        $question = new Question();
        $userMapper = new UserMapper(DbAdapter::getInstance());
        $sectionMapper = new SectionMapper(DbAdapter::getInstance());
        if(count($data)==1){
            $question->setId($data[0]['id']);
            $question->setUser($userMapper->find(array('id'=>$data[0]['user_id'])));
            $question->setSection($sectionMapper->find(array('id'=>$data[0]['section_id'])));
            $question->setQuestion($data[0]['question']);
            $question->setDescription($data[0]['description']);
            $question->setModifiedDate(new \DateTime($data[0]['modified_date']));
            return $question;
        }else{
            $questions = array();
            for($i = 0; $i<count($data);$i++){
                $question = new Question();
                $question->setId($data[$i]['id']);
                $question->setUser($userMapper->find(array('id'=>$data[$i]['user_id'])));
                $question->setSection($sectionMapper->find(array('id'=>$data[$i]['section_id'])));
                $question->setQuestion($data[$i]['question']);
                $question->setDescription($data[$i]['description']);
                $question->setModifiedDate(new \DateTime($data[$i]['modified_date']));
                array_push($questions,$question);
            }

        }
        return $questions;
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

    public function save(Question $obj){
        $id = $obj->getId();
        if(!empty($id)){
            $sql = "UPDATE $this->table SET user_id=:user_id,section_id=:section_id,question=:question,description=:description,modified_date=:modified_date WHERE  id=:id";
            $cols = array(':id',':user_id',':section_id',':question',':description',':modified_date');
            $vals = array($obj->getId(),$obj->getUser()->getId(),$obj->getSection()->getId(),$obj->getQuestion(),$obj->getDescription(),$obj->getModifiedDate()->format('Y/m/d G:i:s'));
            $this->_db->beginTransaction();
            try{
                $query = $this->prepareStatement($sql,$cols,$vals);
                $query->execute();
                $this->_db->commit();
                $this->notify($obj);
            }catch (\PDOException $e){
                $this->_db->rollBack();
                echo "Exception caught: ".$e->getMessage();
            }
            return true;
        }
        $sql = "INSERT INTO $this->table (`user_id`,`section_id`,`question`,`description`,`modified_date`) VALUES (:user_id, :section_id, :question, :description, :modified_date)";
        $cols = array(':user_id',':section_id',':question',':description',':modified_date');
        $vals = array($obj->getUser()->getId(),$obj->getSection()->getId(),$obj->getQuestion(),$obj->getDescription(),$obj->getModifiedDate()->format('Y/m/d G:i:s'));
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

    public function delete(Question $obj){
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