<?php
/**
 * Created by PhpStorm.
 * User: Darius
 * Date: 8/1/14
 * Time: 11:35 AM
 */

namespace application\assets\Routes;


class Constrains {

    private $conditions = array();

    public function register($name,$condition){
        array_push($this->conditions,array('name'=>$name,'condition'=>$condition));
        return true;
    }
    public function getCondition($name){
        foreach($this->conditions as $condition){
            if($condition['name'] == $name){
                return $condition;
            }
        }
        return false;
    }
} 