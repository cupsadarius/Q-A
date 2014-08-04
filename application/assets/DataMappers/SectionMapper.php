<?php


namespace application\assets\DataMappers;

use application\assets\Db\DbConnection;
use application\assets\Entities\Section;

class SectionMapper {

    private $_db;
    private $table = '`sections`';

    public function __construct(DbConnection $db){
        $this->_db = $db->getDbCon();
    }

    public function convert($data){
        if(empty($data)) return null;
        $section = new Section();
        if(count($data) == 1){
            $section->setId($data[0]['id']);
            $section->setName($data[0]['name']);
            $section->setTag($data[0]['tag']);
            return $section;
        } else {
            $sections = array();
            for($i = 0; $i < count($data); $i++){
                $section = new Section();
                $section->setId($data[$i]['id']);
                $section->setName($data[$i]['name']);
                $section->setTag($data[$i]['tag']);
                array_push($sections,$section);
            }
        }
        return $sections;
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
            $sql .=" $ord $lim $off";
            $query = $this->prepareStatement($sql,$cols,$vals);
            $query->execute();
            $data = $query->fetchAll(\PDO::FETCH_ASSOC);
        }
        return $this->convert($data);
    }

    public function save(Section $obj){
        $id = $obj->getId();
        if(!empty($id)){
            $sql = "UPDATE $this->table SET name=:name, tag=:tag WHERE id=:id";
            $cols = array(':id',':name',':tag');
            $vals = array($obj->getId(),$obj->getName(),$obj->getTag());
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
        $sql = "INSERT INTO $this->table (`name`, `tag`) VALUES(:name, :tag)";
        $cols = array(':name',':tag');
        $vals = array($obj->getName(),$obj->getTag());
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

    public function delete(Section $obj){
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